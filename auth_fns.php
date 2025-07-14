<?php
function registerUser() {
    $conn = getDBConnection();
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        header('Location: register.php?error=empty_fields');
        return;
    }

    if (!$email) {
        header('Location: register.php?error=invalid_email');
        return;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $api_key = bin2hex(random_bytes(16));

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, api_key) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $api_key);

    if ($stmt->execute()) {
        header('Location: login.php?success=registration_complete');
    } else {
        header('Location: register.php?error=registration_failed');
    }

    $stmt->close();
    $conn->close();
}

function loginUser() {
    $conn = getDBConnection();
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        header('Location: login.php?error=empty_fields');
        return;
    }

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            header('Location: dashboard.php');
        } else {
            header('Location: login.php?error=invalid_credentials');
        }
    } else {
        header('Location: login.php?error=invalid_credentials');
    }

    $stmt->close();
    $conn->close();
}

function logoutUser() {
    session_unset();
    session_destroy();
    header('Location: index.php');
}

function regenerateApiKey() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        return;
    }

    $conn = getDBConnection();
    $new_api_key = bin2hex(random_bytes(16));

    $stmt = $conn->prepare("UPDATE users SET api_key = ? WHERE id = ?");
    $stmt->bind_param("si", $new_api_key, $_SESSION['user_id']);

    if ($stmt->execute()) {
        header('Location: dashboard.php?success=api_key_regenerated');
    } else {
        header('Location: dashboard.php?error=api_key_regeneration_failed');
    }

    $stmt->close();
    $conn->close();
}
?>
