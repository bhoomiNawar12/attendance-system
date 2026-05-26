<?php
session_start();

$errors = $_SESSION['login_errors'] ?? [];
$old = $_SESSION['login_old'] ?? [];
unset($_SESSION['login_errors'], $_SESSION['login_old']);

$emailVal = htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8');
$showRegistered = isset($_GET['registered']) && $_GET['registered'] === '1';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>EduAttend - Login</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="../../assets/js/tailwind-config.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="../../assets/css/app.css"/>
</head>
<body class="bg-background text-on-background min-h-screen flex">
<div class="flex w-full min-h-screen">
<main class="w-full lg:w-1/2 flex flex-col justify-between p-lg md:p-xl lg:p-[80px] bg-white overflow-y-auto">
<header class="flex items-center gap-sm mb-xl">
<span class="material-symbols-outlined text-primary text-[32px]">how_to_reg</span>
<h1 class="font-display text-[28px] text-primary tracking-tight font-bold">EduAttend</h1>
</header>
<div class="w-full max-w-[440px] mx-auto my-auto py-xl">
<section class="flex flex-col gap-xl">
<div class="flex flex-col gap-xs">
<h2 class="font-h1 text-h1 text-on-surface">Welcome back</h2>
<p class="font-body-md text-body-md text-on-surface-variant">Please enter your institutional credentials to continue.</p>
</div>

<?php if ($showRegistered): ?>
<div class="rounded-lg border border-primary-container bg-secondary-container px-lg py-md" role="status">
<p class="font-body-md text-body-md text-primary font-semibold">Registration successful! You can now log in.</p>
</div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
<div class="rounded-lg border border-error bg-error-container/30 px-md py-sm" role="alert">
<p class="font-label-md text-label-md text-error mb-xs font-bold">Login failed</p>
<ul class="list-disc pl-lg space-y-xs font-body-sm text-body-sm text-on-error-container">
<?php foreach ($errors as $error): ?>
<li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<form action="../../php/auth/login.php" method="POST" class="flex flex-col gap-lg">
<div class="flex flex-col gap-xs">
<label class="font-label-md text-label-md text-on-surface-variant" for="email">Email Address</label>
<input class="w-full px-lg py-md bg-white border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline input-focus-halo transition-all" id="email" name="email" placeholder="name@university.edu" required type="email" value="<?= $emailVal ?>"/>
</div>
<div class="flex flex-col gap-xs">
<div class="flex justify-between items-center">
<label class="font-label-md text-label-md text-on-surface-variant" for="password">Password</label>

</div>
<input class="w-full px-lg py-md bg-white border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline input-focus-halo transition-all" id="password" name="password" placeholder="••••••••" required type="password"/>
</div>
<div class="flex items-center gap-sm">
<input class="w-4 h-4 text-primary border-outline-variant rounded focus:ring-primary transition-all" id="remember" name="remember" type="checkbox"/>
<label class="font-body-sm text-body-sm text-on-surface-variant cursor-pointer" for="remember">Remember this device</label>
</div>
<button class="w-full bg-primary hover:bg-[#1e3a8a] text-white font-label-md text-label-md py-lg rounded-lg transition-all active:scale-[0.98] focus:ring-2 focus:ring-primary focus:ring-offset-2 shadow-sm" type="submit">
Login
</button>
</form>
<div class="pt-lg text-center">
<p class="font-body-md text-body-md text-on-surface-variant">
Don't have an account?
<a class="text-primary font-bold hover:underline" href="register.php">Register</a>
</p>
</div>
</section>
</div>
<footer class="mt-auto pt-xl">
<div class="flex flex-col md:flex-row justify-between items-center gap-md border-t border-outline-variant pt-lg">
<div class="flex items-center gap-xs px-md py-xs bg-secondary-container rounded-full">
<span class="material-symbols-outlined text-[14px] text-on-secondary-container" style="font-variation-settings: 'FILL' 1;">verified_user</span>
<span class="font-label-sm text-label-sm text-on-secondary-container">System Status: Active</span>
</div>
<div class="text-center md:text-right">
<p class="font-label-sm text-label-sm text-outline">© 2024 EduAttend. All rights reserved.</p>
</div>
</div>
</footer>
</main>
<aside class="hidden lg:flex w-1/2 relative overflow-hidden bg-surface-container">
<img alt="University Campus Architecture" class="absolute inset-0 w-full h-full object-cover grayscale opacity-90" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAiLx8l74VnF8S3ce-V0RXexdVpQwglqBeRqTVDhVK5Rumy04SgCEI9YYydgVhcqdzTcOBlzkg7ua7Y6xzpvowsD-VVJoX7Y5cGnZGZzo9BkEd2-eqAzLMuBlQ3nXiF5QDjpHJcAMF8RDoj9QgHOyRnkR7cthr2CUkh3dsmPHpEmcJbRqp76bau4p4kZFOWQ7StTzqmggSX7PaDNasorDm7yPH7ccypP-vq8LzPpoHU4nppSYzzNreUh2D_Dxob0DfL0y9mLLlMRA"/>
<div class="absolute inset-0 bg-gradient-to-t from-primary/60 via-transparent to-transparent"></div>
<div class="relative z-10 mt-auto p-[80px] text-white">
<h3 class="font-display text-[44px] leading-tight mb-md font-bold text-white drop-shadow-md">Securing Academic Excellence</h3>
<p class="font-body-lg text-body-lg text-white/90 max-w-[500px] italic">
"The function of education is to teach one to think intensively and to think critically."
</p>
</div>
</aside>
</div>
<script src="../../assets/js/app.js"></script>
</body>
</html>
