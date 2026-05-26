<?php
session_start();

$errors = $_SESSION['register_errors'] ?? [];
$old = $_SESSION['register_old'] ?? [];
unset($_SESSION['register_errors'], $_SESSION['register_old']);

$fullName = htmlspecialchars((string) ($old['full_name'] ?? ''), ENT_QUOTES, 'UTF-8');
$emailVal = htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8');
$roleOld = (string) ($old['role'] ?? 'student');
$isStudent = $roleOld !== 'teacher';
$isTeacher = $roleOld === 'teacher';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>EduAttend - Registration</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="../../assets/js/tailwind-config.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="../../assets/css/app.css"/>
</head>
<body class="bg-background text-on-background min-h-screen">
<main class="flex min-h-screen flex-col lg:flex-row">
<div class="flex-1 flex items-center justify-center p-lg lg:p-xl bg-surface">
<div class="w-full max-w-[440px]">
<div class="mb-xl">
<div class="flex items-center gap-md mb-sm">
<div class="bg-primary-container p-2 rounded-lg">
<span class="material-symbols-outlined text-white text-[24px]">school</span>
</div>
<h1 class="font-display text-h1 text-primary tracking-tight">EduAttend</h1>
</div>
<h2 class="font-h2 text-h2 text-on-surface">Create your account</h2>
<p class="font-body-md text-body-md text-on-surface-variant mt-xs">Online Web-Based Student Attendance Management System</p>
</div>

<?php if (!empty($errors)): ?>
<div class="mb-lg rounded-lg border border-error bg-error-container/30 px-md py-sm" role="alert">
<p class="font-label-md text-label-md text-error mb-xs font-bold">Please fix the following:</p>
<ul class="list-disc pl-lg space-y-xs font-body-sm text-body-sm text-on-error-container">
<?php foreach ($errors as $error): ?>
<li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<form action="../../php/auth/register.php" method="POST" class="space-y-lg">
<div class="space-y-xs">
<label class="font-label-md text-label-md text-on-surface" for="full_name">Full Name</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">person</span>
<input class="w-full pl-10 pr-md py-sm border border-outline-variant rounded-lg font-body-sm text-body-sm bg-white input-focus-halo transition-all" id="full_name" name="full_name" placeholder="John Doe" required type="text" value="<?= $fullName ?>"/>
</div>
</div>
<div class="space-y-xs">
<label class="font-label-md text-label-md text-on-surface" for="email">Institution Email</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">mail</span>
<input class="w-full pl-10 pr-md py-sm border border-outline-variant rounded-lg font-body-sm text-body-sm bg-white input-focus-halo transition-all" id="email" name="email" placeholder="john.doe@university.edu" required type="email" value="<?= $emailVal ?>"/>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-md">
<div class="space-y-xs">
<label class="font-label-md text-label-md text-on-surface" for="password">Password</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">lock</span>
<input class="w-full pl-10 pr-md py-sm border border-outline-variant rounded-lg font-body-sm text-body-sm bg-white input-focus-halo transition-all" id="password" name="password" placeholder="••••••••" required type="password" minlength="8"/>
</div>
</div>
<div class="space-y-xs">
<label class="font-label-md text-label-md text-on-surface" for="confirm_password">Confirm Password</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">lock_reset</span>
<input class="w-full pl-10 pr-md py-sm border border-outline-variant rounded-lg font-body-sm text-body-sm bg-white input-focus-halo transition-all" id="confirm_password" name="confirm_password" placeholder="••••••••" required type="password" minlength="8"/>
</div>
</div>
</div>
<div class="space-y-xs">
<span class="font-label-md text-label-md text-on-surface">Select Your Role</span>
<div class="grid grid-cols-2 gap-md">
<label class="relative flex items-center p-md border border-outline-variant rounded-lg cursor-pointer hover:bg-surface-container transition-colors group">
<input class="w-4 h-4 text-primary border-outline-variant focus:ring-primary" name="role" type="radio" value="student"<?= $isStudent ? ' checked' : '' ?>/>
<span class="ml-sm font-label-md text-label-md text-on-surface group-hover:text-primary">Student</span>
</label>
<label class="relative flex items-center p-md border border-outline-variant rounded-lg cursor-pointer hover:bg-surface-container transition-colors group">
<input class="w-4 h-4 text-primary border-outline-variant focus:ring-primary" name="role" type="radio" value="teacher"<?= $isTeacher ? ' checked' : '' ?>/>
<span class="ml-sm font-label-md text-label-md text-on-surface group-hover:text-primary">Teacher</span>
</label>
</div>
</div>
<div class="space-y-md pt-md">
<button class="w-full bg-primary-container text-white py-sm rounded-lg font-label-md text-label-md font-bold hover:bg-[#1e3a8a] active:scale-95 transition-all" type="submit">
Register
</button>
<div class="text-center">
<p class="font-body-sm text-body-sm text-on-surface-variant">
Already have an account?
<a class="text-primary font-semibold hover:underline" href="login.php">Login</a>
</p>
</div>
</div>
</form>
<div class="mt-xl pt-lg border-t border-outline-variant">
<div class="flex items-center gap-xs opacity-60">
<span class="material-symbols-outlined text-[16px]">verified_user</span>
<span class="font-label-sm text-label-sm uppercase tracking-widest text-secondary">Secured Academic Network</span>
</div>
</div>
</div>
</div>
<div class="hidden lg:flex flex-1 relative bg-primary items-center justify-center overflow-hidden">
<div class="absolute inset-0 opacity-10">
<svg class="h-full w-full" fill="none" preserveAspectRatio="none" viewBox="0 0 100 100">
<defs>
<pattern height="10" id="grid" patternUnits="userSpaceOnUse" width="10">
<path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"></path>
</pattern>
</defs>
<rect fill="url(#grid)" height="100" width="100"></rect>
</svg>
</div>
<div class="absolute top-0 right-0 w-96 h-96 bg-primary-fixed-dim/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
<div class="absolute bottom-0 left-0 w-96 h-96 bg-primary-container/40 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
<div class="relative z-10 max-w-xl p-xl">
<div class="space-y-xl">
<div>
<span class="inline-flex items-center px-3 py-1 rounded-full bg-white/10 text-white font-label-sm border border-white/20 backdrop-blur-sm mb-lg">
INTELLIGENT CAMPUS
</span>
<h2 class="font-display text-[48px] leading-[1.1] text-white font-bold mb-md">
Modernizing attendance for the next generation.
</h2>
<p class="text-white/80 font-body-lg text-lg leading-relaxed">
Join thousands of students and educators using EduAttend to streamline academic tracking with security and precision.
</p>
</div>
<div class="grid grid-cols-2 gap-lg">
<div class="bg-white/10 p-lg rounded-xl border border-white/10 backdrop-blur-md">
<span class="material-symbols-outlined text-white text-[32px] mb-sm">assessment</span>
<h3 class="font-h3 text-white mb-xs">Real-time Tracking</h3>
<p class="font-body-sm text-white/60">Instant synchronization between devices for 100% data integrity.</p>
</div>
<div class="bg-white/10 p-lg rounded-xl border border-white/10 backdrop-blur-md">
<span class="material-symbols-outlined text-white text-[32px] mb-sm">verified</span>
<h3 class="font-h3 text-white mb-xs">Secure Auth</h3>
<p class="font-body-sm text-white/60">Role-based access control protecting verified academic records.</p>
</div>
</div>
</div>
</div>
</div>
</main>
<script src="../../assets/js/app.js"></script>
</body>
</html>
