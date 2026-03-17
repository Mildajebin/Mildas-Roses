<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
$page_title = 'Contact Us';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = clean($conn, $_POST['name'] ?? '');
    $email   = clean($conn, $_POST['email'] ?? '');
    $subject = clean($conn, $_POST['subject'] ?? '');
    $message = clean($conn, $_POST['message'] ?? '');

    if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($message) < 10) {
        $error = 'Please fill in all fields correctly.';
    } else {
        // In a real site: mail('hello@mildasroses.qa', $subject, $message);
        $success = 'Thank you! We\'ll get back to you within 24 hours.';
    }
}

require_once 'includes/header.php';
?>
<div class="page-wrap">
<div class="shop-header">
    <span class="section-tag">Get in Touch</span>
    <h1 style="font-size:clamp(36px,5vw,64px);font-weight:300;color:var(--bark)">We'd Love to Hear From You</h1>
</div>

<div class="contact-layout">
    <!-- INFO -->
    <div style="flex:1;min-width:260px">
        <h2 style="font-size:32px;color:var(--bark);margin-bottom:16px">Let's Create Something Beautiful Together</h2>
        <p style="color:var(--muted);line-height:1.8;margin-bottom:32px;font-size:14px">Whether you have a question about an order, want to discuss a custom design, or simply want to say hello — we're here and happy to help.</p>
        <div class="contact-item"><div class="contact-icon">📧</div><div style="font-size:14px;color:var(--bark)">hello@mildasroses.qa</div></div>
        <div class="contact-item"><div class="contact-icon">📞</div><div style="font-size:14px;color:var(--bark)">+974 4412 8833</div></div>
        <div class="contact-item"><div class="contact-icon">📍</div><div style="font-size:14px;color:var(--bark)">Pearl-Qatar, P.O. Box 22344, Doha, Qatar</div></div>
        <div class="contact-item"><div class="contact-icon">⏰</div><div style="font-size:14px;color:var(--bark)">Sunday–Thursday, 9am–6pm (AST)</div></div>
        <div class="social-row">
            <a class="social-btn" href="#" title="Instagram">📷</a>
            <a class="social-btn" href="#" title="Facebook">📘</a>
            <a class="social-btn" href="#" title="TikTok">🎵</a>
            <a class="social-btn" href="#" title="Pinterest">📌</a>
        </div>
    </div>

    <!-- FORM -->
    <div style="flex:1;min-width:300px">
        <div style="background:var(--warm-white);border:1px solid var(--light-muted);border-radius:20px;padding:36px">
            <?php if ($success): ?><div class="form-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
            <?php if ($error):   ?><div class="form-alert"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Your Name</label>
                        <input class="form-input" name="name" required placeholder="Fatima Al-Rashid" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subject</label>
                        <select class="form-input" name="subject">
                            <option>General Enquiry</option>
                            <option>Custom Order</option>
                            <option>Order Support</option>
                            <option>Wholesale</option>
                            <option>Press</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:14px">
                    <label class="form-label">Email Address</label>
                    <input class="form-input" type="email" name="email" required placeholder="you@example.com" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>">
                </div>
                <div class="form-group" style="margin-bottom:20px">
                    <label class="form-label">Message</label>
                    <textarea class="form-input" name="message" rows="5" required placeholder="Tell us about your vision, event, or how we can help…"></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width:100%">Send Message</button>
            </form>
        </div>
    </div>
</div>
</div>
<?php require_once 'includes/footer.php'; ?>
