<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
$page_title = 'Checkout';
if (session_status() === PHP_SESSION_NONE) session_start();

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) { header('Location: shop.php'); exit; }

$subtotal = 0;
foreach ($cart as $item) $subtotal += $item['price'] * $item['qty'];
$shipping_cost = 25.00;
$vat    = round($subtotal * 0.05, 2);
$total  = $subtotal + $shipping_cost + $vat;

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = clean($conn, $_POST['first_name'].' '.$_POST['last_name']);
    $email   = clean($conn, $_POST['email']);
    $phone   = clean($conn, $_POST['phone']);
    $street  = clean($conn, $_POST['street']);
    $city    = clean($conn, $_POST['city']);
    $country = clean($conn, $_POST['country']);
    $ship_m  = clean($conn, $_POST['shipping_method'] ?? 'standard');
    $pay_m   = clean($conn, $_POST['payment_method']  ?? 'card');
    $gift    = clean($conn, $_POST['gift_message'] ?? '');
    $coupon  = clean($conn, $_POST['coupon_code'] ?? '');
    $ship_c  = (float)($_POST['shipping_cost'] ?? 25);
    $vat2    = round($subtotal * 0.05, 2);
    $total2  = $subtotal + $ship_c + $vat2;

    $user_id = is_logged_in() ? (int)$_SESSION['user_id'] : 'NULL';
    $uid_sql = $user_id === 'NULL' ? 'NULL' : $user_id;

    $sql = "INSERT INTO orders (user_id,guest_name,guest_email,guest_phone,shipping_street,shipping_city,shipping_country,shipping_method,payment_method,subtotal,shipping_cost,vat,total,gift_message,coupon_code)
            VALUES ($uid_sql,'$name','$email','$phone','$street','$city','$country','$ship_m','$pay_m',$subtotal,$ship_c,$vat2,$total2,'$gift','$coupon')";
    mysqli_query($conn, $sql);
    $order_id = mysqli_insert_id($conn);

    if ($order_id) {
        // Insert order items
        foreach ($cart as $item) {
            $pid  = (int)$item['id'];
            $iname = clean($conn, $item['name']);
            $ipr  = (float)$item['price'];
            $iqty = (int)$item['qty'];
            $icol = clean($conn, $item['color'] ?? '');
            $isz  = clean($conn, $item['size'] ?? '');
            mysqli_query($conn, "INSERT INTO order_items (order_id,product_id,name,price,quantity,color,size) VALUES ($order_id,$pid,'$iname',$ipr,$iqty,'$icol','$isz')");
            // Reduce stock
            mysqli_query($conn, "UPDATE products SET stock = GREATEST(0, stock - $iqty) WHERE id=$pid");
        }
        unset($_SESSION['cart']);
        $ref = 'BP-' . date('Ymd') . '-' . str_pad($order_id, 4, '0', STR_PAD_LEFT);
        $success = $ref;
    } else {
        $error = 'There was a problem placing your order. Please try again.';
    }
}

require_once 'includes/header.php';
?>
<div class="page-wrap">

<?php if ($success): ?>
<!-- ORDER SUCCESS -->
<div style="max-width:520px;margin:80px auto;text-align:center;padding:20px">
    <div style="font-size:72px;margin-bottom:20px">🌸</div>
    <h1 style="font-size:36px;color:var(--bark);margin-bottom:12px">Order Placed!</h1>
    <p style="color:var(--muted);margin-bottom:24px;font-size:15px">Thank you! You'll receive a confirmation email shortly.</p>
    <div style="background:var(--blush);border-radius:14px;padding:20px;margin:24px 0;text-align:left">
        <div style="font-size:12px;color:var(--muted);margin-bottom:4px">Order Reference</div>
        <div style="font-size:22px;color:var(--bark);font-family:'Cormorant Garamond',serif">#<?php echo htmlspecialchars($success); ?></div>
    </div>
    <a href="account.php" class="btn-primary">Track My Order</a>
    <a href="shop.php" class="btn-outline" style="margin-left:10px">Continue Shopping</a>
</div>

<?php else: ?>

<div class="checkout-layout">
<div class="checkout-form">
    <h1 style="font-size:36px;color:var(--bark);margin-bottom:36px;font-weight:400">Checkout</h1>

    <?php if ($error): ?><div class="form-alert"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <form method="POST" id="checkout-form">
    <div class="form-section">
        <h3 class="form-section-title">Contact Information</h3>
        <div class="form-row">
            <div class="form-group"><label class="form-label">First Name</label><input class="form-input" name="first_name" required placeholder="Fatima" value="<?php echo isset($_SESSION['user_name']) ? explode(' ',$_SESSION['user_name'])[0] : ''; ?>"></div>
            <div class="form-group"><label class="form-label">Last Name</label><input class="form-input" name="last_name" required placeholder="Al-Rashid"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Email</label><input class="form-input" type="email" name="email" required placeholder="you@example.com" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>"></div>
            <div class="form-group"><label class="form-label">Phone</label><input class="form-input" name="phone" placeholder="+974 XXXX XXXX"></div>
        </div>
    </div>

    <div class="form-section">
        <h3 class="form-section-title">Shipping Address</h3>
        <div class="form-group" style="margin-bottom:14px"><label class="form-label">Street Address</label><input class="form-input" name="street" required placeholder="Street, Building, Apartment"></div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">City</label><input class="form-input" name="city" required placeholder="Doha"></div>
            <div class="form-group"><label class="form-label">Country</label>
                <select class="form-input" name="country">
                    <option>Qatar</option><option>Saudi Arabia</option><option>UAE</option><option>Kuwait</option><option>Bahrain</option><option>Oman</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3 class="form-section-title">Shipping Method</h3>
        <div class="shipping-options">
            <div class="shipping-option selected" onclick="selectShipping(this)" data-method="standard" data-cost="25">
                <div><div style="font-size:14px;color:var(--bark);font-weight:500">Standard Delivery</div><div style="font-size:12px;color:var(--muted);margin-top:2px">3–5 business days · DHL</div></div>
                <div style="font-size:14px;color:var(--deep-rose);font-family:'Cormorant Garamond',serif;font-weight:500">QAR 25</div>
            </div>
            <div class="shipping-option" onclick="selectShipping(this)" data-method="express" data-cost="65">
                <div><div style="font-size:14px;color:var(--bark);font-weight:500">Express Delivery</div><div style="font-size:12px;color:var(--muted);margin-top:2px">1–2 business days · FedEx</div></div>
                <div style="font-size:14px;color:var(--deep-rose);font-family:'Cormorant Garamond',serif;font-weight:500">QAR 65</div>
            </div>
            <div class="shipping-option" onclick="selectShipping(this)" data-method="sameday" data-cost="95">
                <div><div style="font-size:14px;color:var(--bark);font-weight:500">🎁 Same-Day Gift Delivery</div><div style="font-size:12px;color:var(--muted);margin-top:2px">Today by 8pm · Doha only</div></div>
                <div style="font-size:14px;color:var(--deep-rose);font-family:'Cormorant Garamond',serif;font-weight:500">QAR 95</div>
            </div>
        </div>
        <input type="hidden" name="shipping_method" id="shipping_method" value="standard">
        <input type="hidden" name="shipping_cost"   id="shipping_cost"   value="25">
    </div>

    <div class="form-section">
        <h3 class="form-section-title">Payment Method</h3>
        <div class="payment-options">
            <div class="payment-option selected" onclick="selectPayment(this)" data-method="card"><div style="font-size:24px">💳</div><div style="font-size:13px;margin-top:6px">Card</div></div>
            <div class="payment-option" onclick="selectPayment(this)" data-method="paypal"><div style="font-size:24px">🅿️</div><div style="font-size:13px;margin-top:6px">PayPal</div></div>
            <div class="payment-option" onclick="selectPayment(this)" data-method="apple_pay"><div style="font-size:24px">🍎</div><div style="font-size:13px;margin-top:6px">Apple Pay</div></div>
            <div class="payment-option" onclick="selectPayment(this)" data-method="google_pay"><div style="font-size:24px">🅖</div><div style="font-size:13px;margin-top:6px">Google Pay</div></div>
        </div>
        <input type="hidden" name="payment_method" id="payment_method" value="card">
        <div id="card-fields">
            <label class="form-label">Card Number</label>
            <input class="form-input" placeholder="1234 5678 9012 3456" style="margin-bottom:12px">
            <div class="form-row">
                <div class="form-group"><label class="form-label">Expiry</label><input class="form-input" placeholder="MM / YY"></div>
                <div class="form-group"><label class="form-label">CVV</label><input class="form-input" placeholder="•••"></div>
            </div>
            <div style="font-size:12px;color:var(--muted);margin-top:8px">🔒 Secured by Stripe · Card details are never stored.</div>
        </div>
    </div>

    <div style="background:var(--blush);border-radius:14px;padding:18px;margin-bottom:24px">
        <label style="display:flex;align-items:center;gap:10px;font-size:13px;color:var(--bark);cursor:pointer">
            <input type="checkbox" onclick="document.getElementById('gift-field').style.display=this.checked?'block':'none'" style="accent-color:var(--rose)">
            Add a gift message (optional)
        </label>
        <textarea id="gift-field" name="gift_message" class="form-input" placeholder="Write your personal message here…" style="margin-top:12px;height:80px;resize:none;display:none"></textarea>
    </div>
    <input type="hidden" name="coupon_code" id="co_coupon_code" value="">

    <button type="submit" class="btn-primary" style="width:100%;font-size:14px;padding:16px">
        Place Order — <span id="co_total_display">QAR <?php echo number_format($total,2); ?></span>
    </button>
    <p style="text-align:center;font-size:12px;color:var(--muted);margin-top:14px">By placing your order you agree to our <a href="policies.php" style="color:var(--rose)">Terms of Service</a>.</p>
    </form>
</div>

<!-- SIDEBAR SUMMARY -->
<div style="width:280px;flex-shrink:0;align-self:flex-start;position:sticky;top:80px">
    <div class="summary-card">
        <h3 class="summary-title" style="font-size:18px">Order Summary</h3>
        <?php foreach ($cart as $item): ?>
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
            <img src="<?php echo htmlspecialchars($item['image']); ?>" style="width:48px;height:48px;border-radius:8px;object-fit:cover">
            <div style="flex:1">
                <div style="font-size:13px;color:var(--bark)"><?php echo htmlspecialchars($item['name']); ?> ×<?php echo $item['qty']; ?></div>
                <div style="font-size:13px;color:var(--deep-rose);font-family:'Cormorant Garamond',serif">QAR <?php echo number_format($item['price']*$item['qty'],2); ?></div>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="summary-row"><span>Subtotal</span><span data-value="<?php echo $subtotal; ?>" id="co_subtotal">QAR <?php echo number_format($subtotal,2); ?></span></div>
        <div class="summary-row"><span>Shipping</span><span>QAR 25.00</span></div>
        <div class="summary-row"><span>VAT (5%)</span><span>QAR <?php echo number_format($vat,2); ?></span></div>
        <div class="summary-row total"><span>Total</span><span>QAR <?php echo number_format($total,2); ?></span></div>
    </div>
</div>
</div>
<?php endif; ?>
</div>
<?php require_once 'includes/footer.php'; ?>
