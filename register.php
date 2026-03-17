<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
$page_title = 'Create Account';

if (is_logged_in()) { header('Location: account.php'); exit; }

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if (strlen($name) < 2)          $error = 'Please enter your full name.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Please enter a valid email address.';
    elseif (strlen($pass) < 6)      $error = 'Password must be at least 6 characters.';
    elseif ($pass !== $pass2)        $error = 'Passwords do not match.';
    else {
        $result = register_user($conn, $name, $email, $pass, $phone);
        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            // Auto-login
            login_user($conn, $email, $pass);
            header('Location: account.php');
            exit;
        }
    }
}

require_once 'includes/header.php';
?>
<div class="page-wrap" style="background:var(--blush);min-height:100vh">
<div class="auth-wrap">
    <div class="auth-card">
        <h1>Create Account</h1>
        <p>Join Milda's Roses to save wishlists, track orders &amp; more</p>

        <?php if ($error): ?><div class="form-alert"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-group" style="margin-bottom:14px">
                <label class="form-label">Full Name</label>
                <input class="form-input" type="text" name="name" required placeholder="Fatima Al-Rashid" value="<?php echo htmlspecialchars($_POST['name']??''); ?>" autofocus>
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label class="form-label">Email Address</label>
                <input class="form-input" type="email" name="email" required placeholder="you@example.com" value="<?php echo htmlspecialchars($_POST['email']??''); ?>">
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label class="form-label">Phone (optional)</label>
                <input class="form-input" type="tel" name="phone" placeholder="+974 XXXX XXXX" value="<?php echo htmlspecialchars($_POST['phone']??''); ?>">
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label class="form-label">Password</label>
                <input class="form-input" type="password" name="password" required placeholder="Min. 6 characters">
            </div>
            <div class="form-group" style="margin-bottom:24px">
                <label class="form-label">Confirm Password</label>
                <input class="form-input" type="password" name="confirm_password" required placeholder="Repeat password">
            </div>
            <button type="submit" class="btn-primary" style="width:100%">Create My Account</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Sign in</a>
        </div>
    </div>
</div>
</div>
<?php require_once 'includes/footer.php'; ?>
