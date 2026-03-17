<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
$page_title = 'Handmade Floral Boutique';

// Fetch 6 featured products
$featured = mysqli_query($conn, "SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id ORDER BY p.review_count DESC LIMIT 6");

// Fetch categories with count
$categories = mysqli_query($conn, "SELECT c.*, COUNT(p.id) AS product_count FROM categories c LEFT JOIN products p ON p.category_id=c.id GROUP BY c.id");

require_once 'includes/header.php';
?>
<div class="page-wrap">

<!-- HERO -->
<section class="hero">
    <div class="hero-img-wrap">
        <img src="uploads/products/1.jpg" alt="Handmade flowers">
        <div class="hero-img-overlay"></div>
    </div>
    <div class="hero-content">
        <span class="hero-tag">Handcrafted with love · Since 2018</span>
        <h1>Where Every Rose Tells a <em>Story</em></h1>
        <p>Handmade paper, fabric &amp; preserved flower arrangements crafted for life's most beautiful moments.</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap">
            <a href="shop.php" class="btn-primary">Shop Now</a>
            <a href="about.php" class="btn-outline">Our Story</a>
        </div>
        <div class="hero-stats">
            <div><div class="stat-num">2,400+</div><div class="stat-label">Happy Customers</div></div>
            <div><div class="stat-num">180+</div><div class="stat-label">Unique Designs</div></div>
            <div><div class="stat-num">4.9★</div><div class="stat-label">Average Rating</div></div>
        </div>
    </div>
</section>

<!-- CATEGORIES -->
<section class="section">
    <div class="section-header">
        <span class="section-tag">Browse by Collection</span>
        <h2 class="section-title">Find Your Perfect Arrangement</h2>
    </div>
    <div class="categories-grid">
        <?php
        $cat_imgs = ['bouquets'=>'1.jpg','wedding-decor'=>'3.jpg','gift-sets'=>'2.jpg','custom-designs'=>'4.jpg'];
        mysqli_data_seek($categories, 0);
        while ($cat = mysqli_fetch_assoc($categories)):
            $img = $cat_imgs[$cat['slug']] ?? '1.jpg';
        ?>
        <a href="shop.php?category=<?php echo urlencode($cat['slug']); ?>" class="cat-card">
            <img src="uploads/products/<?php echo $img; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>">
            <div class="cat-card-content">
                <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                <p><?php echo $cat['product_count']; ?> designs</p>
            </div>
        </a>
        <?php endwhile; ?>
    </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="section" style="background:var(--blush);border-radius:40px;margin:0 3%">
    <div class="section-header">
        <span class="section-tag">Bestsellers</span>
        <h2 class="section-title">Most Loved Pieces</h2>
    </div>
    <div class="products-grid">
        <?php while ($p = mysqli_fetch_assoc($featured)): ?>
        <div class="product-card">
            <a href="product.php?id=<?php echo $p['id']; ?>">
                <div class="product-img">
                    <img src="<?php echo htmlspecialchars($p['image_path']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" loading="lazy">
                    <?php if ($p['badge']): ?>
                    <div class="product-badge"><?php echo htmlspecialchars($p['badge']); ?></div>
                    <?php endif; ?>
                    <button class="wishlist-btn" onclick="event.preventDefault();toggleWishlist(this,<?php echo $p['id']; ?>)" title="Wishlist">♡</button>
                </div>
            </a>
            <div class="product-info">
                <div class="stars"><?php echo str_repeat('★', (int)$p['rating']) . str_repeat('☆', 5-(int)$p['rating']); ?> <span style="color:var(--muted);font-size:11px">(<?php echo $p['review_count']; ?>)</span></div>
                <div class="product-name"><a href="product.php?id=<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></a></div>
                <div class="product-desc"><?php echo htmlspecialchars(substr($p['description'],0,70)).'...'; ?></div>
                <div class="product-footer">
                    <div class="product-price">QAR <?php echo number_format($p['price'],2); ?></div>
                    <button class="add-cart-btn" onclick="addToCart(<?php echo $p['id']; ?>)">+ Add</button>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <div style="text-align:center;margin-top:48px">
        <a href="shop.php" class="btn-primary">View All Products</a>
    </div>
</section>

<!-- PROMO BANNER -->
<div class="promo-banner" style="margin:48px 3%;border-radius:32px">
    <div>
        <h2>Custom Orders for Your <em>Special Day</em></h2>
        <p style="color:rgba(255,255,255,.65);font-size:14px;margin-top:12px">Tell us your vision and we'll craft something uniquely yours.</p>
    </div>
    <a href="contact.php" class="btn-gold">Request Custom Order</a>
</div>

<!-- TESTIMONIALS -->
<section class="testimonials">
    <div class="section-header">
        <span class="section-tag">What Our Customers Say</span>
        <h2 class="section-title">Made with Love, Felt in Every Rose</h2>
    </div>
    <div class="testimonials-grid">
        <div class="testimonial-card">
            <div class="stars">★★★★★</div>
            <p class="testimonial-text">"The bridal bouquet exceeded every expectation. Guests couldn't believe it was handmade paper!"</p>
            <div style="display:flex;align-items:center;gap:12px"><div style="width:40px;height:40px;border-radius:50%;background:var(--rose);display:flex;align-items:center;justify-content:center;font-size:18px">👰</div><div><div style="font-size:14px;color:var(--bark);font-weight:500">Sarah M.</div><div style="font-size:11px;color:var(--muted)">Bride · Dubai</div></div></div>
        </div>
        <div class="testimonial-card">
            <div class="stars">★★★★★</div>
            <p class="testimonial-text">"The care and detail put into each petal was extraordinary. A truly special gift."</p>
            <div style="display:flex;align-items:center;gap:12px"><div style="width:40px;height:40px;border-radius:50%;background:var(--sage);display:flex;align-items:center;justify-content:center;font-size:18px">🌷</div><div><div style="font-size:14px;color:var(--bark);font-weight:500">Amira K.</div><div style="font-size:11px;color:var(--muted)">Regular Customer · Doha</div></div></div>
        </div>
        <div class="testimonial-card">
            <div class="stars">★★★★★</div>
            <p class="testimonial-text">"The preserved roses are still gorgeous 6 months later. Worth every riyal!"</p>
            <div style="display:flex;align-items:center;gap:12px"><div style="width:40px;height:40px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:18px">🌹</div><div><div style="font-size:14px;color:var(--bark);font-weight:500">Fatima A.</div><div style="font-size:11px;color:var(--muted)">Gift Buyer · Riyadh</div></div></div>
        </div>
    </div>
</section>

</div>
<?php require_once 'includes/footer.php'; ?>
