<?php
header('Content-Type: application/json');
require_once '../database.php';
require_once '../remove-bg.php';

$ip_address = $_SERVER['REMOTE_ADDR'];
$conn = getDBConnection();

// Rate limiting
$rate_limit_stmt = $conn->prepare("SELECT request_count, last_request FROM ip_rate_limit WHERE ip_address = ?");
$rate_limit_stmt->bind_param("s", $ip_address);
$rate_limit_stmt->execute();
$result = $rate_limit_stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $last_request = strtotime($row['last_request']);
    $current_time = time();
    $time_diff = $current_time - $last_request;

    if ($time_diff < 3600 && $row['request_count'] >= 100) { // 100 requests per hour
        http_response_code(429);
        echo json_encode(['success' => false, 'message' => 'Too Many Requests']);
        exit;
    }

    if ($time_diff >= 3600) {
        $update_stmt = $conn->prepare("UPDATE ip_rate_limit SET request_count = 1, last_request = CURRENT_TIMESTAMP WHERE ip_address = ?");
        $update_stmt->bind_param("s", $ip_address);
        $update_stmt->execute();
    } else {
        $update_stmt = $conn->prepare("UPDATE ip_rate_limit SET request_count = request_count + 1 WHERE ip_address = ?");
        $update_stmt->bind_param("s", $ip_address);
        $update_stmt->execute();
    }
} else {
    $insert_stmt = $conn->prepare("INSERT INTO ip_rate_limit (ip_address, request_count) VALUES (?, 1)");
    $insert_stmt->bind_param("s", $ip_address);
    $insert_stmt->execute();
}
$rate_limit_stmt->close();


$apiKey = $_POST['api_key'] ?? '';

if (empty($apiKey)) {
    echo json_encode(['success' => false, 'message' => 'API key is missing.']);
    exit;
}

$conn = getDBConnection();
$stmt = $conn->prepare("SELECT id FROM users WHERE api_key = ?");
$stmt->bind_param("s", $apiKey);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (isset($_FILES['image'])) {
        $file = $_FILES['image'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileType = $file['type'];

        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));

        $allowed = array('jpg', 'jpeg', 'png');

        if (in_array($fileActualExt, $allowed)) {
            if ($fileError === 0) {
                if ($fileSize < 5000000) { // 5MB
                    $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                    $fileDestination = '../uploads/' . $fileNameNew;
                    move_uploaded_file($fileTmpName, $fileDestination);

                    $processedFileName = '../processed/' . uniqid('', true) . '.png';

                    if (removeBackground($fileDestination, $processedFileName)) {
                        $imageNameOnly = basename($processedFileName);
                        $status = 'success';
                        $stmt = $conn->prepare("INSERT INTO api_usage (user_id, image_name, status) VALUES (?, ?, ?)");
                        $stmt->bind_param("iss", $user['id'], $imageNameOnly, $status);
                        $stmt->execute();

                        echo json_encode([
                            'success' => true,
                            'processed_image_url' => 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/' . $processedFileName
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to remove background.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Your file is too big!']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'There was an error uploading your file!']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'You cannot upload files of this type!']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid API key.']);
}

$stmt->close();
$conn->close();
?>
