<?php
declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

/**
 * Registration handler — validates input and inserts into users table.
 */

session_start();

require_once __DIR__ . '/../config/database.php';

/** Relative URLs from php/auth/ */
const REGISTER_PAGE = '../../pages/auth/register.php';
const LOGIN_PAGE = '../../pages/auth/login.php?registered=1';

/**
 * Save flash data and return to the registration form.
 */
function redirectWithErrors(array $errors, array $old = []): void
{
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_old'] = $old;
    header('Location: ' . REGISTER_PAGE);
    exit;
}

// Only accept form submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . REGISTER_PAGE);
    exit;
}

$name = trim((string) ($_POST['full_name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$confirmPassword = (string) ($_POST['confirm_password'] ?? '');
$roleInput = strtolower(trim((string) ($_POST['role'] ?? '')));

$errors = [];
$old = [
    'full_name' => $name,
    'email' => $email,
    'role' => $roleInput,
];

// --- Validation ---
if ($name === '') {
    $errors[] = 'Full name is required.';
}

if ($email === '') {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if ($password === '') {
    $errors[] = 'Password is required.';
} elseif (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters.';
}

if ($confirmPassword === '') {
    $errors[] = 'Please confirm your password.';
} elseif ($password !== $confirmPassword) {
    $errors[] = 'Passwords do not match.';
}

$role = null;
if ($roleInput === 'student') {
    $role = 'Student';
} elseif ($roleInput === 'teacher') {
    $role = 'Teacher';
} else {
    $errors[] = 'Please select a valid role (Student or Teacher).';
}

if (!empty($errors)) {
    redirectWithErrors($errors, $old);
}

// --- Database insert ---
try {
    $db = getDBConnection();

    $checkStmt = $db->prepare('SELECT UserID FROM users WHERE Email = ? LIMIT 1');
    $checkStmt->bind_param('s', $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $checkStmt->close();
        redirectWithErrors(['This email is already registered. Please login or use another email.'], $old);
    }
    $checkStmt->close();

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $insertStmt = $db->prepare(
        'INSERT INTO users (Name, Email, Password, Role) VALUES (?, ?, ?, ?)'
    );

    if (!$insertStmt) {
        die('Prepare failed: ' . $db->error);
    }

    $insertStmt->bind_param('ssss', $name, $email, $passwordHash, $role);
    $insertStmt->execute();
    $insertStmt->close();

    unset($_SESSION['register_errors'], $_SESSION['register_old']);

    header('Location: ' . LOGIN_PAGE);
    exit;

} catch (mysqli_sql_exception $e) {

    die('MySQL Error: ' . $e->getMessage());

} catch (RuntimeException $e) {

    die('Runtime Error: ' . $e->getMessage());

}