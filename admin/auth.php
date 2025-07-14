<?php
session_start();
require_once '../database.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        loginAdmin();
        break;
    case 'logout':
        logoutAdmin();
        break;
    default:
        header('Location: index.php');
        exit;
}

function loginAdmin() {
    $conn = getDBConnection();
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        header('Location: index.php?error=empty_fields');
        exit;
    }

    $stmt = $conn->prepare("SELECT id, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $username;
            header('Location: dashboard.php');
        } else {
            header('Location: index.php?error=invalid_credentials');
        }
    } else {
        header('Location: index.php?error=invalid_credentials');
    }

    $stmt->close();
    $conn->close();
}

function logoutAdmin() {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
