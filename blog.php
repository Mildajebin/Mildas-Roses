<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
$page_title = 'Blog & DIY Tutorials';

// Single post view
$slug = clean($conn, $_GET['post'] ?? '');
if ($slug) {
    $post = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM blog_posts WHERE slug='$slug' AND published=1 LIMIT 1"));
    if (!$post) { header('Location: blog.php'); exit; }
    $page_title = $post['title'];
}

// All posts
$posts = mysqli_query($conn, "SELECT * FROM blog_posts WHERE published=1 ORDER BY created_at DESC");

require_once 'includes/header.php';
?>
<div class="page-wrap">

<?php if ($slug && $post): ?>
<!-- SINGLE POST -->
<div style="max-width:800px;margin:0 auto;padding:60px 5% 80px">
    <div class="breadcrumb" style="padding:0 0 20px">
        <a href="index.php">Home</a> /
        <a href="blog.php">Blog</a> /
        <span><?php echo htmlspecialchars($post['title']); ?></span>
    </div>
    <span style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--rose);margin-bottom:14px;display:block"><?php echo htmlspecialchars($post['tag']); ?></span>
    <h1 style="font-size:clamp(28px,4vw,48px);color:var(--bark);font-weight:400;margin-bottom:16px;line-height:1.2"><?php echo htmlspecialchars($post['title']); ?></h1>
    <p style="font-size:12px;color:var(--muted);margin-bottom:32px">By <?php echo htmlspecialchars($post['author']); ?> · <?php echo date('d F Y', strtotime($post['created_at'])); ?></p>
    <?php if ($post['image_path']): ?>
    <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" style="width:100%;border-radius:16px;margin-bottom:36px;aspect-ratio:16/9;object-fit:cover">
    <?php endif; ?>
    <div style="font-size:15px;color:var(--muted);line-height:2">
        <?php if ($post['body']): ?>
            <?php echo nl2br(htmlspecialchars($post['body'])); ?>
        <?php else: ?>
            <p><?php echo nl2br(htmlspecialchars($post['excerpt'])); ?></p>
            <p style="margin-top:24px">This is a sample blog post for the Milda's Roses website. You can add the full article content in the <strong>blog_posts</strong> table in your database by updating the <code>body</code> column for this post.</p>
            <p style="margin-top:16px">Use the Admin Panel to manage blog posts, or update the database directly via phpMyAdmin.</p>
        <?php endif; ?>
    </div>
    <div style="margin-top:48px;padding-top:32px;border-top:1px solid var(--light-muted)">
        <a href="blog.php" class="btn-outline">← Back to Blog</a>
    </div>
</div>

<?php else: ?>
<!-- BLOG LISTING -->
<div class="shop-header">
    <span class="section-tag">Ideas &amp; Inspiration</span>
    <h1 style="font-size:clamp(36px,5vw,64px);font-weight:300;color:var(--bark)">Blog &amp; DIY Tutorials</h1>
</div>

<div class="blog-grid">
    <?php while ($p = mysqli_fetch_assoc($posts)): ?>
    <a href="blog.php?post=<?php echo urlencode($p['slug']); ?>" class="blog-card" style="display:block">
        <div class="blog-img">
            <?php if ($p['image_path']): ?>
            <img src="<?php echo htmlspecialchars($p['image_path']); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>">
            <?php else: ?>
            <div style="height:100%;background:var(--blush);display:flex;align-items:center;justify-content:center;font-size:60px">🌸</div>
            <?php endif; ?>
        </div>
        <div class="blog-content">
            <span class="blog-tag"><?php echo htmlspecialchars($p['tag']); ?></span>
            <h3 style="font-size:20px;color:var(--bark);line-height:1.3;margin-bottom:10px;font-weight:500"><?php echo htmlspecialchars($p['title']); ?></h3>
            <p style="font-size:13px;color:var(--muted);line-height:1.7;margin-bottom:16px"><?php echo htmlspecialchars(substr($p['excerpt'],0,110)).'…'; ?></p>
            <div style="font-size:11px;color:var(--muted)">By <?php echo htmlspecialchars($p['author']); ?> · <?php echo date('d M Y', strtotime($p['created_at'])); ?></div>
        </div>
    </a>
    <?php endwhile; ?>
</div>
<?php endif; ?>

</div>
<?php require_once 'includes/footer.php'; ?>
