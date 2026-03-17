<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
$page_title = 'Your Cart';

if (session_status() === PHP_SESSION_NONE) session_start();

// Handle update/remove from POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $key    = htmlspecialchars($_POST['key'] ?? '');

    if ($action === 'remove' && isset($_SESSION['cart'][$key])) {
        unset($_SESSION['cart'][$key]);
    }
    if ($action === 'update' && isset($_SESSION['cart'][$key])) {
        $qty = (int)$_POST['qty'];
        if ($qty <= 0) unset($_SESSION['cart'][$key]);
        else $_SESSION['cart'][$key]['qty'] = $qty;
    }
    header('Location: cart.php');
    exit;
}

$cart  = $_SESSION['cart'] ?? [];
$subtotal = 0;
foreach ($cart as $item) $subtotal += $item['price'] * $item['qty'];
$shipping = $subtotal >= 200 ? 0 : 25;
$vat      = round($subtotal * 0.05, 2);
$total    = $subtotal + $shipping + $vat;

require_once 'includes/header.php';
?>
<div class="page-wrap">
<div class="cart-layout">

<!-- CART ITEMS -->
<div class="cart-items">
    <h1 class="cart-title">Shopping Cart</h1>

    <?php if (empty($cart)): ?>
    <div class="empty-state">
        <div class="icon">🛒</div>
        <h3>Your cart is empty</h3>
        <p style="margin-bottom:24px">Discover our beautiful handmade arrangements.</p>
        <a href="shop.php" class="btn-primary">Start Shopping</a>
    </div>
    <?php else: ?>
    <?php foreach ($cart as $key => $item): ?>
    <div class="cart-item">
        <div class="cart-item-img">
            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
        </div>
        <div class="cart-item-info">
            <div class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
            <div class="cart-item-opt"><?php echo htmlspecialchars($item['color'].' · '.$item['size']); ?></div>
            <div style="display:flex;align-items:center;gap:16px">
                <form method="POST" style="display:flex;align-items:center;gap:0">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="key" value="<?php echo htmlspecialchars($key); ?>">
                    <div class="qty-control">
                        <button type="submit" name="qty" value="<?php echo $item['qty']-1; ?>" class="qty-btn">−</button>
                        <div class="qty-num"><?php echo $item['qty']; ?></div>
                        <button type="submit" name="qty" value="<?php echo $item['qty']+1; ?>" class="qty-btn">+</button>
                    </div>
                </form>
                <div class="cart-item-price">QAR <?php echo number_format($item['price'] * $item['qty'], 2); ?></div>
            </div>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="remove">
            <input type="hidden" name="key" value="<?php echo htmlspecialchars($key); ?>">
            <button type="submit" class="remove-btn" title="Remove">✕</button>
        </form>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- ORDER SUMMARY -->
<div class="cart-summary">
    <div class="summary-card">
        <h3 class="summary-title">Order Summary</h3>
        <div class="summary-row"><span>Subtotal</span><span>QAR <?php echo number_format($subtotal,2); ?></span></div>
        <div class="summary-row"><span>Shipping</span><span><?php echo $shipping ? 'QAR '.number_format($shipping,2) : 'Free'; ?></span></div>
        <div class="summary-row"><span>VAT (5%)</span><span>QAR <?php echo number_format($vat,2); ?></span></div>
        <?php if ($subtotal > 0 && $subtotal < 200): ?>
        <div style="font-size:12px;color:var(--sage);margin:-4px 0 12px;text-align:right">Add QAR <?php echo number_format(200-$subtotal,2); ?> more for free shipping!</div>
        <?php endif; ?>
        <div class="coupon-row">
            <input class="coupon-input" id="coupon_input" placeholder="Coupon code">
            <button class="coupon-btn" onclick="applyCoupon()">Apply</button>
        </div>
        <div class="summary-row total"><span>Total</span><span id="total-display">QAR <?php echo number_format($total,2); ?></span></div>
        <?php if (!empty($cart)): ?>
        <a href="checkout.php" class="btn-primary" style="display:block;text-align:center;margin-top:20px">Proceed to Checkout</a>
        <?php endif; ?>
        <a href="shop.php" class="btn-outline" style="display:block;text-align:center;margin-top:10px">Continue Shopping</a>
        <div style="display:flex;justify-content:center;gap:12px;margin-top:20px;font-size:20px">🔒 💳</div>
        <p style="text-align:center;font-size:11px;color:var(--muted);margin-top:8px">Secure checkout · SSL encrypted</p>
    </div>
</div>

</div>
</div>
<?php require_once 'includes/footer.php'; ?>
