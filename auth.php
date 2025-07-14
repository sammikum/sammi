<?php
session_start();
require_once 'database.php';
require_once 'auth_fns.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'register':
        registerUser();
        break;
    case 'login':
        loginUser();
        break;
    case 'logout':
        logoutUser();
        break;
    case 'regenerate_api_key':
        regenerateApiKey();
        break;
    default:
        header('Location: index.php');
        exit;
}
?>
