# Web Based Attendance Management System

PHP-based academic attendance system for XAMPP (HTML, CSS, JavaScript, PHP, MySQL).

## Project structure

```
attendance-system/
├── index.html              # Site map / entry point
├── pages/                  # Frontend screens (pure HTML)
│   ├── auth/               # Login, register
│   ├── student/            # Student portal
│   └── teacher/            # Teacher portal
├── assets/
│   ├── css/app.css         # Shared styles
│   ├── js/
│   │   ├── tailwind-config.js
│   │   └── app.js          # Mobile nav, shared UI
│   └── images/             # Local images (add here)
├── php/                    # Backend (grows over time)
│   ├── auth/               # Login / register handlers
│   └── test.php
├── includes/               # PHP partials (header, sidebar) — future
├── database/               # SQL schema and migrations — future
├── docs/                   # Design system notes
├── stitch_export/          # Original Google Stitch export (reference)
└── scripts/                # Build helpers
```

## Quick start (XAMPP)

1. Place this folder in `C:\xampp\htdocs\attendance-system`
2. Start **Apache** (and **MySQL** when you add the database)
3. Open **http://localhost/attendance-system/**

## Frontend

- UI exported from **Google Stitch**, reorganized into `pages/`
- **Tailwind CSS** via CDN + shared theme in `assets/js/tailwind-config.js`
- No React or other frameworks

## Backend (next steps)

1. Add `database/schema.sql`
2. Add `php/config/database.php`
3. Convert `pages/**/*.html` → `pages/**/*.php` and use `includes/` for layout
4. Point forms to `php/auth/login.php` and `php/auth/register.php`

## Rebuild pages from Stitch export

If you update files under `stitch_export/`, run:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/build-pages.ps1
```

Then re-apply any manual nav fixes if needed.


---

# Current Working Features

## Authentication
- Student registration
- Teacher registration
- Login system
- Password hashing
- Session management
- Logout system
- Role-based protection

## Database
Database name:
attendance_system

Tables:
- users
- classes
- enrollments
- attendance
- reports

## Class Management
Teacher:
- create class
- auto-generate class code
- view classes

Student:
- join class using class code
- duplicate enrollment prevention

## Attendance Management
Teacher:
- load class students
- mark Present/Absent
- submit attendance

System:
- prevents duplicate attendance for same date

---

# Dashboard Logic

## Student Dashboard
Each class card should display:
- class/subject name
- teacher name
- class code
- LIVE attendance percentage

Example:
DATABASE MANAGEMENT              97%
EDU-DE64B1 • Teacher: John Doe

Percentage formula:
(Present Count / Total Attendance Count) * 100

---

# Shared Attendance Matrix

Both students and teachers should access the same consolidated attendance matrix.

Matrix displays:
- all classmates
- all subjects/classes
- attendance percentage for each subject

Example:

| Student Name | DBMS | OS | CN |
| John | 85% | 90% | 76% |
| Alice | 92% | 88% | 81% |

Rules:
- students only see enrolled classes
- teachers only see classes they teach

---

# Important Notes
- Do NOT regenerate the project
- Existing authentication already works
- Existing attendance marking already works
- Keep code beginner friendly
- Use mysqli prepared statements
- Use existing session system

---

# Next Pending Work
1. Add live attendance percentage on class cards
2. Create consolidated attendance matrix page
3. Improve UI polishing
4. Optional export/report improvements
