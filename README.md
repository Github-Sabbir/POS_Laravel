# Retail POS — Laravel + XAMPP MySQL

This project follows the supplied master POS requirements: Laravel/Blade, XAMPP MySQL, automatic immutable SKU, multiple unique barcodes, secure product image upload, transactional stock management, POS, purchases, returns, customers, suppliers, expenses, reports, roles/permissions, activity logs, backup/restore, responsive Glassmorphism UI, and dark/light mode.

## Setup on Windows/XAMPP

1. Install PHP 8.2+, Composer and XAMPP.
2. Put this folder at `C:\xampp\htdocs\retail-pos`.
3. Start Apache and MySQL in XAMPP.
4. Create a MySQL database named `retail_pos` (utf8mb4).
5. Copy `.env.example` to `.env`.
6. Run:
   `composer install`
   `php artisan key:generate`
   `php artisan migrate --seed`
   `php artisan storage:link`
7. Visit `http://localhost/retail-pos/public`.

Demo:
- admin@example.com / password
- cashier@example.com / password
Change these passwords before real use.

## Important
This ZIP is source code and setup scaffolding; run the Laravel install/migrations on your machine before production use. Test backup/restore and printing against your local XAMPP environment.
