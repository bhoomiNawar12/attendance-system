<?php
declare(strict_types=1);

/**
 * Save attendance for a whole class — teacher only.
 */

session_start();

require_once __DIR__ . '/../config/database.php';

const MARK_ATTENDANCE_PAGE = '../../pages/teacher/mark-attendance.php';

function redirectWithAttendanceErrors(array $errors, int $classId, string $date): void
{
    $_SESSION['attendance_errors'] = $errors;
    header('Location: ' . MARK_ATTENDANCE_PAGE . '?class_id=' . $classId . '&date=' . urlencode($date));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . MARK_ATTENDANCE_PAGE);
    exit;
}

if (!isset($_SESSION['UserID'], $_SESSION['Role']) || $_SESSION['Role'] !== 'Teacher') {
    header('Location: /attendance-system/pages/auth/login.php');
    exit;
}

$teacherId = (int) $_SESSION['UserID'];
$classId = (int) ($_POST['class_id'] ?? 0);
$attendanceDate = trim((string) ($_POST['attendance_date'] ?? ''));
$statuses = $_POST['status'] ?? [];

if ($classId <= 0) {
    redirectWithAttendanceErrors(['Please select a valid class.'], 0, $attendanceDate);
}

$dateObj = DateTime::createFromFormat('Y-m-d', $attendanceDate);
if (!$dateObj || $dateObj->format('Y-m-d') !== $attendanceDate) {
    redirectWithAttendanceErrors(['Please enter a valid date.'], $classId, $attendanceDate);
}

if (!is_array($statuses) || empty($statuses)) {
    redirectWithAttendanceErrors(['No attendance data received.'], $classId, $attendanceDate);
}

try {
    $db = getDBConnection();

    $classStmt = $db->prepare(
        'SELECT ClassID, ClassName FROM classes WHERE ClassID = ? AND TeacherID = ? LIMIT 1'
    );
    $classStmt->bind_param('ii', $classId, $teacherId);
    $classStmt->execute();
    $class = $classStmt->get_result()->fetch_assoc();
    $classStmt->close();

    if (!$class) {
        redirectWithAttendanceErrors(['You do not have permission to mark this class.'], $classId, $attendanceDate);
    }

    $studentStmt = $db->prepare(
        'SELECT u.UserID
         FROM enrollments e
         INNER JOIN users u ON u.UserID = e.StudentID
         WHERE e.ClassID = ?
         ORDER BY u.Name ASC'
    );
    $studentStmt->bind_param('i', $classId);
    $studentStmt->execute();
    $result = $studentStmt->get_result();
    $enrolledIds = [];
    while ($row = $result->fetch_assoc()) {
        $enrolledIds[] = (int) $row['UserID'];
    }
    $studentStmt->close();

    if (empty($enrolledIds)) {
        redirectWithAttendanceErrors(['This class has no enrolled students.'], $classId, $attendanceDate);
    }

    $checkStmt = $db->prepare(
        'SELECT COUNT(*) AS total FROM attendance WHERE ClassID = ? AND AttendanceDate = ?'
    );
    $checkStmt->bind_param('is', $classId, $attendanceDate);
    $checkStmt->execute();
    $existingCount = (int) $checkStmt->get_result()->fetch_assoc()['total'];
    $checkStmt->close();

    if ($existingCount > 0) {
        redirectWithAttendanceErrors(
            ['Attendance for this class on ' . $attendanceDate . ' has already been recorded.'],
            $classId,
            $attendanceDate
        );
    }

    $allowedStatuses = ['Present', 'Absent'];
    $records = [];

    foreach ($enrolledIds as $studentId) {
        if (!isset($statuses[$studentId])) {
            redirectWithAttendanceErrors(
                ['Please mark attendance for every student.'],
                $classId,
                $attendanceDate
            );
        }

        $status = (string) $statuses[$studentId];
        if (!in_array($status, $allowedStatuses, true)) {
            redirectWithAttendanceErrors(
                ['Invalid attendance status for one or more students.'],
                $classId,
                $attendanceDate
            );
        }

        $records[] = ['student_id' => $studentId, 'status' => $status];
    }

    $insertStmt = $db->prepare(
        'INSERT INTO attendance (StudentID, ClassID, AttendanceDate, Status)
         VALUES (?, ?, ?, ?)'
    );

    $db->begin_transaction();

    foreach ($records as $record) {
        $studentId = $record['student_id'];
        $status = $record['status'];
        $insertStmt->bind_param('iiss', $studentId, $classId, $attendanceDate, $status);
        $insertStmt->execute();
    }

    $insertStmt->close();
    $db->commit();

    $_SESSION['attendance_success'] = 'Attendance saved for "' . $class['ClassName'] . '" on ' . $attendanceDate . '.';

    header('Location: ' . MARK_ATTENDANCE_PAGE . '?class_id=' . $classId . '&date=' . urlencode($attendanceDate));
    exit;
} catch (mysqli_sql_exception $e) {
    if (isset($db) && $db instanceof mysqli) {
        $db->rollback();
    }
    error_log('Save attendance DB error: ' . $e->getMessage());
    redirectWithAttendanceErrors(
        ['Could not save attendance. Please try again.'],
        $classId,
        $attendanceDate
    );
}
