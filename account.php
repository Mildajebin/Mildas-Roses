<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
$page_title = 'My Account';
require_login();

$user_id = (int)$_SESSION['user_id'];
$tab = clean($conn, $_GET['tab'] ?? 'orders');

// Fetch orders
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY created_at DESC LIMIT 20");

// Logout
if (isset($_GET['logout'])) logout_user();

require_once 'includes/header.php';
?>
<div class="page-wrap">
<div class="account-layout">

<!-- SIDEBAR -->
<div class="account-sidebar">
    <div class="account-avatar">🌸</div>
    <div class="account-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
    <div class="account-email"><?php echo htmlspecialchars($_SESSION['user_email']); ?></div>
    <ul class="account-nav">
        <li><a href="?tab=orders"    class="<?php echo $tab==='orders'?'active':''; ?>">📦 My Orders</a></li>
        <li><a href="?tab=wishlist"  class="<?php echo $tab==='wishlist'?'active':''; ?>">♡ Wishlist</a></li>
        <li><a href="?tab=addresses" class="<?php echo $tab==='addresses'?'active':''; ?>">📍 Addresses</a></li>
        <li><a href="?tab=profile"   class="<?php echo $tab==='profile'?'active':''; ?>">👤 Profile</a></li>
        <li><a href="?logout=1" style="color:#e88;margin-top:24px">↩ Sign Out</a></li>
    </ul>
</div>

<!-- MAIN -->
<div class="account-main">
<?php if ($tab === 'orders'): ?>
    <h2 style="font-size:28px;color:var(--bark);margin-bottom:28px">My Orders</h2>
    <?php if (mysqli_num_rows($orders) === 0): ?>
    <div class="empty-state">
        <div class="icon">📦</div>
        <h3>No orders yet</h3>
        <p style="margin-bottom:24px">When you place an order, it will appear here.</p>
        <a href="shop.php" class="btn-primary">Start Shopping</a>
    </div>
    <?php else: ?>
    <?php while ($o = mysqli_fetch_assoc($orders)):
        $ref = 'BP-' . date('Ymd', strtotime($o['created_at'])) . '-' . str_pad($o['id'],4,'0',STR_PAD_LEFT);
        $items_res = mysqli_query($conn, "SELECT name FROM order_items WHERE order_id={$o['id']} LIMIT 2");
        $item_names = [];
        while ($oi = mysqli_fetch_assoc($items_res)) $item_names[] = $oi['name'];
    ?>
    <div class="order-card">
        <div>
            <div style="font-size:12px;color:var(--muted);margin-bottom:4px">#<?php echo $ref; ?></div>
            <div style="font-size:16px;color:var(--bark);font-family:'Cormorant Garamond',serif;margin-bottom:4px"><?php echo htmlspecialchars(implode(', ',$item_names)); ?></div>
            <div style="font-size:12px;color:var(--muted)"><?php echo date('d M Y', strtotime($o['created_at'])); ?> · QAR <?php echo number_format($o['total'],2); ?></div>
        </div>
        <div style="display:flex;align-items:center;gap:12px">
            <span class="status-badge status-<?php echo $o['status']; ?>"><?php echo ucfirst($o['status']); ?></span>
            <?php if ($o['tracking_number']): ?>
            <button class="track-btn" onclick="showNotif('Tracking: <?php echo htmlspecialchars($o['tracking_number']); ?>')">Track</button>
            <?php endif; ?>
        </div>
    </div>
    <?php endwhile; ?>
    <?php endif; ?>

<?php elseif ($tab === 'wishlist'): ?>
    <h2 style="font-size:28px;color:var(--bark);margin-bottom:28px">My Wishlist</h2>
    <?php
    $wl = mysqli_query($conn, "SELECT p.* FROM wishlists w JOIN products p ON w.product_id=p.id WHERE w.user_id=$user_id");
    if (mysqli_num_rows($wl) === 0): ?>
    <div class="empty-state"><div class="icon">♡</div><h3>Your wishlist is empty</h3><p style="margin-bottom:24px">Save items you love for later.</p><a href="shop.php" class="btn-primary">Browse Products</a></div>
    <?php else: ?>
    <div class="products-grid">
        <?php while ($p = mysqli_fetch_assoc($wl)): ?>
        <div class="product-card">
            <a href="product.php?id=<?php echo $p['id']; ?>">
                <div class="product-img"><img src="<?php echo htmlspecialchars($p['image_path']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>"></div>
            </a>
            <div class="product-info">
                <div class="product-name"><?php echo htmlspecialchars($p['name']); ?></div>
                <div class="product-footer">
                    <div class="product-price">QAR <?php echo number_format($p['price'],2); ?></div>
                    <button class="add-cart-btn" onclick="addToCart(<?php echo $p['id']; ?>)">+ Add</button>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>

<?php elseif ($tab === 'profile'): ?>
    <h2 style="font-size:28px;color:var(--bark);margin-bottom:28px">Profile Settings</h2>
    <?php
    $me = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id=$user_id"));
    if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_profile'])) {
        $n = clean($conn,$_POST['name']); $ph = clean($conn,$_POST['phone']);
        mysqli_query($conn,"UPDATE users SET name='$n',phone='$ph' WHERE id=$user_id");
        $_SESSION['user_name']=$n;
        echo '<div class="form-success">Profile updated successfully!</div>';
    }
    ?>
    <form method="POST" style="max-width:480px">
        <div class="form-group" style="margin-bottom:14px"><label class="form-label">Full Name</label><input class="form-input" name="name" value="<?php echo htmlspecialchars($me['name']); ?>" required></div>
        <div class="form-group" style="margin-bottom:14px"><label class="form-label">Email</label><input class="form-input" value="<?php echo htmlspecialchars($me['email']); ?>" disabled style="opacity:.6"></div>
        <div class="form-group" style="margin-bottom:24px"><label class="form-label">Phone</label><input class="form-input" name="phone" value="<?php echo htmlspecialchars($me['phone']??''); ?>"></div>
        <button type="submit" name="update_profile" class="btn-primary">Save Changes</button>
    </form>

<?php else: ?>
    <h2 style="font-size:28px;color:var(--bark);margin-bottom:28px">Saved Addresses</h2>
    <p style="color:var(--muted)">Address management coming soon.</p>
<?php endif; ?>
</div>

</div>
</div>
<?php require_once 'includes/footer.php'; ?>
