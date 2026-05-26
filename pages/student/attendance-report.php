<?php
session_start();

if (!isset($_SESSION['UserID'])) {
    header('Location: ../../pages/auth/login.html');
    exit;
}

if (!in_array($_SESSION['Role'], ['Student', 'Teacher'])) {
    header('Location: ../../pages/auth/login.html');
    exit;
}

require_once __DIR__ . '/../../php/config/database.php';

$userName = htmlspecialchars((string) $_SESSION['Name'], ENT_QUOTES, 'UTF-8');
$userId = (int) $_SESSION['UserID'];
$userRole = (string) $_SESSION['Role'];

$filterClassId = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;

$enrolledClasses = [];
$attendanceRecords = [];

try {
    $db = getDBConnection();

    $classStmt = $db->prepare(
        'SELECT c.ClassID, c.ClassName, c.ClassCode
         FROM enrollments e
         INNER JOIN classes c ON c.ClassID = e.ClassID
         WHERE e.StudentID = ?
         ORDER BY c.ClassName ASC'
    );
   $classStmt->bind_param('i', $userId);
    $classStmt->execute();
    $classResult = $classStmt->get_result();
    while ($row = $classResult->fetch_assoc()) {
        $enrolledClasses[] = $row;
    }
    $classStmt->close();

$recordStmt = $db->prepare(

    'SELECT
        u.Name AS StudentName,
        c.ClassName,

        ROUND(
            (
                SUM(CASE WHEN a.Status = "Present" THEN 1 ELSE 0 END)
                /
                NULLIF(COUNT(a.AttendanceID), 0)
            ) * 100
        ) AS AttendancePercentage

     FROM attendance a

     INNER JOIN users u
     ON u.UserID = a.StudentID

     INNER JOIN classes c
     ON c.ClassID = a.ClassID

     WHERE a.StudentID = ?

     AND MONTH(a.AttendanceDate) = MONTH(CURRENT_DATE())

     AND YEAR(a.AttendanceDate) = YEAR(CURRENT_DATE())

     GROUP BY c.ClassID

     ORDER BY c.ClassName ASC'
);

$recordStmt->bind_param('i', $userId);

    $recordStmt->execute();
    $recordResult = $recordStmt->get_result();
    $attendanceMatrix = [];
$subjects = [];

while ($row = $recordResult->fetch_assoc()) {

    $studentName = $row['StudentName'];
    $subjectName = $row['ClassName'];
    $percentage = (int) $row['AttendancePercentage'] . '%';

    $subjects[$subjectName] = true;

    $attendanceMatrix[$studentName][$subjectName] = $percentage;
}
    $recordStmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('Student attendance report error: ' . $e->getMessage());
}

$recordCount = count($attendanceRecords);
$currentDay = (int) date('d');
$isMonthEnd =$currentDay >= 28;

?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Student Attendance Matrix</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="../../assets/js/tailwind-config.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="../../assets/css/app.css"/>
</head><body class="flex min-h-screen">
<!-- Sidebar -->
<aside id="app-sidebar" class="w-sidebar-width h-screen sticky top-0 left-0 bg-surface-container-low border-r border-outline-variant flex flex-col py-xl">
<div class="px-xl mb-xl">
<h1 class="text-h2 font-h2 text-primary">Student Portal</h1>
<p class="font-body-md text-body-md text-secondary">Academic Management</p>
</div>
<nav class="flex-1 px-sm space-y-1">
<a class="flex items-center gap-md px-md py-sm text-secondary font-body-md hover:bg-surface-container-high transition-all duration-200 cursor-pointer" href="dashboard.php">
<span class="material-symbols-outlined">dashboard</span>
<span>Dashboard</span>
</a>
<!-- Active State Navigation -->
<a class="flex items-center gap-md px-md py-sm text-primary font-bold border-l-4 border-primary bg-secondary-container/20 transition-all duration-200 cursor-pointer" href="attendance-report.php">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">assessment</span>
<span>View Report</span>
</a>
<a class="flex items-center gap-md px-md py-sm text-secondary font-body-md hover:bg-surface-container-high transition-all duration-200 cursor-pointer" href="profile.php">
<span class="material-symbols-outlined">person</span>
<span>Profile</span>
</a>
</nav>
<div class="px-xl mt-auto pt-xl border-t border-outline-variant">
<div class="flex items-center gap-md">
<img alt="Institution Logo" class="w-10 h-10 rounded-lg" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC1SQYeosoTPaDSB_vdfRzKpZiHb2Q1xgEWKUbkQuW_YR3UpDRGfPzaNzPOT69IzDYdiVe-zDYREApNFCrxBP8h89CdqBjg1IJaHfPd-evmXb9hXyL74w9mDoukw1Rh3wUjlkboQ4hrMRIilq84-qT_VfaM7l179Y9-UKPkYzPRdijsN3JlnURHEK49i4U5KPyVzeRfd1ezBxubs-Se03gMxQOEkwP719kPpWTnyHil4D9iIIbUCANDSdC69m8z8wWooQnTFIUwHw"/>
<div class="overflow-hidden">
<p class="font-label-md text-label-md text-on-surface truncate">Highland Institute</p>
<p class="text-xs text-on-surface-variant">Admin Branch</p>
</div>
</div>
</div>
</aside>
<!-- Main Content Area -->
<main class="app-layout-main flex-1 flex flex-col min-w-0 lg:ml-[280px]">
<!-- TopAppBar -->
<header class="flex justify-between items-center h-16 px-xl w-full bg-surface border-b border-outline-variant sticky top-0 z-10">
<div class="flex items-center">
<span class="text-h3 font-h3 text-primary">Academic Portal</span>
</div>
<div class="flex items-center gap-xl">
<div class="flex items-center gap-lg">
<button class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors cursor-pointer">notifications</button>
<button class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors cursor-pointer">settings</button>
</div>
<div class="h-8 w-[1px] bg-outline-variant"></div>
<div class="flex items-center gap-md">
<div class="text-right hidden md:block">
<p class="font-label-md text-label-md text-on-surface"><?= $userName ?></p>
<p class="text-xs text-on-surface-variant">Student</p>
</div>
<img alt="User Avatar" class="w-9 h-9 rounded-full border border-outline-variant" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBsPSiLohkzjW0hWO4EWyvYWbuOBcmIaBM-Mfu4JGdY3jwSMipkaDk0M7k0PBUfPtsl0MGu8lXjGqAKoPqFelH1jL6X0_BZenFEygVNSF-q4ZfDo1OtdUQ-jNez_ctplozkBcTwmrwSma15SkPxB5elIg8VWZCoNZDneqqDF481-hjK5ohTzDHZFQHuxGdaJeaZwNVO7OXmoTnyv5rntYiqPUycXnQpT-Xw0YCbZyrx1tBb3uqTnMHDnDyf-Ix7zlTyaebRZhveuw"/>
</div>
</div>
</header>
<!-- Content Canvas -->
<div class="p-xl max-w-container-max mx-auto w-full">
<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-lg mb-xl">
<div>
<h2 class="font-display text-display text-on-surface mb-xs">My Attendance Records</h2>
<p class="font-body-lg text-body-lg text-secondary">View your Present and Absent history for enrolled classes</p>
</div>

</div>
<?php if (!$isMonthEnd): ?>

<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-xl text-center">
    <h3 class="text-h3 font-h3 text-on-surface mb-sm">
        Monthly Report Not Available Yet
    </h3>

    <p class="text-body-md text-secondary">
        Attendance matrix reports will be generated at the end of the month.
    </p>
</div>

<?php else: ?>
<!-- Attendance Table -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.1)] overflow-hidden">
<div class="p-lg border-b border-outline-variant flex items-center justify-between">
<h3 class="font-h3 text-h3 text-on-surface">Attendance History</h3>
<span class="px-md py-xs bg-primary-container/10 text-primary rounded-full text-xs font-bold uppercase"><?= $recordCount ?> Record<?= $recordCount === 1 ? '' : 's' ?></span>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead class="bg-surface-container-low">

<tr>

<th class="px-lg py-md font-label-sm text-on-surface-variant uppercase border-b border-outline-variant">
Student Name
</th>

<?php foreach (array_keys($subjects) as $subject): ?>

<th class="px-lg py-md font-label-sm text-on-surface-variant uppercase border-b border-outline-variant text-center">

<?= htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') ?>

</th>

<?php endforeach; ?>

</tr>

</thead>
<tbody class="divide-y divide-outline-variant">

<?php if (empty($attendanceMatrix)): ?>

<tr>
<td class="px-lg py-xl text-center font-body-md text-on-surface-variant" colspan="10">
No attendance matrix data available yet.
</td>
</tr>

<?php else: ?>

<?php foreach ($attendanceMatrix as $studentName => $studentData): ?>

<tr class="hover:bg-primary-fixed/10 transition-colors">

<td class="px-lg py-md font-label-md text-on-surface">
<?= htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') ?>
</td>

<?php foreach (array_keys($subjects) as $subject): ?>

<td class="px-lg py-md text-center font-body-md text-on-surface">

<?= htmlspecialchars($studentData[$subject] ?? '0%', ENT_QUOTES, 'UTF-8') ?>

</td>

<?php endforeach; ?>

</tr>

<?php endforeach; ?>

<?php endif; ?>

</tbody>
</table>
</div>
</div>
<?php endif; ?>
</div>
</main>
</body>
<script src="../../assets/js/app.js"></script>
</html>
