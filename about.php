<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
$page_title = 'Our Story';
require_once 'includes/header.php';
?>
<div class="page-wrap">

<div class="about-hero">
    <span class="section-tag" style="color:rgba(255,255,255,.6)">Est. 2018 · Doha, Qatar</span>
    <h1 style="font-size:clamp(36px,5vw,64px);font-weight:300;margin-bottom:20px">Roses Grown from Passion</h1>
    <p style="font-size:16px;opacity:.75;max-width:560px;margin:0 auto;line-height:1.9">What started as a hobby in a small apartment has blossomed into a celebration of handcrafted beauty, bringing lasting florals to homes and hearts across the region.</p>
</div>

<!-- QUOTE -->
<div class="section" style="max-width:800px;margin:0 auto;text-align:center">
    <p style="font-size:22px;color:var(--muted);line-height:1.9;font-family:'Cormorant Garamond',serif;font-style:italic">"We believe every flower should tell a story — and unlike real blooms, ours last forever. Each petal we fold carries intention, care, and a deep love for the craft."</p>
    <p style="font-size:14px;color:var(--muted);margin-top:16px">— Leila Hassan, Founder</p>
</div>

<!-- STORY -->
<section class="section" style="background:var(--blush);border-radius:40px;margin:0 3%">
    <div style="display:flex;gap:60px;align-items:center;flex-wrap:wrap">
        <div style="flex:1;min-width:260px">
            <img src="uploads/products/2.jpg" alt="Our story" style="width:100%;border-radius:20px;object-fit:cover;aspect-ratio:4/3">
        </div>
        <div style="flex:1;min-width:260px">
            <span class="section-tag">How It All Started</span>
            <h2 style="font-size:36px;color:var(--bark);margin-bottom:20px;font-weight:400">From Hobby to Heartfelt Business</h2>
            <p style="color:var(--muted);line-height:1.9;font-size:14px;margin-bottom:16px">In 2018, Leila Hassan began crafting paper flowers as a creative escape. What started as a weekend hobby quickly turned into a passion project when friends and family couldn't stop admiring her work.</p>
            <p style="color:var(--muted);line-height:1.9;font-size:14px;margin-bottom:16px">Today, Milda's Roses is a small but mighty team of three artisans based in Doha, Qatar — each bringing a unique skill to the studio. From paper and fabric to preserved botanicals, every piece is made by hand with love and meticulous attention to detail.</p>
            <p style="color:var(--muted);line-height:1.9;font-size:14px">We ship across the GCC and beyond, bringing a touch of everlasting beauty to weddings, homes, and hearts worldwide.</p>
        </div>
    </div>
</section>

<!-- VALUES -->
<section class="about-values">
    <div class="section-header">
        <span class="section-tag">What We Stand For</span>
        <h2 class="section-title">Our Values</h2>
    </div>
    <div class="values-grid">
        <div class="value-card">
            <span class="value-icon">🌿</span>
            <h3 style="font-size:22px;color:var(--bark);margin-bottom:12px;font-weight:500">Eco-Conscious</h3>
            <p style="font-size:13px;color:var(--muted);line-height:1.8">We use sustainable, recycled, and natural materials wherever possible. Our packaging is 100% biodegradable, and we plant one tree for every order placed.</p>
        </div>
        <div class="value-card">
            <span class="value-icon">✋</span>
            <h3 style="font-size:22px;color:var(--bark);margin-bottom:12px;font-weight:500">Truly Handmade</h3>
            <p style="font-size:13px;color:var(--muted);line-height:1.8">Every single petal, stem, and leaf is crafted by hand by our small team of dedicated artisans. No shortcuts, no machines — just patience and passion.</p>
        </div>
        <div class="value-card">
            <span class="value-icon">🎨</span>
            <h3 style="font-size:22px;color:var(--bark);margin-bottom:12px;font-weight:500">Endlessly Creative</h3>
            <p style="font-size:13px;color:var(--muted);line-height:1.8">We love pushing the boundaries of what's possible with paper and fabric. From realistic roses to avant-garde sculptural pieces, we craft it all with joy.</p>
        </div>
    </div>
</section>

<!-- TEAM -->
<section style="padding:0 8% 80px">
    <div class="section-header">
        <span class="section-tag">The Artisans</span>
        <h2 class="section-title">Meet Our Team</h2>
    </div>
    <div class="team-grid">
        <div class="team-card">
            <div class="team-img" style="background:var(--blush)">👩‍🎨</div>
            <div class="team-info">
                <div style="font-family:'Cormorant Garamond',serif;font-size:22px;color:var(--bark);margin-bottom:4px">Leila Hassan</div>
                <div style="font-size:12px;color:var(--muted);letter-spacing:1px;text-transform:uppercase">Founder &amp; Lead Designer</div>
            </div>
        </div>
        <div class="team-card">
            <div class="team-img" style="background:#dcebd8">👩‍🌾</div>
            <div class="team-info">
                <div style="font-family:'Cormorant Garamond',serif;font-size:22px;color:var(--bark);margin-bottom:4px">Maryam Khalil</div>
                <div style="font-size:12px;color:var(--muted);letter-spacing:1px;text-transform:uppercase">Paper Flower Specialist</div>
            </div>
        </div>
        <div class="team-card">
            <div class="team-img" style="background:#fdf0e0">🧵</div>
            <div class="team-info">
                <div style="font-family:'Cormorant Garamond',serif;font-size:22px;color:var(--bark);margin-bottom:4px">Noor Al-Sayed</div>
                <div style="font-size:12px;color:var(--muted);letter-spacing:1px;text-transform:uppercase">Fabric &amp; Textile Artist</div>
            </div>
        </div>
    </div>
</section>

</div>
<?php require_once 'includes/footer.php'; ?>
