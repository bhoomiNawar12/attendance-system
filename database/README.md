# Database

Place MySQL schema and seed data here.

Files:

- `schema.sql` — full database schema (import in phpMyAdmin)
- `seed.sql` — sample data (optional, add when needed)

### Import in phpMyAdmin (XAMPP)

1. Start **MySQL** in XAMPP Control Panel
2. Open **http://localhost/phpmyadmin**
3. Go to **Import** → choose `schema.sql` → **Go**

Or use the SQL tab and paste the file contents.

Connect from PHP using `php/config/database.php` (create that file next).
