<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
$page_title = 'Sign In';

if (is_logged_in()) { header('Location: account.php'); exit; }

$error = '';
$redirect = clean($conn, $_GET['redirect'] ?? 'account.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (login_user($conn, $_POST['email'], $_POST['password'])) {
        header('Location: ' . $redirect);
        exit;
    } else {
        $error = 'Invalid email or password. Please try again.';
    }
}

require_once 'includes/header.php';
?>
<div class="page-wrap" style="background:var(--blush);min-height:100vh">
<div class="auth-wrap">
    <div class="auth-card">
        <h1>Welcome Back</h1>
        <p>Sign in to access your account, orders &amp; wishlist</p>

        <?php if ($error): ?><div class="form-alert"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

        <form method="POST">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
            <div class="form-group" style="margin-bottom:14px">
                <label class="form-label">Email Address</label>
                <input class="form-input" type="email" name="email" required placeholder="you@example.com" autofocus>
            </div>
            <div class="form-group" style="margin-bottom:20px">
                <label class="form-label">Password</label>
                <input class="form-input" type="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn-primary" style="width:100%">Sign In</button>
        </form>

        <div class="auth-divider">or</div>
        <button class="btn-outline" style="width:100%;margin-bottom:10px">🔵 Continue with Google</button>
        <button class="btn-outline" style="width:100%">🍎 Continue with Apple</button>

        <div class="auth-footer">
            Don't have an account? <a href="register.php">Create one free</a>
        </div>
    </div>
</div>
</div>
<?php require_once 'includes/footer.php'; ?>
