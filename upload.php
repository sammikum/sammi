<?php
session_start();
require_once 'database.php';

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
                $fileDestination = 'uploads/' . $fileNameNew;
                move_uploaded_file($fileTmpName, $fileDestination);

                require_once 'remove-bg.php';
                $processedFileName = 'processed/' . uniqid('', true) . '.png';

                if (removeBackground($fileDestination, $processedFileName)) {
                    if (isset($_SESSION['user_id'])) {
                        $conn = getDBConnection();
                        $stmt = $conn->prepare("INSERT INTO api_usage (user_id, image_name, status) VALUES (?, ?, ?)");
                        $imageNameOnly = basename($processedFileName);
                        $status = 'success';
                        $stmt->bind_param("iss", $_SESSION['user_id'], $imageNameOnly, $status);
                        $stmt->execute();
                        $stmt->close();
                        $conn->close();
                    }
                    echo json_encode([
                        'success' => true,
                        'original' => $fileDestination,
                        'processed' => $processedFileName
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
?>
