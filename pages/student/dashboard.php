<?php
$requiredRole = 'Student';
require_once __DIR__ . '/../../php/auth/session-check.php';
require_once __DIR__ . '/../../php/config/database.php';

$userName = htmlspecialchars((string) $_SESSION['Name'], ENT_QUOTES, 'UTF-8');
$studentId = (int) $_SESSION['UserID'];

$joinErrors = $_SESSION['join_errors'] ?? [];
$joinOld = $_SESSION['join_old'] ?? [];
$joinSuccess = $_SESSION['join_success'] ?? null;
unset($_SESSION['join_errors'], $_SESSION['join_old'], $_SESSION['join_success']);

$classCodeVal = htmlspecialchars((string) ($joinOld['class_code'] ?? ''), ENT_QUOTES, 'UTF-8');

$enrolledClasses = [];
try {
    $db = getDBConnection();
   $stmt = $db->prepare(
    'SELECT 
        c.ClassID,
        c.ClassName,
        c.ClassCode,
        c.CreateDate,
        u.Name AS TeacherName,

        ROUND(
            (
                SUM(CASE WHEN a.Status = "Present" THEN 1 ELSE 0 END)
                /
                NULLIF(COUNT(a.AttendanceID), 0)
            ) * 100
        ) AS AttendancePercentage

     FROM enrollments e

     INNER JOIN classes c ON c.ClassID = e.ClassID
     INNER JOIN users u ON u.UserID = c.TeacherID

     LEFT JOIN attendance a
     ON a.ClassID = c.ClassID
     AND a.StudentID = e.StudentID

     WHERE e.StudentID = ?

     GROUP BY c.ClassID

     ORDER BY e.created_at DESC'
);
    $stmt->bind_param('i', $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $enrolledClasses[] = $row;
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('Student dashboard enrollments error: ' . $e->getMessage());
}

$enrolledCount = count($enrolledClasses);
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>EduAttend - Student Dashboard</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="../../assets/js/tailwind-config.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="../../assets/css/app.css"/>
</head><body class="bg-surface text-on-surface">
<div class="flex min-h-screen">
<!-- SideNavBar Component -->
<aside id="app-sidebar" class="w-sidebar-width h-screen sticky top-0 left-0 bg-surface-container-low border-r border-outline-variant flex flex-col py-xl">
<div class="px-xl mb-xl">
<h1 class="text-h2 font-h2 text-primary">Student Portal</h1>
<p class="text-body-md text-secondary">Academic Management</p>
</div>
<nav class="flex-1 px-md space-y-base">
<!-- Dashboard Active -->
<a class="flex items-center gap-md px-md py-sm text-primary font-bold border-l-4 border-primary bg-secondary-container/20 transition-all duration-200 cursor-pointer" href="dashboard.php">
<span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
<span class="font-body-md text-body-md">Dashboard</span>
</a>
<a class="flex items-center gap-md px-md py-sm text-secondary font-body-md hover:bg-surface-container-high transition-colors cursor-pointer" href="attendance-report.php">
<span class="material-symbols-outlined" data-icon="assessment">assessment</span>
<span class="font-body-md text-body-md">View Report</span>
</a>
<a class="flex items-center gap-md px-md py-sm text-secondary font-body-md hover:bg-surface-container-high transition-colors cursor-pointer" href="profile.php">
<span class="material-symbols-outlined" data-icon="person">person</span>
<span class="font-body-md text-body-md">Profile</span>
</a>
</nav>
<div class="px-xl mt-auto pt-xl">
<div class="flex items-center gap-sm">
<img alt="Institution Logo" class="w-8 h-8 rounded-lg" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDVk0COZK3dN2vZiQvsVxYsjV0E0uv7kGPUJufsqixquZnzI2_fZ5lUhd8MEC4S_QyfPmfYTFRRmRCmG8mUgPWV9lBTM7fdTYifpBqu6DU_Fg4hQnExw3IBMgSQ_oWhnApVnKph-hoeqQ2WdXGjGMO9tUf8cznPNT15RZbu00jvllW__5cx8whqlHf_p5Ej7MV1G_aKcXzl_ZA3HEKHP-ngPiVGVnUlgxYNSze03tV7Acm8uTtP-4dZwvtfvgL9sDoyZRiwbylj2Q"/>
<span class="text-label-sm font-label-sm text-outline uppercase tracking-wider">EduAttend v2.4</span>
</div>
</div>
</aside>
<!-- Main Canvas -->
<div class="app-layout-main flex-1 flex flex-col min-w-0 lg:ml-[280px]">
<!-- TopAppBar Component -->
<header class="flex justify-between items-center h-16 px-xl w-full bg-surface border-b border-outline-variant sticky top-0 z-10">
<div class="flex items-center">
<span class="text-h3 font-h3 text-primary">Academic Portal</span>
</div>
<div class="flex items-center gap-lg">
<div class="flex items-center gap-md">
</div>

<div class="flex items-center gap-sm pl-lg border-l border-outline-variant">
<img alt="User Avatar" class="w-8 h-8 rounded-full bg-primary-fixed text-on-primary-fixed flex items-center justify-center font-bold text-xs" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBr8mSu7R21xYn0jrBF5SI1-jYG9Kzbi-jC6HcA2K_YfyyVkXs5-2CRWjwSTRJE-Lj2gTjazJ-nn3wA8P7h7VlHV9eoLhU0WdCAb9qJrDRyqBtyM1y6fe-h7lJrTjmLKs0vqZav1W24mMNt30rLkO5y78kUnAmj7MMTAZhTmAzQaOd7XEM-S4jATypaA2W5dxLU5Yzz4RTuX5mrAKHYRD69_mHBDJ3kSQ5ZCLbwoVbvsVDHmTWyUetcNoxRMIr94qunRK4wtKrIAQ"/>
<span class="text-label-md font-label-md text-on-surface-variant"><?= $userName ?></span>
</div>
</div>
</header>
<!-- Content Area -->
<main class="p-xl max-w-container-max mx-auto w-full">
<!-- Welcome Section -->
<section class="mb-xl">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-md">
<div>
<h2 class="text-display font-display text-on-background">Welcome back, <?= $userName ?></h2>
<p class="text-body-lg text-secondary mt-base">Here is your academic overview for today, October 24th.</p>
</div>
</div>
</section>

<?php if ($joinSuccess): ?>
<div class="mb-lg rounded-lg border border-primary-container bg-secondary-container px-lg py-md" role="status">
<p class="font-body-md text-body-md text-primary font-semibold"><?= htmlspecialchars((string) $joinSuccess, ENT_QUOTES, 'UTF-8') ?></p>
</div>
<?php endif; ?>

<?php if (!empty($joinErrors)): ?>
<div class="mb-lg rounded-lg border border-error bg-error-container/30 px-md py-sm" role="alert">
<p class="font-label-md text-label-md text-error mb-xs font-bold">Could not join class:</p>
<ul class="list-disc pl-lg space-y-xs font-body-sm text-body-sm text-on-error-container">
<?php foreach ($joinErrors as $error): ?>
<li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<!-- Join Class -->
<section class="mb-lg">
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl card-shadow p-lg">
<h3 class="text-h3 font-h3 text-on-surface mb-sm">Join a Class</h3>
<p class="text-body-sm text-secondary mb-md">Enter the class code provided by your teacher.</p>
<form action="../../php/classes/join-class.php" method="POST" class="flex flex-col sm:flex-row gap-md">
<input class="flex-1 px-lg py-md border border-outline-variant rounded-lg font-body-md uppercase tracking-widest focus:ring-2 focus:ring-primary/10 focus:border-primary outline-none" id="class_code" name="class_code" placeholder="EDU-A1B2C3" required type="text" value="<?= $classCodeVal ?>" maxlength="20"/>
<button class="px-xl py-md bg-primary-container text-white font-label-md rounded-lg hover:bg-blue-800 transition-colors active:scale-95" type="submit">
Join Class
</button>
</form>
</div>
</section>

<!-- Enrolled Classes -->
<div class="grid grid-cols-1 md:grid-cols-12 gap-lg">
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl card-shadow overflow-hidden flex flex-col md:col-span-12">
<div class="px-lg py-md border-b border-outline-variant flex justify-between items-center bg-surface-bright">
<h3 class="text-h3 font-h3 text-on-surface">My Classes</h3>
<span class="text-label-sm font-label-sm text-secondary uppercase"><?= $enrolledCount ?> ACTIVE COURSE<?= $enrolledCount === 1 ? '' : 'S' ?></span>
</div>
<div class="p-lg space-y-md">
<?php if (empty($enrolledClasses)): ?>
<p class="text-body-md text-on-surface-variant text-center py-lg">You are not enrolled in any classes yet. Use a class code above to join.</p>
<?php else: ?>
<?php foreach ($enrolledClasses as $class): ?>
<?php
    $className = htmlspecialchars((string) $class['ClassName'], ENT_QUOTES, 'UTF-8');
    $classCode = htmlspecialchars((string) $class['ClassCode'], ENT_QUOTES, 'UTF-8');
    $teacherName = htmlspecialchars((string) $class['TeacherName'], ENT_QUOTES, 'UTF-8');
?>
<div class="flex items-center justify-between p-md border border-outline-variant rounded-lg hover:border-primary-container transition-colors">
<div class="flex flex-col flex-1">
    
    <div class="flex items-center justify-between">
        <h4 class="text-body-lg font-bold text-on-surface">
            <?= $className ?>
        </h4>

        <span class="text-primary font-bold text-body-md">
            <?= (int) ($class['AttendancePercentage'] ?? 0) ?>%
        </span>
    </div>

    <p class="text-body-sm text-secondary">
        <?= $classCode ?> &bull; Teacher: <?= $teacherName ?>
    </p>

</div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>
</div>
</div>
</main>
</div>
</div>
</body>
<script src="../../assets/js/app.js"></script>
</html>
