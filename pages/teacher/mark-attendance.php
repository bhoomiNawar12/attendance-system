<?php
$requiredRole = 'Teacher';
require_once __DIR__ . '/../../php/auth/session-check.php';
require_once __DIR__ . '/../../php/config/database.php';

$userName = htmlspecialchars((string) $_SESSION['Name'], ENT_QUOTES, 'UTF-8');
$teacherId = (int) $_SESSION['UserID'];

$attendanceErrors = $_SESSION['attendance_errors'] ?? [];
$attendanceSuccess = $_SESSION['attendance_success'] ?? null;
unset($_SESSION['attendance_errors'], $_SESSION['attendance_success']);

$selectedClassId = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;
$selectedDate = trim((string) ($_GET['date'] ?? date('Y-m-d')));
$dateObj = DateTime::createFromFormat('Y-m-d', $selectedDate);
if (!$dateObj || $dateObj->format('Y-m-d') !== $selectedDate) {
    $selectedDate = date('Y-m-d');
}

$classes = [];
$selectedClass = null;
$students = [];
$alreadyMarked = false;
$existingAttendance = [];

try {
    $db = getDBConnection();

    $classListStmt = $db->prepare(
        'SELECT ClassID, ClassName, ClassCode FROM classes WHERE TeacherID = ? ORDER BY ClassName ASC'
    );
    $classListStmt->bind_param('i', $teacherId);
    $classListStmt->execute();
    $classResult = $classListStmt->get_result();
    while ($row = $classResult->fetch_assoc()) {
        $classes[] = $row;
    }
    $classListStmt->close();

    if ($selectedClassId > 0) {
        $classStmt = $db->prepare(
            'SELECT ClassID, ClassName, ClassCode FROM classes WHERE ClassID = ? AND TeacherID = ? LIMIT 1'
        );
        $classStmt->bind_param('ii', $selectedClassId, $teacherId);
        $classStmt->execute();
        $selectedClass = $classStmt->get_result()->fetch_assoc();
        $classStmt->close();

        if ($selectedClass) {
            $studentStmt = $db->prepare(
                'SELECT u.UserID, u.Name
                 FROM enrollments e
                 INNER JOIN users u ON u.UserID = e.StudentID
                 WHERE e.ClassID = ?
                 ORDER BY u.Name ASC'
            );
            $studentStmt->bind_param('i', $selectedClassId);
            $studentStmt->execute();
            $studentResult = $studentStmt->get_result();
            while ($row = $studentResult->fetch_assoc()) {
                $students[] = $row;
            }
            $studentStmt->close();

            $checkStmt = $db->prepare(
                'SELECT COUNT(*) AS total FROM attendance WHERE ClassID = ? AND AttendanceDate = ?'
            );
            $checkStmt->bind_param('is', $selectedClassId, $selectedDate);
            $checkStmt->execute();
            $alreadyMarked = (int) $checkStmt->get_result()->fetch_assoc()['total'] > 0;
            $checkStmt->close();

            if ($alreadyMarked) {
                $existingStmt = $db->prepare(
                    'SELECT u.UserID, u.Name, a.Status
                     FROM attendance a
                     INNER JOIN users u ON u.UserID = a.StudentID
                     WHERE a.ClassID = ? AND a.AttendanceDate = ?
                     ORDER BY u.Name ASC'
                );
                $existingStmt->bind_param('is', $selectedClassId, $selectedDate);
                $existingStmt->execute();
                $existingResult = $existingStmt->get_result();
                while ($row = $existingResult->fetch_assoc()) {
                    $existingAttendance[] = $row;
                }
                $existingStmt->close();
            }
        }
    }
} catch (mysqli_sql_exception $e) {
    error_log('Mark attendance page error: ' . $e->getMessage());
}

$studentCount = count($students);
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>EduAttend - Mark Attendance</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="../../assets/js/tailwind-config.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="../../assets/css/app.css"/>
</head><body class="bg-surface text-on-surface">
<!-- Sidebar Navigation -->
<aside id="app-sidebar" class="w-[280px] h-screen fixed left-0 top-0 bg-surface-low dark:bg-surface-container-lowest border-r border-outline-variant dark:border-outline flex flex-col h-full py-lg">
<div class="px-xl mb-xl">
<h2 class="font-h2 text-h2 font-bold text-primary dark:text-primary-fixed">EduAttend</h2>
<p class="font-body-sm text-body-sm text-secondary">Academic Portal</p>
</div>
<nav class="flex-grow px-md space-y-xs">
<a class="flex items-center gap-md px-md py-sm rounded-lg text-secondary dark:text-secondary-fixed-dim hover:bg-secondary-container dark:hover:bg-on-primary-fixed-variant transition-colors group" href="dashboard.php">
<span class="material-symbols-outlined">dashboard</span>
<span class="font-label-md text-label-md">Dashboard</span>
</a>
<a class="flex items-center gap-md px-md py-sm rounded-lg text-primary dark:text-primary-fixed font-bold border-l-4 border-primary dark:border-primary-fixed hover:bg-secondary-container transition-colors" href="mark-attendance.php">
<span class="material-symbols-outlined">how_to_reg</span>
<span class="font-label-md text-label-md">Mark Attendance</span>
</a>
<a class="flex items-center gap-md px-md py-sm rounded-lg text-secondary dark:text-secondary-fixed-dim hover:bg-secondary-container dark:hover:bg-on-primary-fixed-variant transition-colors" href="consolidated-report.php">
<span class="material-symbols-outlined">assessment</span>
<span class="font-label-md text-label-md">View Report</span>
</a>
<a class="flex items-center gap-md px-md py-sm rounded-lg text-secondary dark:text-secondary-fixed-dim hover:bg-secondary-container dark:hover:bg-on-primary-fixed-variant transition-colors" href="profile.php">
<span class="material-symbols-outlined">person</span>
<span class="font-label-md text-label-md">Profile</span>
</a>
</nav>
<div class="px-xl mt-auto pt-lg border-t border-outline-variant">
<div class="flex items-center gap-sm">
<div class="w-2 h-2 rounded-full bg-emerald-500"></div>
<span class="font-label-sm text-label-sm text-secondary">System Status: Active</span>
</div>
</div>
</aside>
<!-- Header Navigation -->
<header class="teacher-fixed-header fixed top-0 right-0 w-[calc(100%-280px)] h-16 bg-surface dark:bg-background border-b border-outline-variant dark:border-outline shadow-sm dark:shadow-none z-10">
<div class="flex justify-end items-center px-xl h-full">
<div class="flex items-center gap-lg">
<div class="flex items-center gap-md">
<button class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors active:scale-95">notifications</button>
<button class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors active:scale-95">help_outline</button>
</div>
<div class="flex items-center gap-sm border-l border-outline-variant pl-lg">
<div class="text-right">
<p class="font-label-md text-label-md text-on-surface"><?= $userName ?></p>
<p class="text-[10px] text-secondary">Teacher</p>
</div>
<img alt="User Avatar" class="w-10 h-10 rounded-full border border-outline-variant" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB72Nnad0MNZXiMO5MGtBWyM4o5wSp-DQvlDOuaeIva1M5raoTFLOSDFSd3ZYE7UenJ0DF_zfCX7Q1dbEPtlKfgxcTaD0NCZL6vDFUO75VrhSbg72wqh0KvDnWx442kFwZBMpYnkaScTrSl2Uq5cILMjWMcQWbOob33o3kwcMkoSeNeZNOxOhlAJFuvBifLhgJfjUXuHNWwb368-IurTZE-bXedrSeLHdQ_LPVZezlzILekNac4eTER8IvGaznYLhQ8OwXY9G2ZcA"/>
</div>
</div>
</div>
</header>
<!-- Main Content Canvas -->
<main class="ml-[280px] pt-16 min-h-screen">
<div class="max-w-[1440px] mx-auto p-xl">

<?php if ($attendanceSuccess): ?>
<div class="mb-lg rounded-lg border border-primary-container bg-secondary-container px-lg py-md" role="status">
<p class="font-body-md text-body-md text-primary font-semibold"><?= htmlspecialchars((string) $attendanceSuccess, ENT_QUOTES, 'UTF-8') ?></p>
</div>
<?php endif; ?>

<?php if (!empty($attendanceErrors)): ?>
<div class="mb-lg rounded-lg border border-error bg-error-container/30 px-md py-sm" role="alert">
<ul class="list-disc pl-lg space-y-xs font-body-sm text-body-sm text-on-error-container">
<?php foreach ($attendanceErrors as $error): ?>
<li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<!-- Selection Section -->
<div class="bg-white rounded-xl border border-outline-variant shadow-sm mb-lg p-lg">
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-lg">
<div class="space-y-xs">
<div class="flex items-center gap-sm">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">school</span>
<h1 class="font-h1 text-h1 text-on-surface">Mark Attendance</h1>
</div>
<p class="font-body-md text-body-md text-secondary">Select a class and date, then mark Present or Absent for each student.</p>
</div>
<form method="GET" class="flex flex-wrap gap-md w-full md:w-auto items-end">
<div class="flex flex-col gap-xs w-full md:w-64">
<label class="font-label-sm text-label-sm text-secondary uppercase tracking-wider" for="class_id">Select Class</label>
<select
class="rounded-lg border-outline-variant text-body-sm focus:border-primary focus:ring-1 focus:ring-primary/10 transition-all bg-surface w-full"
id="class_id"
name="class_id"
required
onchange="this.form.submit()"
>
<option value="">Choose a class</option>
<?php foreach ($classes as $class): ?>
<?php
    $cid = (int) $class['ClassID'];
    $cname = htmlspecialchars((string) $class['ClassName'], ENT_QUOTES, 'UTF-8');
    $selected = $cid === $selectedClassId ? ' selected' : '';
?>
<option value="<?= $cid ?>"<?= $selected ?>><?= $cname ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="flex flex-col gap-xs w-full md:w-48">
<label class="font-label-sm text-label-sm text-secondary uppercase tracking-wider" for="date">Date</label>
<input class="rounded-lg border-outline-variant text-body-sm focus:border-primary focus:ring-1 focus:ring-primary/10 transition-all bg-surface w-full" id="date" name="date" type="date" value="<?= htmlspecialchars($selectedDate, ENT_QUOTES, 'UTF-8') ?>" required/>
</div>
<button class="px-lg py-md bg-primary-container text-white font-label-md rounded-lg hover:bg-primary transition-colors" type="submit">Load Class</button>
</form>
</div>

<?php if ($selectedClass): ?>
<div class="mt-xl flex items-center justify-between p-md bg-surface-container-low rounded-lg border border-outline-variant/50">
<div class="flex items-center gap-xl flex-wrap">
<div>
<span class="font-label-sm text-label-sm text-secondary">CLASS NAME</span>
<p class="font-h3 text-h3 text-primary"><?= htmlspecialchars((string) $selectedClass['ClassName'], ENT_QUOTES, 'UTF-8') ?></p>
</div>
<div class="border-l border-outline-variant h-10"></div>
<div>
<span class="font-label-sm text-label-sm text-secondary">CLASS CODE</span>
<p class="font-h3 text-h3 text-primary"><?= htmlspecialchars((string) $selectedClass['ClassCode'], ENT_QUOTES, 'UTF-8') ?></p>
</div>
</div>
<div class="text-right">
<p class="font-label-md text-label-md text-on-surface">Total Enrolled</p>
<p class="font-h2 text-h2 text-on-surface"><?= $studentCount ?> Student<?= $studentCount === 1 ? '' : 's' ?></p>
</div>
</div>
<?php endif; ?>
</div>

<?php if ($selectedClass && empty($students)): ?>
<div class="bg-white rounded-xl border border-outline-variant shadow-sm p-xl text-center">
<p class="font-body-md text-on-surface-variant">No students are enrolled in this class yet.</p>
</div>
<?php elseif ($selectedClass && $alreadyMarked): ?>
<!-- Already recorded -->
<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<div class="p-md bg-secondary-container border-b border-outline-variant">
<p class="font-body-md text-primary font-semibold">Attendance already recorded for <?= htmlspecialchars($selectedDate, ENT_QUOTES, 'UTF-8') ?>.</p>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead class="bg-surface-container-low">
<tr>
<th class="px-xl py-md font-label-sm text-secondary uppercase border-b border-outline-variant">#</th>
<th class="px-xl py-md font-label-sm text-secondary uppercase border-b border-outline-variant">Student Name</th>
<th class="px-xl py-md font-label-sm text-secondary uppercase border-b border-outline-variant text-center">Status</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant">
<?php foreach ($existingAttendance as $index => $record): ?>
<tr>
<td class="px-xl py-md font-body-sm text-secondary"><?= $index + 1 ?></td>
<td class="px-xl py-md font-label-md text-on-surface"><?= htmlspecialchars((string) $record['Name'], ENT_QUOTES, 'UTF-8') ?></td>
<td class="px-xl py-md text-center">
<span class="inline-block px-md py-xs rounded-full font-label-sm <?= $record['Status'] === 'Present' ? 'bg-primary-container/10 text-primary' : 'bg-error-container/30 text-error' ?>">
<?= htmlspecialchars((string) $record['Status'], ENT_QUOTES, 'UTF-8') ?>
</span>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>
<?php elseif ($selectedClass && !empty($students)): ?>
<!-- Attendance Form -->
<form action="../../php/attendance/save-attendance.php" method="POST">
<input type="hidden" name="class_id" value="<?= (int) $selectedClass['ClassID'] ?>"/>
<input type="hidden" name="attendance_date" value="<?= htmlspecialchars($selectedDate, ENT_QUOTES, 'UTF-8') ?>"/>
<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<div class="p-md bg-surface-container border-b border-outline-variant">
<h3 class="font-label-md text-label-md text-on-surface-variant">STUDENT ATTENDANCE SHEET</h3>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead class="bg-surface-container-low">
<tr>
<th class="px-xl py-md font-label-sm text-secondary uppercase border-b border-outline-variant">#</th>
<th class="px-xl py-md font-label-sm text-secondary uppercase border-b border-outline-variant">Student Name</th>
<th class="px-xl py-md font-label-sm text-secondary uppercase border-b border-outline-variant text-center">Present</th>
<th class="px-xl py-md font-label-sm text-secondary uppercase border-b border-outline-variant text-center">Absent</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant">
<?php foreach ($students as $index => $student): ?>
<?php
    $studentId = (int) $student['UserID'];
    $studentName = htmlspecialchars((string) $student['Name'], ENT_QUOTES, 'UTF-8');
    $initials = strtoupper(substr((string) $student['Name'], 0, 1));
?>
<tr class="hover:bg-surface-bright transition-colors">
<td class="px-xl py-md font-body-sm text-secondary"><?= $index + 1 ?></td>
<td class="px-xl py-md">
<div class="flex items-center gap-md">
<div class="w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-label-sm"><?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?></div>
<span class="font-label-md text-on-surface"><?= $studentName ?></span>
</div>
</td>
<td class="px-xl py-md text-center">
<input class="w-5 h-5 text-primary focus:ring-primary-container border-outline-variant cursor-pointer" name="status[<?= $studentId ?>]" value="Present" type="radio" checked required/>
</td>
<td class="px-xl py-md text-center">
<input class="w-5 h-5 text-error focus:ring-error-container border-outline-variant cursor-pointer" name="status[<?= $studentId ?>]" value="Absent" type="radio"/>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<div class="p-lg bg-surface-container-low border-t border-outline-variant flex justify-end">
<button class="px-xl py-md rounded-lg bg-primary text-white font-label-md hover:bg-primary-container transition-all active:scale-95 shadow-md" type="submit">Submit Attendance</button>
</div>
</div>
</form>
<?php elseif (empty($classes)): ?>
<div class="bg-white rounded-xl border border-outline-variant shadow-sm p-xl text-center">
<p class="font-body-md text-on-surface-variant mb-md">You have no classes yet.</p>
<a href="create-class.php" class="inline-flex items-center gap-sm bg-primary-container text-white px-lg py-md rounded-lg font-label-md">Create a Class</a>
</div>
<?php endif; ?>
</div>
</main>
</body>
<script src="../../assets/js/app.js"></script>
</html>
