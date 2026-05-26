<?php
$requiredRole = 'Teacher';
require_once __DIR__ . '/../../php/auth/session-check.php';
require_once __DIR__ . '/../../php/config/database.php';

$userName = htmlspecialchars((string) $_SESSION['Name'], ENT_QUOTES, 'UTF-8');
$teacherId = (int) $_SESSION['UserID'];

$classSuccess = $_SESSION['class_success'] ?? null;
unset($_SESSION['class_success']);

$classes = [];
try {
    $db = getDBConnection();
    $stmt = $db->prepare(
        'SELECT c.ClassID, c.ClassName, c.ClassCode, c.CreateDate,
                (SELECT COUNT(*) FROM enrollments e WHERE e.ClassID = c.ClassID) AS student_count
         FROM classes c
         WHERE c.TeacherID = ?
         ORDER BY c.CreateDate DESC'
    );
    $stmt->bind_param('i', $teacherId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('Teacher dashboard classes error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>EduAttend - Teacher Dashboard</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="../../assets/js/tailwind-config.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="../../assets/css/app.css"/>
</head><body class="bg-background text-on-background antialiased">
<!-- SideNavBar Anchor -->
<aside id="app-sidebar" class="w-[280px] h-screen fixed left-0 top-0 bg-surface-low border-r border-outline-variant flex flex-col h-full py-lg">
<div class="px-xl mb-xl">
<h1 class="font-h2 text-h2 font-bold text-primary">EduAttend</h1>
<p class="font-body-sm text-body-sm text-secondary">Academic Portal</p>
</div>
<nav class="flex-1 flex flex-col gap-1">
<!-- Active Navigation: Dashboard -->
<a class="flex items-center gap-md px-xl py-md text-primary font-bold border-l-4 border-primary hover:bg-secondary-container transition-colors" href="dashboard.php">
<span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
<span class="font-label-md text-label-md">Dashboard</span>
</a>
<a class="flex items-center gap-md px-xl py-md text-secondary hover:bg-secondary-container transition-colors" href="mark-attendance.php">
<span class="material-symbols-outlined" data-icon="how_to_reg">how_to_reg</span>
<span class="font-label-md text-label-md">Mark Attendance</span>
</a>
<a class="flex items-center gap-md px-xl py-md text-secondary hover:bg-secondary-container transition-colors" href="consolidated-report.php">
<span class="material-symbols-outlined" data-icon="assessment">assessment</span>
<span class="font-label-md text-label-md">View Report</span>
</a>
<a class="flex items-center gap-md px-xl py-md text-secondary hover:bg-secondary-container transition-colors" href="profile.php">
<span class="material-symbols-outlined" data-icon="person">person</span>
<span class="font-label-md text-label-md">Profile</span>
</a>
</nav>
<div class="px-xl mt-auto pt-lg">
<div class="p-md rounded-lg bg-surface-container flex items-center gap-sm">
<div class="w-2 h-2 rounded-full bg-green-500"></div>
<span class="font-label-sm text-label-sm text-on-surface-variant">System Status: Active</span>
</div>
</div>
</aside>
<!-- TopNavBar Anchor -->
<header class="teacher-fixed-header fixed top-0 right-0 w-[calc(100%-280px)] h-16 bg-surface border-b border-outline-variant shadow-sm flex justify-between items-center px-xl z-10">
<div class="flex items-center gap-lg ml-auto">
<div class="flex items-center gap-md">
</div>
<div class="flex items-center gap-sm border-l border-outline-variant pl-lg ">
<div class="text-right">
<p class="font-label-md text-label-md text-on-surface leading-tight"><?= $userName ?></p>
<p class="font-label-sm text-label-sm text-on-surface-variant">Faculty Admin</p>
</div>
<img alt="User Avatar" class="w-8 h-8 rounded-full bg-primary-fixed" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAxWKdfhYL8P_r1kHrNrRatYByJCLwJq1pjW8__ddIhZN9b40PZ44JZEEWmL74YRYv0TJShzd7n84XRD9KvFw72gBUqIIjaT2upcpZr6wsenysVvKpyAUR_Dihf67xTF3UQSzhk7uYkGOKuIcEj4cheoscthWTqkq_m6HrRMAfr9qkjcv4Jbiqo2T4s5B7SpRV3VU29uwEkpLMNCroUNSSYovFMZFM6SmRg_fQb9QMWFMvv1vmdCUqNbwUOmzHOCSY8KuCpsmH85w"/>

</div>
</div>
</header>
<!-- Main Content Canvas -->
<main class="ml-[280px] pt-16 min-h-screen">
<div class="max-w-[1440px] mx-auto p-xl">
<!-- Welcome Section -->
<section class="flex flex-col md:flex-row md:items-center justify-between gap-lg mb-xl">
<div>
<h2 class="font-h1 text-h1 text-on-surface">Welcome, <?= $userName ?></h2>
<p class="font-body-md text-body-md text-on-surface-variant mt-1">Manage your academic schedules and student engagement today.</p>
</div>
<a href="create-class.php" class="flex items-center gap-sm bg-primary-container text-white px-lg py-md rounded-lg shadow-sm hover:bg-blue-800 transition-colors active:scale-95">
<span class="material-symbols-outlined" data-icon="add">add</span>
<span class="font-label-md text-label-md">Create Class</span>
</a>
</section>

<?php if ($classSuccess): ?>
<div class="mb-lg rounded-lg border border-primary-container bg-secondary-container px-lg py-md" role="status">
<p class="font-body-md text-body-md text-primary font-semibold"><?= htmlspecialchars((string) $classSuccess, ENT_QUOTES, 'UTF-8') ?></p>
</div>
<?php endif; ?>

<!-- Class Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-lg">
<?php if (empty($classes)): ?>
<div class="md:col-span-2 xl:col-span-3 bg-surface border border-outline-variant rounded-xl p-xl text-center">
<p class="font-body-md text-body-md text-on-surface-variant">You have not created any classes yet.</p>
<a href="create-class.php" class="inline-flex items-center gap-sm mt-lg bg-primary-container text-white px-lg py-md rounded-lg font-label-md hover:bg-blue-800 transition-colors">
<span class="material-symbols-outlined">add</span>
Create your first class
</a>
</div>
<?php else: ?>
<?php foreach ($classes as $class): ?>
<?php
    $className = htmlspecialchars((string) $class['ClassName'], ENT_QUOTES, 'UTF-8');
    $classCode = htmlspecialchars((string) $class['ClassCode'], ENT_QUOTES, 'UTF-8');
    $studentCount = (int) $class['student_count'];
    $createDate = htmlspecialchars(date('M j, Y', strtotime((string) $class['CreateDate'])), ENT_QUOTES, 'UTF-8');
?>
<div class="bg-surface border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col hover:shadow-md transition-shadow">
<div class="h-24 w-full bg-primary-container/10 relative flex items-center justify-center">
<div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-sm py-xs rounded font-label-sm text-primary border border-outline-variant">
<?= $classCode ?>
</div>
<span class="material-symbols-outlined text-primary text-[48px] opacity-40">school</span>
</div>
<div class="p-lg flex flex-col flex-1">
<h3 class="font-h3 text-h3 text-on-surface mb-xs"><?= $className ?></h3>
<div class="flex items-center gap-sm text-on-surface-variant mb-lg">
<span class="material-symbols-outlined text-sm" data-icon="group">group</span>
<span class="font-body-sm"><?= $studentCount ?> Student<?= $studentCount === 1 ? '' : 's' ?> Enrolled</span>
</div>
<div class="mt-auto pt-lg border-t border-outline-variant flex justify-between items-center">
<span class="font-label-sm text-label-sm text-secondary">Created <?= $createDate ?></span>
<a href="mark-attendance.php?class_id=<?= (int) $class['ClassID'] ?>&date=<?= date('Y-m-d') ?>" class="text-primary font-label-sm hover:underline">Mark Attendance</a>
</div>
</div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>
<!-- Recent Activity Section -->
</div>
</main>
</body>
<script src="../../assets/js/app.js"></script>
</html>
