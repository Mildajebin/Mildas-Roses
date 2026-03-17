# 🌸 Milda's Roses — PHP + MySQL E-Commerce Website

A fully functional handmade flower shop built with PHP, MySQL, HTML/CSS, and JavaScript.

---

## 📁 File Structure

```
bloom_petal/
├── index.php              ← Home page
├── shop.php               ← Product listing with filters
├── product.php            ← Product detail + reviews
├── cart.php               ← Shopping cart
├── checkout.php           ← Checkout + order placement
├── login.php              ← Customer login
├── register.php           ← Customer registration
├── account.php            ← Customer dashboard (orders, wishlist)
├── about.php              ← About Us page
├── blog.php               ← Blog listing + post detail
├── contact.php            ← Contact form
├── policies.php           ← Shipping, Returns, Privacy, Terms
├── setup_database.sql     ← Run this ONCE to create DB + seed data
│
├── includes/
│   ├── db.php             ← Database connection (edit credentials here)
│   ├── auth.php           ← Login/register/session helpers
│   ├── header.php         ← Shared nav header
│   └── footer.php         ← Shared footer
│
├── api/
│   ├── cart.php           ← AJAX cart add/update/remove
│   ├── wishlist.php       ← AJAX wishlist toggle
│   ├── coupon.php         ← AJAX coupon validation
│   └── review.php         ← Review submission handler
│
├── admin/
│   ├── index.php          ← Admin dashboard (products, orders, customers)
│   ├── edit_product.php   ← Edit product form
│   └── delete_product.php ← Delete product handler
│
├── assets/
│   ├── css/style.css      ← All styles
│   └── js/main.js         ← Cart AJAX, interactions
│
└── uploads/
    └── products/          ← Product images go here
        ├── 1.jpg
        ├── 2.jpg
        ├── 3.jpg
        ├── 4.jpg
        └── 11.jpg
```

---

## 🚀 Setup Instructions (XAMPP — Local)

### Step 1: Install XAMPP
Download from https://www.apachefriends.org and install it.
Start both **Apache** and **MySQL** from the XAMPP Control Panel.

### Step 2: Copy Files
Copy the entire `bloom_petal` folder into:
```
C:\xampp\htdocs\bloom_petal\     (Windows)
/Applications/XAMPP/htdocs/bloom_petal/  (Mac)
```

### Step 3: Copy Your Flower Images
Copy your flower photos into:
```
bloom_petal/uploads/products/
```
Make sure they are named: `1.jpg`, `2.jpg`, `3.jpg`, `4.jpg`, `11.jpg`

### Step 4: Create the Database
1. Open your browser and go to: http://localhost/phpmyadmin
2. Click **Import** in the top menu
3. Choose the file `bloom_petal/setup_database.sql`
4. Click **Go**

This creates the database, all tables, and sample data automatically.

### Step 5: Configure Database Connection
Open `includes/db.php` and update if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // your MySQL username
define('DB_PASS', '');           // your MySQL password (blank by default in XAMPP)
define('DB_NAME', 'bloom_petal');
```

### Step 6: Open the Website
Go to: **http://localhost/bloom_petal/**

---

## 🔐 Default Admin Login
- **URL:** http://localhost/bloom_petal/login.php
- **Email:** admin@mildasroses.qa
- **Password:** Admin@1234

Then visit: http://localhost/bloom_petal/admin/

---

## 🌐 Live Hosting (cPanel / Hostinger / Namecheap)

1. **Upload** all files via cPanel File Manager or FTP into `public_html/bloom_petal/`
2. **Create a MySQL database** in cPanel → MySQL Databases
3. **Import** `setup_database.sql` via phpMyAdmin
4. **Update** `includes/db.php` with your hosting credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_cpanel_db_user');
   define('DB_PASS', 'your_db_password');
   define('DB_NAME', 'your_db_name');
   ```
5. Make `uploads/products/` writable: set permissions to **755**

---

## 💳 Payment Integration (Stripe)

To activate real card payments, add the Stripe PHP SDK:

```bash
composer require stripe/stripe-php
```

In `checkout.php`, replace the order insert with:
```php
require 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_live_YOUR_SECRET_KEY');

$intent = \Stripe\PaymentIntent::create([
    'amount'   => $total * 100,  // in fils
    'currency' => 'qar',
]);
// Use $intent->client_secret in frontend Stripe.js
```

Get your API keys from: https://dashboard.stripe.com

---

## 📧 Email Confirmations (PHPMailer)

```bash
composer require phpmailer/phpmailer
```

Add to `checkout.php` after successful order:
```php
use PHPMailer\PHPMailer\PHPMailer;
$mail = new PHPMailer();
$mail->isSMTP();
$mail->Host     = 'smtp.gmail.com';
$mail->Username = 'your@gmail.com';
$mail->Password = 'your_app_password';
$mail->setFrom('hello@mildasroses.qa', 'Milda's Roses');
$mail->addAddress($email);
$mail->Subject = 'Order Confirmed — #' . $ref;
$mail->Body    = 'Thank you for your order! We will process it shortly.';
$mail->send();
```

---

## 🛡️ Security Checklist

- [x] All user inputs sanitized with `mysqli_real_escape_string()`
- [x] Passwords hashed with `password_hash()` (bcrypt)
- [x] Admin pages protected with `require_admin()`
- [x] File uploads restricted to jpg/png/webp only
- [ ] Add HTTPS (SSL certificate) on your live host
- [ ] Set `display_errors = Off` in production php.ini
- [ ] Move `includes/db.php` credentials to environment variables

---

## 🔧 Customisation Tips

| What to change | Where |
|---|---|
| Shop name & contact | `includes/header.php`, `includes/footer.php`, `contact.php` |
| Colours & fonts | `assets/css/style.css` (:root variables) |
| Product images | `uploads/products/` + update `image_path` in DB |
| Shipping prices | `checkout.php` (data-cost values) |
| VAT rate | `cart.php` and `checkout.php` (change 0.05 to your rate) |
| Currency | Search & replace "QAR" across all files |
| Coupon codes | Add rows to `coupons` table in phpMyAdmin |
| Blog posts | Add rows to `blog_posts` table in phpMyAdmin |

---

Built with ❤️ using PHP 8, MySQL, and vanilla CSS/JS. No frameworks required.
