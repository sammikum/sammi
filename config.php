<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'password');

if (defined('PHPUNIT_TEST_SUITE')) {
    define('DB_NAME', 'bg_remover_test');
} else {
    define('DB_NAME', 'bg_remover');
}
?>
