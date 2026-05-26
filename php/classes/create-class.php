<?php
declare(strict_types=1);

/**
 * Create class — teacher only.
 */

session_start();

require_once __DIR__ . '/../config/database.php';

const CREATE_CLASS_PAGE = '../../pages/teacher/create-class.php';
const TEACHER_DASHBOARD = '../../pages/teacher/dashboard.php';

function redirectWithClassErrors(array $errors, array $old = []): void
{
    $_SESSION['class_errors'] = $errors;
    $_SESSION['class_old'] = $old;
    header('Location: ' . CREATE_CLASS_PAGE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . CREATE_CLASS_PAGE);
    exit;
}

if (!isset($_SESSION['UserID'], $_SESSION['Role']) || $_SESSION['Role'] !== 'Teacher') {
    header('Location: /attendance-system/pages/auth/login.php');
    exit;
}

$className = trim((string) ($_POST['class_name'] ?? ''));
$teacherId = (int) $_SESSION['UserID'];
$old = ['class_name' => $className];

if ($className === '') {
    redirectWithClassErrors(['Class name is required.'], $old);
}

if (strlen($className) > 150) {
    redirectWithClassErrors(['Class name must be 150 characters or less.'], $old);
}

/**
 * Generate a unique class code (e.g. EDU-A1B2C3).
 */
function generateUniqueClassCode(mysqli $db): string
{
    $maxAttempts = 10;

    for ($i = 0; $i < $maxAttempts; $i++) {
        $code = 'EDU-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        $stmt = $db->prepare('SELECT ClassID FROM classes WHERE ClassCode = ? LIMIT 1');
        $stmt->bind_param('s', $code);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$exists) {
            return $code;
        }
    }

    throw new RuntimeException('Could not generate a unique class code.');
}

try {
    $db = getDBConnection();
    $classCode = generateUniqueClassCode($db);

    $stmt = $db->prepare(
        'INSERT INTO classes (ClassName, ClassCode, TeacherID, CreateDate)
         VALUES (?, ?, ?, NOW())'
    );
    $stmt->bind_param('ssi', $className, $classCode, $teacherId);
    $stmt->execute();
    $stmt->close();

    unset($_SESSION['class_errors'], $_SESSION['class_old']);
    $_SESSION['class_success'] = 'Class created successfully. Share code: ' . $classCode;

    header('Location: ' . TEACHER_DASHBOARD);
    exit;
} catch (mysqli_sql_exception $e) {
    error_log('Create class DB error: ' . $e->getMessage());
    redirectWithClassErrors(
        ['Could not create class. Please try again.'],
        $old
    );
} catch (RuntimeException $e) {
    error_log('Create class error: ' . $e->getMessage());
    redirectWithClassErrors(
        ['Could not generate a class code. Please try again.'],
        $old
    );
}
