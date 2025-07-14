<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start a session for testing
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('PHPUNIT_TEST_SUITE', true);
require_once dirname(__FILE__) . '/../database.php';
require_once dirname(__FILE__) . '/../auth_fns.php';

// Mock the header function
if (!function_exists('header')) {
    function header($location) {
        // Do nothing
    }
}

function test_registerUser() {
    // Test successful registration
    $_POST['username'] = 'testuser';
    $_POST['email'] = 'test@example.com';
    $_POST['password'] = 'password';
    registerUser();

    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = 'testuser'");
    $stmt->execute();
    $result = $stmt->get_result();
    assert($result->num_rows === 1, 'User should be registered');
    $conn->query("DELETE FROM users WHERE username = 'testuser'");
    echo "Test successful registration passed.\n";

    // Test registration with empty fields
    $_POST['username'] = '';
    $_POST['email'] = '';
    $_POST['password'] = '';
    registerUser();
    // No assertion here, just checking that it doesn't crash
    echo "Test registration with empty fields passed.\n";

    // Test registration with invalid email
    $_POST['username'] = 'testuser2';
    $_POST['email'] = 'invalid-email';
    $_POST['password'] = 'password';
    registerUser();
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = 'testuser2'");
    $stmt->execute();
    $result = $stmt->get_result();
    assert($result->num_rows === 0, 'User with invalid email should not be registered');
    echo "Test registration with invalid email passed.\n";

    $conn->close();
}

function test_loginUser() {
    $conn = getDBConnection();
    // Create a user to test login
    $username = 'testloginuser';
    $password = 'password';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $api_key = bin2hex(random_bytes(16));
    $email = 'testlogin@example.com';
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, api_key) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $api_key);
    $stmt->execute();

    // Test successful login
    $_POST['username'] = $username;
    $_POST['password'] = $password;
    loginUser();
    assert($_SESSION['username'] === $username, 'Session username should be set on successful login');
    echo "Test successful login passed.\n";

    // Test login with invalid credentials
    session_unset();
    $_POST['username'] = $username;
    $_POST['password'] = 'wrongpassword';
    loginUser();
    assert(!isset($_SESSION['user_id']), 'Session should not be set on failed login');
    echo "Test login with invalid credentials passed.\n";

    // Clean up
    $conn->query("DELETE FROM users WHERE username = '$username'");
    $conn->close();
}


test_registerUser();
test_loginUser();
?>
