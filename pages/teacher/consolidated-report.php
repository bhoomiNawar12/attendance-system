<?php
$requiredRole = 'Teacher';
require_once __DIR__ . '/../../php/auth/session-check.php';
require_once __DIR__ . '/../../php/config/database.php';

$userName = htmlspecialchars((string) $_SESSION['Name'], ENT_QUOTES, 'UTF-8');

$teacherId = (int) $_SESSION['UserID'];

$classList = [];
$attendanceMatrix = [];
$subjects = [];
$selectedClassId = isset($_GET['class_id'])
    ? (int) $_GET['class_id']
    : 0;
    $currentDay = (int) date('d');
$isMonthEnd = $currentDay >= 28;
try {

    $db = getDBConnection();

    $classStmt = $db->prepare(
        'SELECT ClassID, ClassName
         FROM classes
         WHERE TeacherID = ?
         ORDER BY ClassName ASC'
    );

    $classStmt->bind_param('i', $teacherId);
    $classStmt->execute();

    $classResult = $classStmt->get_result();

    while ($row = $classResult->fetch_assoc()) {
        $classList[] = $row;
    }

    $classStmt->close();
    $reportStmt = $db->prepare(

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

     WHERE c.ClassID = ?
     AND MONTH(a.AttendanceDate) = MONTH(CURRENT_DATE())
AND YEAR(a.AttendanceDate) = YEAR(CURRENT_DATE())

     GROUP BY u.UserID, c.ClassID

     ORDER BY u.Name ASC'
);
$reportStmt->bind_param('i', $selectedClassId);

$reportStmt->execute();

$reportResult = $reportStmt->get_result();

while ($row = $reportResult->fetch_assoc()) {

    $studentName = $row['StudentName'];
    $subjectName = $row['ClassName'];
    $percentage = (int) $row['AttendancePercentage'] . '%';

    $subjects[$subjectName] = true;

    $attendanceMatrix[$studentName][$subjectName] = $percentage;
}

$reportStmt->close();

} catch (mysqli_sql_exception $e) {

    error_log('Teacher report error: ' . $e->getMessage());

}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>EduAttend - Teacher Consolidated Report</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="../../assets/js/tailwind-config.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="../../assets/css/app.css"/>
</head><body class="bg-surface-bright text-on-surface antialiased">
<!-- SideNavBar (Shared Component) -->
<aside id="app-sidebar" class="w-[280px] h-screen fixed left-0 top-0 bg-surface-low dark:bg-surface-container-lowest border-r border-outline-variant dark:border-outline flex flex-col h-full py-lg z-50">
<div class="px-lg mb-xl flex flex-col gap-xs">
<span class="font-h2 text-h2 font-bold text-primary dark:text-primary-fixed">EduAttend</span>
<span class="font-label-md text-label-md text-secondary">Academic Portal</span>
</div>
<nav class="flex-1 px-md flex flex-col gap-xs">
<a class="flex items-center gap-md px-md py-sm rounded-lg text-secondary dark:text-secondary-fixed-dim hover:bg-secondary-container dark:hover:bg-on-primary-fixed-variant transition-colors group" href="dashboard.php">
<span class="material-symbols-outlined group-hover:text-primary">dashboard</span>
<span class="font-label-md text-label-md">Dashboard</span>
</a>
<a class="flex items-center gap-md px-md py-sm rounded-lg text-secondary dark:text-secondary-fixed-dim hover:bg-secondary-container dark:hover:bg-on-primary-fixed-variant transition-colors group" href="mark-attendance.php">
<span class="material-symbols-outlined group-hover:text-primary">how_to_reg</span>
<span class="font-label-md text-label-md">Mark Attendance</span>
</a>
<a class="flex items-center gap-md px-md py-sm rounded-lg text-primary dark:text-primary-fixed font-bold border-l-4 border-primary dark:border-primary-fixed bg-secondary-container/30 transition-colors group" href="consolidated-report.php">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">assessment</span>
<span class="font-label-md text-label-md">View Report</span>
</a>
<a class="flex items-center gap-md px-md py-sm rounded-lg text-secondary dark:text-secondary-fixed-dim hover:bg-secondary-container dark:hover:bg-on-primary-fixed-variant transition-colors group" href="profile.php">
<span class="material-symbols-outlined group-hover:text-primary">person</span>
<span class="font-label-md text-label-md">Profile</span>
</a>
</nav>
<div class="px-lg mt-auto">
<div class="p-sm bg-surface-container-high rounded-lg">
<span class="font-label-sm text-label-sm text-on-surface-variant">System Status: Active</span>
</div>
</div>
</aside>
<!-- TopNavBar (Shared Component) -->
<header class="teacher-fixed-header fixed top-0 right-0 w-[calc(100%-280px)] h-16 bg-surface dark:bg-background border-b border-outline-variant dark:border-outline flex justify-between items-center px-xl z-40 shadow-sm dark:shadow-none">
<div class="flex items-center gap-lg">
<!-- Search bar removed as per instructions -->
</div>
<div class="flex items-center gap-lg">
<div class="flex items-center gap-md">
<button class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-all cursor-pointer active:scale-95">notifications</button>
<button class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-all cursor-pointer active:scale-95">help_outline</button>
</div>
<div class="h-8 w-px bg-outline-variant"></div>
<div class="flex items-center gap-sm">
<div class="text-right">
<p class="font-label-md text-label-md font-bold text-on-surface">
    <?= $userName ?>
</p>

<p class="font-label-sm text-label-sm text-secondary">
    Teacher
</p>
</div>
<img alt="User Avatar" class="w-10 h-10 rounded-full border border-primary/20" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBwV88MhzmEpPXAUKnzE0FkZIFzFH6hEJjtSNSOy9GJxX_aPPpJcQL2WfBvj-6ZIidCJ_1fERoCUXa-19KXS_qFCMNyZPc6HDgwsf0grr5DAZwQHvdqDIE12wkR98myWGrsJ4kG_YVElsuU6uGD06DW7hHDeWQkKJiGtsqo5lNerWtFh0k7w9QNNfooBaEYcpIZyHmrPcDVDxuGvpkh-d5WXRc5tN6F1oqHIupZ5EK1-oliJTqK1tV_Ryt07lqJutRumFY4Cd2hag"/>
</div>
</div>
</header>
<!-- Main Content Canvas -->
<main class="ml-[280px] mt-16 p-xl max-w-[1440px]">
<!-- Header & Selectors -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-lg mb-xl">
<div>
<h1 class="font-h1 text-h1 text-on-surface mb-xs">Consolidated Attendance Report</h1>

</div>
<div class="flex gap-md">
<div class="flex flex-col gap-xs">
<label class="font-label-sm text-label-sm text-on-surface-variant font-bold">Select Class</label>
<div class="relative">
<select
name="class_id"
onchange="window.location.href='?class_id=' + this.value"
class="appearance-none bg-surface-container-low border-2 border-primary rounded-lg px-lg py-sm pr-xl font-label-md text-label-md text-on-surface focus:ring-2 focus:ring-primary/10 focus:border-primary outline-none min-w-[240px] shadow-sm"
>

<?php foreach ($classList as $class): ?>

<option
value="<?= $class['ClassID'] ?>"
<?= $selectedClassId == $class['ClassID'] ? 'selected' : '' ?>
>

<?= htmlspecialchars($class['ClassName'], ENT_QUOTES, 'UTF-8') ?>

</option>

<?php endforeach; ?>

</select>
<span class="material-symbols-outlined absolute right-sm top-1/2 -translate-y-1/2 pointer-events-none text-primary">school</span>
</div>
</div>
<div class="flex flex-col gap-xs">
<label class="font-label-sm text-label-sm text-on-surface-variant font-bold">Reporting Month</label>
<div class="relative">

<span class="material-symbols-outlined absolute right-sm top-1/2 -translate-y-1/2 pointer-events-none text-secondary">calendar_month</span>
</div>
</div>
</div>
</div>
<?php if (!$isMonthEnd): ?>

<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-xl text-center">
    <h3 class="text-h3 font-h3 text-on-surface mb-sm">
        Monthly Report Not Available Yet
    </h3>

    <p class="text-body-md text-secondary">
        Attendance reports will be generated at the end of the month.
    </p>
</div>

<?php else: ?>
<!-- Main Report Table Card -->
<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<div class="px-lg py-md border-b border-outline-variant flex items-center justify-between bg-surface-container-lowest">
<div>
<h2 class="font-h3 text-h3 text-on-surface">Student Performance Summary</h2>

</div>
<div class="flex items-center gap-md">
<span class="font-label-sm text-label-sm text-secondary">
Viewing <?= count($attendanceMatrix) ?> Students
</span>
<div class="flex gap-xs">
<button class="material-symbols-outlined p-xs border border-outline-variant rounded text-on-surface-variant hover:bg-surface-container transition-colors">chevron_left</button>
<button class="material-symbols-outlined p-xs border border-outline-variant rounded text-on-surface-variant hover:bg-surface-container transition-colors">chevron_right</button>
</div>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead class="bg-surface-container-low">

<tr>

<th class="px-lg py-md font-label-sm text-label-sm text-secondary uppercase tracking-wider border-b border-outline-variant">
Student Name
</th>

<?php foreach (array_keys($subjects) as $subject): ?>

<th class="px-md py-md font-label-sm text-label-sm text-secondary uppercase tracking-wider border-b border-outline-variant text-center">

<?= htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') ?>

</th>

<?php endforeach; ?>

</tr>

</thead>
<tbody class="divide-y divide-outline-variant">

<?php if (empty($attendanceMatrix)): ?>

<tr>
<td class="px-lg py-xl text-center font-body-md text-on-surface-variant" colspan="10">
No attendance data available yet.
</td>
</tr>

<?php else: ?>

<?php foreach ($attendanceMatrix as $studentName => $studentData): ?>

<tr class="hover:bg-secondary-container/10 transition-colors">

<td class="px-lg py-md">

<div class="flex items-center gap-md">

<div class="w-10 h-10 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-bold">

<?= strtoupper(substr($studentName, 0, 2)) ?>

</div>

<div>
<p class="font-label-md text-label-md text-on-surface font-semibold">

<?= htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') ?>

</p>
</div>

</div>

</td>

<?php foreach (array_keys($subjects) as $subject): ?>

<td class="px-md py-md text-center font-label-md">

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
</main>
</body>
<script src="../../assets/js/app.js"></script>
</html>
