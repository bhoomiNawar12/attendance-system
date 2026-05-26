<?php
/**
 * Login handler — validates credentials and starts user session.
 */
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/database.php';

/** Relative URLs from php/auth/ */
const LOGIN_PAGE = '../../pages/auth/login.php';
const TEACHER_DASHBOARD = '../../pages/teacher/dashboard.php';
const STUDENT_DASHBOARD = '../../pages/student/dashboard.php';

/**
 * Save flash data and return to the login form.
 */
function redirectWithLoginErrors(array $errors, array $old = []): void
{
    $_SESSION['login_errors'] = $errors;
    $_SESSION['login_old'] = $old;
    header('Location: ' . LOGIN_PAGE);
    exit;
}

// Only accept form submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . LOGIN_PAGE);
    exit;
}

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

$errors = [];
$old = ['email' => $email];

if ($email === '') {
    $errors[] = 'Email is required.';
}

if ($password === '') {
    $errors[] = 'Password is required.';
}

if (!empty($errors)) {
    redirectWithLoginErrors($errors, $old);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithLoginErrors(['Please enter a valid email address.'], $old);
}

try {
    $db = getDBConnection();

    $stmt = $db->prepare(
        'SELECT UserID, Name, Email, Password, Role FROM users WHERE Email = ? LIMIT 1'
    );
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        redirectWithLoginErrors(
            ['Invalid email. No account found with this address.'],
            $old
        );
    }

    if (!password_verify($password, $user['Password'])) {
        redirectWithLoginErrors(
            ['Wrong password. Please try again.'],
            $old
        );
    }

    session_regenerate_id(true);

    $_SESSION['UserID'] = (int) $user['UserID'];
    $_SESSION['Name'] = $user['Name'];
    $_SESSION['Role'] = $user['Role'];

    unset($_SESSION['login_errors'], $_SESSION['login_old']);

    if ($user['Role'] === 'Teacher') {
        header('Location: ' . TEACHER_DASHBOARD);
        exit;
    }

    header('Location: ' . STUDENT_DASHBOARD);
    exit;
} catch (mysqli_sql_exception $e) {
    error_log('Login DB error: ' . $e->getMessage());
    redirectWithLoginErrors(
        ['Login failed due to a server error. Please try again later.'],
        $old
    );
} catch (RuntimeException $e) {
    error_log('Login connection error: ' . $e->getMessage());
    redirectWithLoginErrors(
        ['Could not connect to the database. Ensure MySQL is running and the eduattend database exists.'],
        $old
    );
}
