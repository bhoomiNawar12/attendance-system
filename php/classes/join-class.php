<?php
declare(strict_types=1);

/**
 * Join class — student only.
 */

session_start();

require_once __DIR__ . '/../config/database.php';

const STUDENT_DASHBOARD = '../../pages/student/dashboard.php';

function redirectWithJoinErrors(array $errors, array $old = []): void
{
    $_SESSION['join_errors'] = $errors;
    $_SESSION['join_old'] = $old;
    header('Location: ' . STUDENT_DASHBOARD);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . STUDENT_DASHBOARD);
    exit;
}

if (!isset($_SESSION['UserID'], $_SESSION['Role']) || $_SESSION['Role'] !== 'Student') {
    header('Location: /attendance-system/pages/auth/login.php');
    exit;
}

$classCode = strtoupper(trim((string) ($_POST['class_code'] ?? '')));
$studentId = (int) $_SESSION['UserID'];
$old = ['class_code' => $classCode];

if ($classCode === '') {
    redirectWithJoinErrors(['Please enter a class code.'], $old);
}

try {
    $db = getDBConnection();

    $stmt = $db->prepare(
        'SELECT ClassID, ClassName FROM classes WHERE ClassCode = ? LIMIT 1'
    );
    $stmt->bind_param('s', $classCode);
    $stmt->execute();
    $class = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$class) {
        redirectWithJoinErrors(['Invalid class code. No class found with that code.'], $old);
    }

    $classId = (int) $class['ClassID'];

    $checkStmt = $db->prepare(
        'SELECT EnrollmentID FROM enrollments WHERE StudentID = ? AND ClassID = ? LIMIT 1'
    );
    $checkStmt->bind_param('ii', $studentId, $classId);
    $checkStmt->execute();
    $existing = $checkStmt->get_result()->fetch_assoc();
    $checkStmt->close();

    if ($existing) {
        redirectWithJoinErrors(
            ['You are already enrolled in "' . $class['ClassName'] . '".'],
            $old
        );
    }

    $insertStmt = $db->prepare(
        'INSERT INTO enrollments (StudentID, ClassID) VALUES (?, ?)'
    );
    $insertStmt->bind_param('ii', $studentId, $classId);
    $insertStmt->execute();
    $insertStmt->close();

    unset($_SESSION['join_errors'], $_SESSION['join_old']);
    $_SESSION['join_success'] = 'You joined "' . $class['ClassName'] . '" successfully.';

    header('Location: ' . STUDENT_DASHBOARD);
    exit;
} catch (mysqli_sql_exception $e) {
    error_log('Join class DB error: ' . $e->getMessage());
    redirectWithJoinErrors(
        ['Could not join class. Please try again.'],
        $old
    );
}
