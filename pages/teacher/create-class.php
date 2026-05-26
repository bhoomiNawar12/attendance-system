<?php
$requiredRole = 'Teacher';
require_once __DIR__ . '/../../php/auth/session-check.php';
$userName = htmlspecialchars((string) $_SESSION['Name'], ENT_QUOTES, 'UTF-8');

$classErrors = $_SESSION['class_errors'] ?? [];
$classOld = $_SESSION['class_old'] ?? [];
unset($_SESSION['class_errors'], $_SESSION['class_old']);

$classNameVal = htmlspecialchars((string) ($classOld['class_name'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>EduAttend - Create Class</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="../../assets/js/tailwind-config.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="../../assets/css/app.css"/>
</head><body class="bg-surface text-on-surface font-body-md antialiased">
<!-- Sidebar (Shared Component) -->
<aside id="app-sidebar" class="w-[280px] h-screen fixed left-0 top-0 bg-surface-low dark:bg-surface-container-lowest border-r border-outline-variant dark:border-outline flex flex-col py-lg z-50">
<div class="px-lg mb-xl">
<h1 class="font-h2 text-h2 font-bold text-primary dark:text-primary-fixed">EduAttend</h1>
<p class="font-body-sm text-body-sm text-secondary">Academic Portal</p>
</div>
<nav class="flex-grow space-y-xs px-md">
<a class="flex items-center gap-md px-lg py-sm text-secondary dark:text-secondary-fixed-dim hover:bg-secondary-container transition-colors" href="dashboard.php">
<span class="material-symbols-outlined">dashboard</span>
<span class="font-label-md text-label-md">Dashboard</span>
</a>
<a class="flex items-center gap-md px-lg py-sm text-primary dark:text-primary-fixed font-bold border-l-4 border-primary dark:border-primary-fixed hover:bg-secondary-container transition-colors" href="create-class.php">
<span class="material-symbols-outlined">add_box</span>
<span class="font-label-md text-label-md">Create Class</span>
</a>
<a class="flex items-center gap-md px-lg py-sm text-secondary dark:text-secondary-fixed-dim hover:bg-secondary-container transition-colors" href="mark-attendance.php">
<span class="material-symbols-outlined">how_to_reg</span>
<span class="font-label-md text-label-md">Attendance</span>
</a>
<a class="flex items-center gap-md px-lg py-sm text-secondary dark:text-secondary-fixed-dim hover:bg-secondary-container transition-colors" href="consolidated-report.php">
<span class="material-symbols-outlined">assessment</span>
<span class="font-label-md text-label-md">Reports</span>
</a>
<a class="flex items-center gap-md px-lg py-sm text-secondary dark:text-secondary-fixed-dim hover:bg-secondary-container transition-colors" href="profile.php">
<span class="material-symbols-outlined">person</span>
<span class="font-label-md text-label-md">Profile</span>
</a>
</nav>
<div class="mt-auto px-lg pt-lg border-t border-outline-variant">
<div class="flex items-center gap-sm py-xs">
<div class="w-2 h-2 rounded-full bg-emerald-500"></div>
<span class="font-label-sm text-label-sm text-secondary">System Status: Active</span>
</div>
</div>
</aside>
<!-- Header (Shared Component) -->
<header class="teacher-fixed-header fixed top-0 right-0 w-[calc(100%-280px)] h-16 bg-surface dark:bg-background border-b border-outline-variant dark:border-outline shadow-sm dark:shadow-none z-40 flex justify-between items-center px-xl">
<div class="flex items-center gap-lg">
<!-- Removed Hamburger Menu Icon -->
</div>
<div class="flex items-center gap-lg">
<div class="flex gap-md">
<button class="material-symbols-outlined p-xs text-on-surface-variant hover:text-primary transition-colors cursor-pointer active:scale-95">notifications</button>
<button class="material-symbols-outlined p-xs text-on-surface-variant hover:text-primary transition-colors cursor-pointer active:scale-95">help_outline</button>
</div>
<div class="flex items-center gap-sm border-l border-outline-variant pl-lg">
<div class="text-right hidden lg:block">
<p class="font-label-md text-label-md leading-none"><?= $userName ?></p>
<p class="font-label-sm text-label-sm text-secondary">Teacher</p>
</div>
<img alt="User Avatar" class="w-10 h-10 rounded-full border-2 border-primary-fixed shadow-sm" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDb1OTsSyqUeGyQBfv0shvJlZZpJBERS_wa9n6E1h2pln7b8xd5gfP4vzXlwxmOsJx6QVAwqx4NjA4p46tgnoMsHUg4djBve71bDBBlQSEsy_Smqa-DXRs4gPZaYtYoxvHhOn9qKh7Nv_gUudNt5a8pidWT_7uZAIp2chsGb6Zj6Bh7-iGnqGiuDaPtzHQNQWyupX5BYJQrscWqvqHzGD46T4Du7ahyTHdnXPh7AuaRVps4XSzcE0OJSc8Z5EF0fNuFJPb37t-5JA"/>
</div>
</div>
</header>
<!-- Main Content Area -->
<main class="ml-[280px] pt-16 min-h-screen flex items-center justify-center bg-surface">
<div class="max-w-[container-max] w-full px-xl py-xl flex justify-center">
<!-- Create Class Card -->
<div class="w-full max-w-lg bg-white rounded-xl border border-outline-variant shadow-[0_1px_3px_0_rgba(0,0,0,0.1),0_1px_2px_-1px_rgba(0,0,0,0.1)] overflow-hidden">
<!-- Card Header -->
<div class="px-xl py-lg border-b border-outline-variant flex items-center gap-md bg-surface-container-low">
<div class="p-sm bg-primary-container rounded-lg">
<span class="material-symbols-outlined text-white" style="font-variation-settings: 'FILL' 1;">add_box</span>
</div>
<div>
<h2 class="font-h3 text-h3 text-on-surface">Create New Class</h2>
<p class="font-body-sm text-body-sm text-secondary">Establish a new academic section for the current semester.</p>
</div>
</div>
<?php if (!empty($classErrors)): ?>
<div class="px-xl pt-lg">
<div class="rounded-lg border border-error bg-error-container/30 px-md py-sm" role="alert">
<p class="font-label-md text-label-md text-error mb-xs font-bold">Please fix the following:</p>
<ul class="list-disc pl-lg space-y-xs font-body-sm text-body-sm text-on-error-container">
<?php foreach ($classErrors as $error): ?>
<li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
<?php endforeach; ?>
</ul>
</div>
</div>
<?php endif; ?>
<!-- Form Content -->
<form action="../../php/classes/create-class.php" method="POST" class="p-xl space-y-lg">
<p class="font-body-sm text-body-sm text-secondary">A unique class code (e.g. EDU-A1B2C3) is generated automatically when you create the class.</p>
<!-- Class Name -->
<div class="space-y-xs">
<label class="font-label-md text-label-md text-on-surface" for="class_name">Class Name</label>
<input class="w-full px-lg py-md border border-outline-variant rounded-lg font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all outline-none" id="class_name" name="class_name" placeholder="e.g. Advanced Linear Algebra" required type="text" value="<?= $classNameVal ?>"/>
</div>
<!-- Visual Divider & Actions -->
<div class="pt-lg border-t border-outline-variant flex flex-col sm:flex-row gap-lg">
<a class="flex-1 px-lg py-md border border-outline-variant text-secondary font-label-md text-label-md rounded-lg hover:bg-surface-container-low transition-colors text-center" href="dashboard.php">
                            Cancel
                        </a>
<button class="flex-1 px-lg py-md bg-primary-container text-white font-label-md text-label-md rounded-lg hover:bg-primary transition-all shadow-sm active:scale-95" type="submit">
                            Create Class
                        </button>
</div>
</form>
<!-- Footer Image/Branding -->
<div class="relative h-24 overflow-hidden border-t border-outline-variant">
<div class="absolute inset-0 bg-primary/5 mix-blend-multiply"></div>
<img alt="Institutional background" class="w-full h-full object-cover grayscale opacity-20" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCw-clt1luyoCn7up2M3tuA5dFdioMNnVLSG9uJt4LTo3Gf53jvr9bcWLKZ7HocOSfvkEjl0OOCVRYfC6rR7A2khpGeXoXdAs1XZr4Rdb-0teTPm0orYadSzgTt4_15-s6M9YaA5sWxiWpZtt8YwFTgVdbBpnygH_v7Xv1IvEMgxj_7uv5qJPS2Yo13tP5RO1rrhMpv2rGD1NZnf_8Bk-Gvxo4F-AGTyE5sAUq3doLMs1KIUhgX-WwJ4YEywyEh9Ga_dt-swUM_lQ"/>
<div class="absolute inset-0 flex items-center justify-center">
<span class="font-label-sm text-label-sm text-secondary uppercase tracking-[0.2em]">Institutional Integrity &amp; Focus</span>
</div>
</div>
</div>
</div>
</main>
</body>
<script src="../../assets/js/app.js"></script>
</html>
