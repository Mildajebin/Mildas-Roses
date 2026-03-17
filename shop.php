<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
$page_title = 'Shop';

// --- FILTERS ---
$where   = ['1=1'];
$cat_slug = clean($conn, $_GET['category'] ?? '');
$search   = clean($conn, $_GET['search'] ?? '');
$sort     = clean($conn, $_GET['sort'] ?? 'featured');
$min_price = (float)($_GET['min'] ?? 0);
$max_price = (float)($_GET['max'] ?? 99999);

if ($cat_slug) {
    $cat_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM categories WHERE slug='$cat_slug'"));
    if ($cat_row) $where[] = "p.category_id=" . $cat_row['id'];
}
if ($search) $where[] = "(p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
if ($min_price > 0)   $where[] = "p.price >= $min_price";
if ($max_price < 99999) $where[] = "p.price <= $max_price";

switch ($sort) {
    case 'price_asc':  $order = 'p.price ASC'; break;
    case 'price_desc': $order = 'p.price DESC'; break;
    case 'newest':     $order = 'p.created_at DESC'; break;
    case 'rating':     $order = 'p.rating DESC'; break;
    default:           $order = 'p.review_count DESC'; break;
}

$where_sql = implode(' AND ', $where);

// Pagination
$per_page = 12;
$page_num = max(1, (int)($_GET['page'] ?? 1));
$offset   = ($page_num - 1) * $per_page;

$total_res  = mysqli_query($conn, "SELECT COUNT(*) FROM products p WHERE $where_sql");
$total_prods = mysqli_fetch_row($total_res)[0];
$total_pages = ceil($total_prods / $per_page);

$products = mysqli_query($conn, "SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE $where_sql ORDER BY $order LIMIT $per_page OFFSET $offset");

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

require_once 'includes/header.php';
?>
<div class="page-wrap">
<div class="shop-header">
    <h1>Our Collection</h1>
    <p style="color:var(--muted);margin-top:10px;font-size:14px">Handcrafted with love · <?php echo $total_prods; ?> beautiful pieces</p>
</div>

<div class="shop-layout">
<!-- SIDEBAR -->
<aside class="shop-sidebar">
    <form method="GET" id="filter-form">
        <?php if ($search): ?><input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>"><?php endif; ?>

        <div class="filter-group">
            <div class="filter-title">Category</div>
            <?php mysqli_data_seek($categories,0); while ($cat = mysqli_fetch_assoc($categories)): ?>
            <div class="filter-option">
                <input type="radio" name="category" id="cat_<?php echo $cat['id']; ?>"
                    value="<?php echo htmlspecialchars($cat['slug']); ?>"
                    <?php echo $cat_slug === $cat['slug'] ? 'checked' : ''; ?>
                    onchange="this.form.submit()">
                <label for="cat_<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></label>
            </div>
            <?php endwhile; ?>
            <?php if ($cat_slug): ?>
            <div class="filter-option">
                <input type="radio" name="category" id="cat_all" value="" onchange="this.form.submit()">
                <label for="cat_all" style="color:var(--rose)">All Categories</label>
            </div>
            <?php endif; ?>
        </div>

        <div class="filter-group">
            <div class="filter-title">Price Range</div>
            <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px">
                <input class="price-input form-input" type="number" name="min" placeholder="Min" value="<?php echo $min_price ?: ''; ?>" style="width:80px;padding:6px 10px">
                <span style="color:var(--muted)">—</span>
                <input class="price-input form-input" type="number" name="max" placeholder="Max" value="<?php echo $max_price < 99999 ? $max_price : ''; ?>" style="width:80px;padding:6px 10px">
            </div>
            <button type="submit" class="btn-primary" style="width:100%;font-size:12px;padding:10px">Apply</button>
        </div>
    </form>

    <?php if ($cat_slug || $search || $min_price || $max_price < 99999): ?>
    <a href="shop.php" class="btn-outline" style="width:100%;display:block;text-align:center;font-size:12px;padding:10px;margin-top:8px">Clear Filters</a>
    <?php endif; ?>
</aside>

<!-- MAIN -->
<div class="shop-main">
    <div class="shop-toolbar">
        <form method="GET" style="display:flex;align-items:center;flex:1;max-width:340px">
            <?php if ($cat_slug): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($cat_slug); ?>"><?php endif; ?>
            <div class="search-bar" style="flex:1">
                <span>🔍</span>
                <input type="text" name="search" placeholder="Search flowers, bouquets…" value="<?php echo htmlspecialchars($search); ?>">
            </div>
        </form>
        <form method="GET">
            <?php if ($cat_slug): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($cat_slug); ?>"><?php endif; ?>
            <?php if ($search): ?><input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>"><?php endif; ?>
            <select class="sort-select" name="sort" onchange="this.form.submit()">
                <option value="featured"   <?php echo $sort==='featured'  ?'selected':''; ?>>Sort: Featured</option>
                <option value="price_asc"  <?php echo $sort==='price_asc' ?'selected':''; ?>>Price: Low to High</option>
                <option value="price_desc" <?php echo $sort==='price_desc'?'selected':''; ?>>Price: High to Low</option>
                <option value="newest"     <?php echo $sort==='newest'    ?'selected':''; ?>>Newest First</option>
                <option value="rating"     <?php echo $sort==='rating'    ?'selected':''; ?>>Best Rated</option>
            </select>
        </form>
    </div>

    <?php if (mysqli_num_rows($products) === 0): ?>
    <div class="empty-state">
        <div class="icon">🌸</div>
        <h3>No products found</h3>
        <p style="margin-bottom:24px">Try adjusting your filters or search terms.</p>
        <a href="shop.php" class="btn-primary">View All Products</a>
    </div>
    <?php else: ?>
    <div class="products-grid">
        <?php while ($p = mysqli_fetch_assoc($products)): ?>
        <div class="product-card">
            <a href="product.php?id=<?php echo $p['id']; ?>">
                <div class="product-img">
                    <img src="<?php echo htmlspecialchars($p['image_path']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" loading="lazy">
                    <?php if ($p['badge']): ?><div class="product-badge"><?php echo htmlspecialchars($p['badge']); ?></div><?php endif; ?>
                    <button class="wishlist-btn" onclick="event.preventDefault();toggleWishlist(this,<?php echo $p['id']; ?>)">♡</button>
                </div>
            </a>
            <div class="product-info">
                <div class="stars"><?php echo str_repeat('★',(int)$p['rating']).str_repeat('☆',5-(int)$p['rating']); ?> <span style="color:var(--muted);font-size:11px">(<?php echo $p['review_count']; ?>)</span></div>
                <div class="product-name"><a href="product.php?id=<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></a></div>
                <div class="product-desc"><?php echo htmlspecialchars(substr($p['description'],0,68)).'…'; ?></div>
                <div class="product-footer">
                    <div class="product-price">QAR <?php echo number_format($p['price'],2); ?></div>
                    <?php if ($p['stock'] > 0): ?>
                    <button class="add-cart-btn" onclick="addToCart(<?php echo $p['id']; ?>)">+ Add</button>
                    <?php else: ?>
                    <span style="font-size:11px;color:var(--muted)">Out of stock</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- PAGINATION -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php for ($i=1; $i<=$total_pages; $i++): ?>
        <a href="?<?php echo http_build_query(array_merge($_GET,['page'=>$i])); ?>"
           class="<?php echo $i===$page_num?'active':''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
</div>
</div>
<?php require_once 'includes/footer.php'; ?>
