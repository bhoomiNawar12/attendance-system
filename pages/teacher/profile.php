<?php
$requiredRole = 'Teacher';
require_once __DIR__ . '/../../php/auth/session-check.php';
$userName = htmlspecialchars((string) $_SESSION['Name'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>EduAttend - Teacher Profile</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="../../assets/js/tailwind-config.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="../../assets/css/app.css"/>
</head><body class="bg-background text-on-surface antialiased">
<!-- Sidebar Navigation Shell -->
<aside id="app-sidebar" class="w-[280px] h-screen fixed left-0 top-0 bg-surface-low dark:bg-surface-container-lowest border-r border-outline-variant dark:border-outline flex flex-col h-full py-lg">
<div class="px-xl mb-xl">
<h1 class="font-h2 text-h2 font-bold text-primary dark:text-primary-fixed">EduAttend</h1>
<p class="font-label-md text-label-md text-secondary">Academic Portal</p>
</div>
<nav class="flex-grow">
<ul class="space-y-xs px-md">
<li>
<a class="flex items-center gap-md px-lg py-sm rounded-lg text-secondary dark:text-secondary-fixed-dim hover:bg-secondary-container transition-colors font-label-md text-label-md" href="dashboard.php">
<span class="material-symbols-outlined">dashboard</span>
<span>Dashboard</span>
</a>
</li>
<li>
<a class="flex items-center gap-md px-lg py-sm rounded-lg text-secondary dark:text-secondary-fixed-dim hover:bg-secondary-container transition-colors font-label-md text-label-md" href="mark-attendance.php">
<span class="material-symbols-outlined">how_to_reg</span>
<span>Mark Attendance</span>
</a>
</li>
<li>
<a class="flex items-center gap-md px-lg py-sm rounded-lg text-secondary dark:text-secondary-fixed-dim hover:bg-secondary-container transition-colors font-label-md text-label-md" href="consolidated-report.php">
<span class="material-symbols-outlined">assessment</span>
<span>View Report</span>
</a>
</li>
<li>
<a class="flex items-center gap-md px-lg py-sm rounded-lg text-primary dark:text-primary-fixed font-bold border-l-4 border-primary dark:border-primary-fixed bg-secondary-container/30 transition-colors font-label-md text-label-md" href="profile.php">
<span class="material-symbols-outlined">person</span>
<span>Profile</span>
</a>
</li>
</ul>
</nav>
<div class="px-xl pt-lg border-t border-outline-variant">
<div class="p-md rounded-lg bg-surface-container-high/50">
<p class="font-label-sm text-label-sm text-primary font-bold">System Status: Active</p>
</div>
</div>
</aside>
<!-- Main Content Area -->
<main class="ml-[280px] min-h-screen flex flex-col bg-background">
<!-- Top Navigation Bar -->
<header class="teacher-fixed-header fixed top-0 right-0 w-[calc(100%-280px)] h-16 bg-surface dark:bg-background border-b border-outline-variant dark:border-outline shadow-sm dark:shadow-none flex justify-between items-center px-xl z-10">
<div class="flex items-center gap-md bg-surface-container-low px-md py-xs rounded-full border border-outline-variant">
<span class="material-symbols-outlined text-outline">search</span>
<input class="bg-transparent border-none focus:ring-0 text-body-sm font-body-sm w-64" placeholder="Search profiles..." type="text"/>
</div>
<div class="flex items-center gap-lg">
<div class="flex items-center gap-md">
<span class="material-symbols-outlined text-on-surface-variant cursor-pointer active:scale-95 transition-all">notifications</span>
<span class="material-symbols-outlined text-on-surface-variant cursor-pointer active:scale-95 transition-all">help_outline</span>
</div>
<div class="h-8 w-8 rounded-full bg-primary-container flex items-center justify-center overflow-hidden border border-outline-variant">
<img alt="User Avatar" class="h-full w-full object-cover" data-alt="A professional headshot of an academic professor in his 40s wearing glasses and a navy blue blazer. The background is a soft-focus library environment with warm lighting, creating an atmosphere of expertise and institutional authority. The image has a clean, high-resolution aesthetic with neutral, modern colors consistent with a professional portal." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAnzv4EU05OL2adIEetqgXIXs9mNHy5oYm_gZUVhpI_Pp0TUvJp0Q1UwU-G0ElEJIpr3zTudnOpYxKsReUN9Qb6YAgaKcbUI2H0xqg2wSrSZMhDqwe_wnHpGoJwHVxtWu-_WLGdyAgOJ5lnBzUB6JgD2dP676qwQJn6Ewrfse_noW_6Xnv2QZn9Z_8lrT-D5VoIbQWKV3gNCMjdgA7TiYmdNoJOhK6YgWO3GyeLOTc5GaAGrtutHUu9gTzo0i9QRbJklvsPDXpfKw"/>
</div>
</div>
</header>
<!-- Profile Content -->
<div class="mt-16 p-xl flex-grow flex items-center justify-center">
<div class="max-w-md w-full bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<!-- Header Decorative Area -->
<div class="h-24 bg-primary relative">
<div class="absolute -bottom-12 left-1/2 -translate-x-1/2">
<div class="h-24 w-24 rounded-full border-4 border-white bg-surface shadow-md overflow-hidden">
<img alt="Prof. Anderson" class="h-full w-full object-cover" data-alt="Close-up portrait of a male professor with a friendly expression, wearing a crisp white shirt and dark blazer. The lighting is bright and even, reflecting a modern corporate minimalism style. The background is a solid light gray, ensuring the subject stands out clearly as the focus of a teacher profile page." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCJv2ZM32NUYkYqoxdsQ6FMRBlxRqxmTWtNpCDGwZORGQUFYqPgvh9TMTNJ_aIOqMLzIBUMFYgY52z0RU9vCjxVLOYOJEW0MBrDhe-N9RWhPYnxfRcL1H6OLqU-cLExRCAI-057tJCcb2JxDb47m4NgHZCkuqcnWOXGYIIr1pPw_caAXPDTo6CnB9RA63pgMhfiaX_lR2eaZ3mK_CZ4-BLrEGjOSwG36d_UJjSPTrw5V2JGB12ZKC5ggd4IU92xssYQxdEEoZA1Ng"/>
</div>
</div>
</div>
<!-- Profile Details -->
<div class="pt-16 pb-lg px-xl text-center">
<h2 class="font-h2 text-h2 text-on-surface mb-xs"><?= $userName ?></h2>
<div class="space-y-md border-f1f5f9 text-center">
<div class="flex items-center gap-md justify-center">
<span class="material-symbols-outlined text-primary">mail</span>
<div>
<p class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Email Address</p>
<p class="font-body-md text-body-md text-on-surface">anderson@university.edu</p>
</div>
</div>
<div class="flex items-center gap-md justify-center">
<span class="material-symbols-outlined text-primary">apartment</span>
<div>
<p class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Department</p>
<p class="font-body-md text-body-md text-on-surface">Computer Science &amp; Engineering</p>
</div>
</div>
</div>
<div class="mt-xl pt-xl flex flex-col gap-md">
<a href="../../php/auth/logout.php" class="w-full py-md bg-white border border-outline-variant rounded-lg font-label-md text-label-md text-error hover:bg-error/5 transition-colors flex items-center justify-center gap-sm">
<span class="material-symbols-outlined">logout</span>
                            Logout
                        </a>
</div>
</div>
</div>
</div>
<!-- Footer / Credits -->
<footer class="p-xl text-center">
<p class="font-label-sm text-label-sm text-outline">© 2024 EduAttend Institutional Management System. All rights reserved.</p>
</footer>
</main>
</body>
<script src="../../assets/js/app.js"></script>
</html>
