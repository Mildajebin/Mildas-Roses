<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT p.*, c.name AS cat_name, c.slug AS cat_slug FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.id=$id LIMIT 1"));
if (!$product) { header('Location: shop.php'); exit; }

$page_title = $product['name'];

// Reviews
$reviews = mysqli_query($conn, "SELECT r.*, u.name AS user_name FROM reviews r LEFT JOIN users u ON r.user_id=u.id WHERE r.product_id=$id ORDER BY r.created_at DESC");

// Related products
$cat_id   = (int)$product['category_id'];
$related  = mysqli_query($conn, "SELECT * FROM products WHERE category_id=$cat_id AND id<>$id LIMIT 4");

// All product images (use same image for thumbs in this demo)
$thumbs = [$product['image_path']];
// You can add more image columns to products table; here we cycle demo images
$demo_imgs = ['uploads/products/1.jpg','uploads/products/2.jpg','uploads/products/3.jpg','uploads/products/4.jpg'];
foreach ($demo_imgs as $d) if ($d !== $product['image_path']) $thumbs[] = $d;
$thumbs = array_slice($thumbs, 0, 4);

require_once 'includes/header.php';
?>
<div class="page-wrap">
<div class="breadcrumb">
    <a href="index.php">Home</a> /
    <a href="shop.php">Shop</a> /
    <a href="shop.php?category=<?php echo urlencode($product['cat_slug']); ?>"><?php echo htmlspecialchars($product['cat_name']); ?></a> /
    <span><?php echo htmlspecialchars($product['name']); ?></span>
</div>

<div class="detail-layout">
    <!-- GALLERY -->
    <div class="detail-gallery">
        <div class="main-img">
            <img id="main-product-img" src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="thumb-row">
            <?php foreach ($thumbs as $i => $t): ?>
            <div class="thumb <?php echo $i===0?'active':''; ?>" onclick="selectThumb(this,'<?php echo htmlspecialchars($t); ?>')">
                <img src="<?php echo htmlspecialchars($t); ?>" alt="View <?php echo $i+1; ?>" loading="lazy">
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- INFO -->
    <div class="detail-info">
        <div class="stars" style="font-size:14px;margin-bottom:8px">
            <?php echo str_repeat('★',(int)$product['rating']).str_repeat('☆',5-(int)$product['rating']); ?>
            <span style="color:var(--muted);font-size:13px;margin-left:4px">(<?php echo $product['review_count']; ?> reviews)</span>
        </div>

        <h1 class="detail-name"><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="detail-price">QAR <?php echo number_format($product['price'],2); ?></div>
        <p class="detail-desc"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

        <span class="option-label">Select Colour</span>
        <div class="color-options">
            <div class="color-swatch selected" style="background:#d4948a" onclick="selectColor(this)" data-color="Blush Rose"></div>
            <div class="color-swatch" style="background:#f5f0e0;border:1px solid var(--light-muted)" onclick="selectColor(this)" data-color="Cream"></div>
            <div class="color-swatch" style="background:#c0708a" onclick="selectColor(this)" data-color="Deep Pink"></div>
            <div class="color-swatch" style="background:#9b5de5" onclick="selectColor(this)" data-color="Lavender"></div>
            <div class="color-swatch" style="background:#e8b400" onclick="selectColor(this)" data-color="Gold"></div>
        </div>

        <span class="option-label">Size</span>
        <div class="size-options">
            <button class="size-btn selected" onclick="selectSize(this)" data-size="Small">Small</button>
            <button class="size-btn" onclick="selectSize(this)" data-size="Medium">Medium</button>
            <button class="size-btn" onclick="selectSize(this)" data-size="Large">Large</button>
        </div>

        <div class="qty-row">
            <div class="qty-control">
                <button class="qty-btn" onclick="changeQty(this,-1)">−</button>
                <div class="qty-num" id="qty-display">1</div>
                <button class="qty-btn" onclick="changeQty(this,1)">+</button>
            </div>
            <?php if ($product['stock'] > 0): ?>
            <span style="font-size:13px;color:var(--sage)">✓ In Stock (<?php echo $product['stock']; ?> left)</span>
            <?php else: ?>
            <span style="font-size:13px;color:#b5635a">✕ Out of Stock</span>
            <?php endif; ?>
        </div>

        <div class="detail-actions">
            <?php if ($product['stock'] > 0): ?>
            <button class="btn-primary" onclick="
                const qty = parseInt(document.getElementById('qty-display').textContent);
                const color = document.querySelector('.color-swatch.selected')?.dataset.color || '';
                const size  = document.querySelector('.size-btn.selected')?.dataset.size || 'Small';
                addToCart(<?php echo $product['id']; ?>, qty, color, size);">Add to Cart</button>
            <a href="checkout.php?buy_now=<?php echo $product['id']; ?>" class="btn-outline">Buy Now</a>
            <?php endif; ?>
            <button onclick="toggleWishlist(this,<?php echo $product['id']; ?>)" style="width:44px;height:44px;border-radius:50%;border:1px solid var(--light-muted);background:none;cursor:pointer;font-size:18px" title="Wishlist">♡</button>
        </div>

        <div class="detail-features">
            <div class="feature-item">🌿 Eco-friendly materials</div>
            <div class="feature-item">📦 Gift-ready packaging</div>
            <div class="feature-item">🚚 Free shipping over QAR 200</div>
            <div class="feature-item">↩️ 14-day returns</div>
            <div class="feature-item">✂️ Fully customisable</div>
            <div class="feature-item">⏱️ Made to order in 3–5 days</div>
        </div>
    </div>
</div>

<!-- REVIEWS -->
<div class="reviews-section">
    <h2 style="font-size:28px;color:var(--bark);margin-bottom:28px">Customer Reviews</h2>
    <?php if (mysqli_num_rows($reviews) === 0): ?>
    <p style="color:var(--muted)">No reviews yet. Be the first to review this product!</p>
    <?php else: ?>
    <?php while ($rv = mysqli_fetch_assoc($reviews)): ?>
    <div class="review-card">
        <div class="review-header">
            <div>
                <div class="reviewer"><?php echo htmlspecialchars($rv['user_name'] ?? $rv['author']); ?></div>
                <div class="stars"><?php echo str_repeat('★',$rv['rating']).str_repeat('☆',5-$rv['rating']); ?></div>
            </div>
            <div style="font-size:12px;color:var(--muted)"><?php echo date('F Y', strtotime($rv['created_at'])); ?></div>
        </div>
        <p class="review-text"><?php echo htmlspecialchars($rv['body']); ?></p>
    </div>
    <?php endwhile; ?>
    <?php endif; ?>

    <!-- LEAVE REVIEW (logged in users) -->
    <?php if (is_logged_in()): ?>
    <div style="margin-top:32px;padding:28px;background:var(--blush);border-radius:16px">
        <h3 style="font-size:20px;color:var(--bark);margin-bottom:20px">Leave a Review</h3>
        <form method="POST" action="api/review.php">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Your Rating</label>
                    <select name="rating" class="form-input">
                        <option value="5">★★★★★ Excellent</option>
                        <option value="4">★★★★☆ Good</option>
                        <option value="3">★★★☆☆ Average</option>
                        <option value="2">★★☆☆☆ Poor</option>
                        <option value="1">★☆☆☆☆ Terrible</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label class="form-label">Your Review</label>
                <textarea name="body" class="form-input" rows="4" placeholder="Share your experience…" required></textarea>
            </div>
            <button type="submit" class="btn-primary">Submit Review</button>
        </form>
    </div>
    <?php else: ?>
    <p style="margin-top:24px;color:var(--muted);font-size:14px"><a href="login.php" style="color:var(--rose)">Sign in</a> to leave a review.</p>
    <?php endif; ?>
</div>

<!-- RELATED PRODUCTS -->
<?php if (mysqli_num_rows($related) > 0): ?>
<section class="section">
    <div class="section-header">
        <span class="section-tag">You Might Also Like</span>
        <h2 class="section-title">Related Products</h2>
    </div>
    <div class="products-grid">
        <?php while ($rp = mysqli_fetch_assoc($related)): ?>
        <div class="product-card">
            <a href="product.php?id=<?php echo $rp['id']; ?>">
                <div class="product-img">
                    <img src="<?php echo htmlspecialchars($rp['image_path']); ?>" alt="<?php echo htmlspecialchars($rp['name']); ?>" loading="lazy">
                    <?php if ($rp['badge']): ?><div class="product-badge"><?php echo htmlspecialchars($rp['badge']); ?></div><?php endif; ?>
                </div>
            </a>
            <div class="product-info">
                <div class="stars"><?php echo str_repeat('★',(int)$rp['rating']); ?></div>
                <div class="product-name"><a href="product.php?id=<?php echo $rp['id']; ?>"><?php echo htmlspecialchars($rp['name']); ?></a></div>
                <div class="product-footer">
                    <div class="product-price">QAR <?php echo number_format($rp['price'],2); ?></div>
                    <button class="add-cart-btn" onclick="addToCart(<?php echo $rp['id']; ?>)">+ Add</button>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>
<?php endif; ?>

</div>
<?php require_once 'includes/footer.php'; ?>
