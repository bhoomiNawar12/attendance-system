# Converts Stitch code.html exports into organized pages/
$root = Split-Path -Parent $PSScriptRoot
$stitch = Join-Path $root "stitch_academic_attendance_management_system"
$assetsPrefix = "../../assets"

$headTemplate = @'
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>{0}</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="{1}/js/tailwind-config.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="{1}/css/app.css"/>
</head>
'@

$pages = @(
    @{ Src = "login_web_layout\code.html"; Dest = "pages\auth\login.html"; Title = "EduAttend - Login" },
    @{ Src = "registration_web_layout\code.html"; Dest = "pages\auth\register.html"; Title = "EduAttend - Registration" },
    @{ Src = "student_dashboard\code.html"; Dest = "pages\student\dashboard.html"; Title = "EduAttend - Student Dashboard" },
    @{ Src = "student_attendance_matrix_report\code.html"; Dest = "pages\student\attendance-report.html"; Title = "Student Attendance Matrix" },
    @{ Src = "student_profile\code.html"; Dest = "pages\student\profile.html"; Title = "Student Profile - Academic Portal" },
    @{ Src = "teacher_dashboard\code.html"; Dest = "pages\teacher\dashboard.html"; Title = "EduAttend - Teacher Dashboard" },
    @{ Src = "teacher_create_class\code.html"; Dest = "pages\teacher\create-class.html"; Title = "EduAttend - Create Class" },
    @{ Src = "teacher_profile\code.html"; Dest = "pages\teacher\profile.html"; Title = "EduAttend - Teacher Profile" },
    @{ Src = "teacher_simplified_mark_attendance\code.html"; Dest = "pages\teacher\mark-attendance.html"; Title = "EduAttend - Mark Attendance" },
    @{ Src = "teacher_simplified_consolidated_report\code.html"; Dest = "pages\teacher\consolidated-report.html"; Title = "EduAttend - Teacher Consolidated Report" }
)

$studentNav = @{
    "dashboard.html" = @("dashboard.html", "attendance-report.html", "profile.html")
    "attendance-report.html" = @("dashboard.html", "attendance-report.html", "profile.html")
    "profile.html" = @("dashboard.html", "attendance-report.html", "profile.html")
}

$teacherNav = @{
    "dashboard.html" = @("dashboard.html", "mark-attendance.html", "consolidated-report.html", "profile.html")
    "mark-attendance.html" = @("dashboard.html", "mark-attendance.html", "consolidated-report.html", "profile.html")
    "consolidated-report.html" = @("dashboard.html", "mark-attendance.html", "consolidated-report.html", "profile.html")
    "profile.html" = @("dashboard.html", "mark-attendance.html", "consolidated-report.html", "profile.html")
    "create-class.html" = @("dashboard.html", "mark-attendance.html", "consolidated-report.html", "profile.html")
}

function Get-BodyHtml([string]$html) {
    if ($html -match '(?s)<body\b([^>]*)>(.*)</body>') {
        return "<body$($matches[1])>$($matches[2])</body>"
    }
    return $html
}

function Clean-StitchArtifacts([string]$body) {
    $body = $body -replace '<style data-stitch[^>]*>.*?</style>', ''
    $body = $body -replace '(?s)<script id="tailwind-config">.*?</script>', ''
    $body = $body -replace '<link href="https://fonts.googleapis.com/css2\?family=Material\+Symbols\+Outlined[^"]*"\s*rel="stylesheet"/>\s*', ''
    return $body
}

function Add-SidebarId([string]$body) {
    if ($body -match '<aside\b' -and $body -notmatch 'id="app-sidebar"') {
        $body = $body -replace '<aside\b', '<aside id="app-sidebar"'
    }
    if ($body -match 'w-\[calc\(100%-280px\)\]' -and $body -notmatch 'teacher-fixed-header') {
        $body = $body -replace 'class="fixed top-0 right-0 w-\[calc\(100%-280px\)\]', 'class="teacher-fixed-header fixed top-0 right-0 w-[calc(100%-280px)]'
    }
    if ($body -match 'flex-1 flex flex-col min-w-0' -and $body -notmatch 'app-layout-main') {
        $body = $body -replace 'class="flex-1 flex flex-col min-w-0"', 'class="app-layout-main flex-1 flex flex-col min-w-0 lg:ml-[280px]"'
    }
    return $body
}

function Fix-StudentNav([string]$body, [string]$fileName) {
    $links = $studentNav[$fileName]
    if (-not $links) { return $body }
    $i = 0
    return [regex]::Replace($body, '(?s)(<nav[^>]*>)(.*?)(</nav>)', {
        param($m)
        $navInner = $m.Groups[2].Value
        $navInner = [regex]::Replace($navInner, 'href="#"', {
            param($h)
            $href = $links[$script:i]
            $script:i++
            return "href=""$href"""
        })
        return $m.Groups[1].Value + $navInner + $m.Groups[3].Value
    }, 1)
}

function Fix-TeacherNav([string]$body, [string]$fileName) {
    $links = $teacherNav[$fileName]
    if (-not $links) { return $body }
    $i = 0
    return [regex]::Replace($body, '(?s)(<nav[^>]*>)(.*?)(</nav>)', {
        param($m)
        $navInner = $m.Groups[2].Value
        $navInner = [regex]::Replace($navInner, 'href="#"', {
            param($h)
            if ($script:i -ge $links.Count) { return $h.Value }
            $href = $links[$script:i]
            $script:i++
            return "href=""$href"""
        })
        return $m.Groups[1].Value + $navInner + $m.Groups[3].Value
    }, 1)
}

foreach ($page in $pages) {
    $srcPath = Join-Path $stitch $page.Src
    $destPath = Join-Path $root $page.Dest
    $destDir = Split-Path $destPath -Parent
    if (-not (Test-Path $destDir)) { New-Item -ItemType Directory -Path $destDir -Force | Out-Null }

    $html = Get-Content $srcPath -Raw -Encoding UTF8
    $body = Get-BodyHtml $html
    $body = Clean-StitchArtifacts $body
    $body = Add-SidebarId $body

    $fileName = Split-Path $page.Dest -Leaf
    if ($page.Dest -like "pages\student\*") {
        $body = Fix-StudentNav $body $fileName
    }
    if ($page.Dest -like "pages\teacher\*") {
        $body = Fix-TeacherNav $body $fileName
    }

    if ($page.Dest -like "pages\auth\login.html") {
        $body = $body -replace 'href="#">Register</a>', 'href="register.html">Register</a>'
        $body = $body -replace 'action="#"', 'action="../../php/auth/login.php"'
    }
    if ($page.Dest -like "pages\auth\register.html") {
        $body = $body -replace 'href="#">Login</a>', 'href="login.html">Login</a>'
        $body = $body -replace 'action="#"', 'action="../../php/auth/register.php"'
    }

    $head = $headTemplate -f $page.Title, $assetsPrefix
    $footer = "`n<script src=`"$assetsPrefix/js/app.js`"></script>`n</html>"
    $out = $head + $body + $footer
    $out = $out -replace '</body></html>$', '</body>'
    if ($out -notmatch '</html>') { $out += "`n</html>" }

    [System.IO.File]::WriteAllText($destPath, $out, [System.Text.UTF8Encoding]::new($false))
    Write-Host "Built: $($page.Dest)"
}

Write-Host "Done."
