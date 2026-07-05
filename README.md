# Business Website — Setup Guide

## যা যা আছে
- Fully responsive frontend (Home, About, Services, Completed Jobs, Reviews, Team, Contact)
- Admin Dashboard (login protected) — Services, Completed Jobs, Reviews, Team, Custom Sections, Messages, Site Settings সব edit করা যাবে
- 1টা global CSS file (`css/style.css`) — style change করতে এখানেই edit করবা
- Nav + Footer শুধু 2টা file এ (`includes/header.php`, `includes/footer.php`) — এখানে change করলে সব page এ apply হবে

## Shared Hosting এ Upload করার ধাপ

### ১. Database বানাও
- cPanel → MySQL Databases → নতুন DB বানাও, DB user বানাও, user কে DB এর সাথে attach করো (All Privileges)
- phpMyAdmin এ ঢুকে ঐ DB select করো → Import → `sql/schema.sql` ফাইল import করো

### ২. DB Connection info বসাও
`includes/db.php` ফাইল খুলে এই ৪টা লাইন নিজের DB info দিয়ে বদলাও:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
```

### ৩. ফাইল আপলোড করো
পুরো folder এর ভিতরের সব কিছু (index.php, includes/, admin/, css/, js/, uploads/, ইত্যাদি) cPanel File Manager বা FTP দিয়ে `public_html` (বা তোমার domain এর root folder) এ upload করো।

### ৪. uploads folder permission
`uploads/` folder এ 755 (বা প্রয়োজনে 775) permission দাও, যাতে admin panel থেকে image upload কাজ করে।

### ৫. Admin Panel এ Login
Browser এ যাও: `yourdomain.com/admin/login.php`
- Username: `admin`
- Password: `admin123`

⚠️ **প্রথম login এর পরেই password change করে নাও** (phpMyAdmin এ গিয়ে `admin_users` table এ password field এ নতুন bcrypt hash বসাতে হবে — অথবা আমাকে বললে আমি "Change Password" page টাও বানিয়ে দিতে পারি)।

## Admin Dashboard থেকে কি edit করা যায়
| Section | Location |
|---|---|
| Services | admin/services.php |
| Completed Jobs | admin/jobs.php |
| Reviews | admin/reviews.php |
| Team | admin/teams.php |
| Custom Sections (About page etc.) | admin/sections.php |
| Contact Messages | admin/messages.php |
| Site Name/Phone/Email/Footer text | admin/settings.php |

## Style / Nav / Footer change korte
- Color, font, spacing → `css/style.css`
- Menu link → `includes/header.php`
- Footer layout → `includes/footer.php`
- Footer/Contact TEXT (phone, email, address) → admin/settings.php থেকে (code touch করা লাগবে না)

## Notes
- Server e PHP 7.4+ এবং MySQL থাকতে হবে (প্রায় সব shared hosting এ default থাকে)
- কোনো framework/Composer লাগে না — plain PHP, direct upload করলেই কাজ করবে
