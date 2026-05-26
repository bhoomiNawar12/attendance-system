<?php
declare(strict_types=1);

/**
 * Session guard — include at the top of protected pages.
 * Set $requiredRole to 'Teacher' or 'Student' before including this file.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const LOGIN_PAGE = '/attendance-system/pages/auth/login.php';
const TEACHER_DASHBOARD = '/attendance-system/pages/teacher/dashboard.php';
const STUDENT_DASHBOARD = '/attendance-system/pages/student/dashboard.php';

if (!isset($_SESSION['UserID'])) {
    header('Location: ' . LOGIN_PAGE);
    exit;
}

if (isset($requiredRole) && $_SESSION['Role'] !== $requiredRole) {
    if ($_SESSION['Role'] === 'Teacher') {
        header('Location: ' . TEACHER_DASHBOARD);
    } else {
        header('Location: ' . STUDENT_DASHBOARD);
    }
    exit;
}
