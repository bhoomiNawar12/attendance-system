# PHP includes (future)

When you add PHP, split repeated layout from `pages/` into partials:

| File | Purpose |
|------|---------|
| `head.php` | Meta, Tailwind CDN, `assets/` links |
| `header-student.php` | Top bar for student pages |
| `sidebar-student.php` | Student navigation |
| `sidebar-teacher.php` | Teacher navigation |
| `footer.php` | Scripts (`app.js`) |

Example in a future `pages/student/dashboard.php`:

```php
<?php require_once __DIR__ . '/../../includes/head.php'; ?>
<?php require_once __DIR__ . '/../../includes/sidebar-student.php'; ?>
<!-- page content -->
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
```
