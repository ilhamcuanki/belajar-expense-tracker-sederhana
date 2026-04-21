<?php
// config.php
error_reporting(E_ALL);
ini_set('display_errors', 1); // Matikan di production
date_default_timezone_set('Asia/Jakarta');
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'db_expense_tracker');
define('DB_USER', 'root');
define('DB_PASS', '');
define('APP_URL', 'http://localhost/expense-tracker');