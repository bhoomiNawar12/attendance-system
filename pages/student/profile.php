<?php
$requiredRole = 'Student';
require_once __DIR__ . '/../../php/auth/session-check.php';
$userName = htmlspecialchars((string) $_SESSION['Name'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Student Profile - Academic Portal</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="../../assets/js/tailwind-config.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="../../assets/css/app.css"/>
</head><body class="bg-background text-on-background flex min-h-screen">
<!-- SideNavBar Component -->
<aside id="app-sidebar" class="w-sidebar-width h-screen sticky top-0 left-0 bg-surface-container-low border-r border-outline-variant flex flex-col py-xl">
<div class="px-xl mb-xl">
<h1 class="text-h2 font-h2 text-primary">Student Portal</h1>
<p class="font-body-md text-secondary">Academic Management</p>
</div>
<nav class="flex-grow">
<ul class="space-y-base">
<li>
<a class="flex items-center px-xl py-md text-secondary font-body-md hover:bg-surface-container-high transition-colors cursor-pointer group" href="dashboard.php">
<span class="material-symbols-outlined mr-md group-hover:text-primary">dashboard</span>
<span>Dashboard</span>
</a>
</li>
<li>
<a class="flex items-center px-xl py-md text-secondary font-body-md hover:bg-surface-container-high transition-colors cursor-pointer group" href="attendance-report.php">
<span class="material-symbols-outlined mr-md group-hover:text-primary">assessment</span>
<span>View Report</span>
</a>
</li>
<li>
<a class="flex items-center px-xl py-md text-primary font-bold border-l-4 border-primary bg-secondary-container/20 transition-all duration-200 cursor-pointer" href="profile.php">
<span class="material-symbols-outlined mr-md" style="font-variation-settings: 'FILL' 1;">person</span>
<span>Profile</span>
</a>
</li>
</ul>
</nav>
<div class="px-xl mt-auto pt-xl border-t border-outline-variant">
<div class="flex items-center gap-md">
<img alt="Alex Rivera" class="w-10 h-10 rounded-full bg-surface-variant" data-alt="A clean, professional headshot of a young male student named Alex Rivera with a friendly expression. The lighting is bright and even, consistent with a modern university campus ID style. The background is a soft, blurred academic setting with institutional blue tones." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBt4V9PWPjr0sOM27HkVC6pt3_KEAzeOeOi7K-CNdvbrSnH2LOniKCsb-NP-0c6G_UFhUxAQvd7icWWcHSx1vhSD7lpj_B5pCrghKWhQFViWvGyBgLmKHDNwyNCSFOPMBXt_XZUY2tNqTOwqGCbpgFUPBU2KyyC7AYLuL3kf-Y5pzGW93izQQsrLI2yBRx8KApangA3VnM3GugcghxkF0f0399-eyRIgVimvUMrU411KHggXwIlIWmZo8VyodsXcdGrUt0Bk_UzJA"/>
<div class="overflow-hidden">
<p class="font-label-md text-on-surface truncate"><?= $userName ?></p>
<p class="text-xs text-on-surface-variant truncate">Student</p>
</div>
</div>
</div>
</aside>
<!-- Main Content Area -->
<main class="flex-grow flex flex-col max-w-[calc(1440px-280px)]">
<!-- TopAppBar Component -->
<header class="flex justify-between items-center h-16 px-xl w-full bg-surface border-b border-outline-variant sticky top-0 z-10">
<div class="flex items-center">
<span class="text-h3 font-h3 text-primary">Profile</span>
</div>
<div class="flex items-center gap-xl">
<div class="flex items-center gap-lg">
<button class="text-on-surface-variant hover:text-primary transition-colors cursor-pointer flex items-center">
<span class="material-symbols-outlined">notifications</span>
</button>
<button class="text-on-surface-variant hover:text-primary transition-colors cursor-pointer flex items-center">
<span class="material-symbols-outlined">settings</span>
</button>
</div>
<div class="h-8 w-[1px] bg-outline-variant"></div>
<img alt="User Avatar" class="w-8 h-8 rounded-full border border-outline-variant" data-alt="A close-up portrait of Alex Rivera, a university student, for a profile interface. He has a warm smile and is wearing a simple navy blue polo shirt. The image is crisp, with a minimalist, high-key light mode background that emphasizes professionalism and clarity." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBhcwxl3uBJS7W5MuPZ9vzzBbRlqAF_P63TV-6q1dY3oE5WtIwYGEtMirdofV05OXLMsQ3zQQ0V2CQRUaa_2fLCwCKNuaqF1_dwWhHdQVekgjP1j1KoBg4YsyCkynGU3VMvtGpPtJFIg_-gSD9W7j8FRqhp59-KZ-O_5X3uy5A1UmC5MQyjZiCyBFJZl8QT9pywDrXE9--QKAWFSGF3VmiyYDXzR8a-nqCn2D2rf6syShGvcqzS2V8OIi9Sob0oN4zRk1fcv5n6-w"/>
</div>
</header>
<!-- Canvas Area -->
<section class="p-xl flex flex-col items-center justify-center min-h-[calc(100vh-64px)] bg-background">
<!-- Profile Card -->
<div class="w-full max-w-md bg-surface-container-lowest rounded-xl border border-outline-variant shadow-[0_1px_3px_0_rgba(0,0,0,0.1),0_1px_2px_-1px_rgba(0,0,0,0.1)] overflow-hidden">
<div class="p-xl flex flex-col items-center text-center">
<!-- Profile Photo -->
<div class="relative mb-lg">
<div class="w-32 h-32 rounded-full border-4 border-surface overflow-hidden shadow-lg bg-surface-variant">
<img alt="Alex Rivera Large" class="w-full h-full object-cover" data-alt="A high-quality, professional profile photograph of a student named Alex Rivera. He has a clean-cut appearance and is looking directly at the camera with a confident, welcoming expression. The lighting is soft and studio-quality against a neutral, light-gray background, reflecting a formal institutional aesthetic." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDWyIgP3f0xniv0yqvcmppo6ODwyjZ6OHL2nMmPt18Jy1_Tb6pagdybbPxSCPeq4xQgUoAATu7cotyyTBfIQzxsLt3AQb-pVb5pQmWVrc7umE4eqfkuuWsgGiOkgSH1iTGDb1Hh2wTbGlg1eJPtV22faQ7n0BCdwbry9NCuPqz9v7eiDbp9m374lELdI7sl9vmn6Clc8XkEyluHWsnkE42dXAeVhDZSzrLHl0zHWsy3c9EyvcBr9y1M6xudQcxLsGO71mWHS5GsRQ"/>
</div>
<div class="absolute bottom-1 right-1 w-6 h-6 bg-green-500 border-2 border-surface rounded-full shadow-sm"></div>
</div>
<!-- User Identity -->
<div class="space-y-xs">
<h2 class="font-h2 text-h2 text-on-surface"><?= $userName ?></h2>
<p class="font-body-md text-secondary">alex.rivera@university.edu</p>
</div>
<!-- Actions -->
<div class="mt-xl w-full border-t border-outline-variant pt-xl">
<a href="../../php/auth/logout.php" class="w-full flex items-center justify-center gap-md py-md px-lg bg-white border border-outline-variant text-secondary font-label-md rounded-lg hover:bg-surface-container-low transition-colors duration-200">
<span class="material-symbols-outlined text-error">logout</span>
<span class="text-on-surface-variant">Logout</span>
</a>
</div>
</div>
<!-- Footer Decoration -->
<div class="bg-surface-container-low px-xl py-md flex justify-center items-center">
<p class="text-xs font-label-sm text-outline uppercase tracking-wider">Session Active</p>
</div>
</div>
<!-- Supporting Text / Contextual Note -->
<div class="mt-lg text-center max-w-xs">
<p class="font-body-sm text-on-surface-variant">Your profile information is managed by the University Registrar. Contact support for any changes.</p>
</div>
</section>
</main>
</body>
<script src="../../assets/js/app.js"></script>
</html>
