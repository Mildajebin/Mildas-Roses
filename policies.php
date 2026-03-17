<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
$page_title = 'Policies';
$section = $_GET['s'] ?? 'shipping';
require_once 'includes/header.php';
?>
<div class="page-wrap">
<div class="shop-header">
    <h1 style="font-size:clamp(32px,4vw,52px);font-weight:300;color:var(--bark)">Policies &amp; Legal</h1>
</div>

<div style="max-width:800px;margin:0 auto;padding:40px 5% 80px;display:flex;gap:40px;flex-wrap:wrap">
    <!-- Sidebar nav -->
    <div style="width:180px;flex-shrink:0">
        <ul style="list-style:none;position:sticky;top:80px">
            <?php foreach(['shipping'=>'Shipping','returns'=>'Returns','privacy'=>'Privacy','terms'=>'Terms'] as $k=>$v): ?>
            <li style="margin-bottom:4px">
                <a href="?s=<?php echo $k; ?>" style="display:block;padding:8px 14px;border-radius:8px;font-size:13px;<?php echo $section===$k?'background:var(--rose);color:white':'color:var(--muted)'; ?>">
                    <?php echo $v; ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Content -->
    <div style="flex:1;min-width:260px">
        <?php if ($section === 'shipping'): ?>
        <h2 style="font-size:28px;color:var(--bark);margin-bottom:20px">Shipping Policy</h2>
        <div style="color:var(--muted);line-height:1.9;font-size:14px">
            <p><strong style="color:var(--bark)">Processing Time:</strong> All orders are handmade to order and take 3–5 business days to prepare.</p>
            <p style="margin-top:16px"><strong style="color:var(--bark)">Standard Delivery:</strong> 3–5 business days via DHL — QAR 25</p>
            <p style="margin-top:8px"><strong style="color:var(--bark)">Express Delivery:</strong> 1–2 business days via FedEx Priority — QAR 65</p>
            <p style="margin-top:8px"><strong style="color:var(--bark)">Same-Day Gift Delivery:</strong> Doha only, order by 12pm — QAR 95</p>
            <p style="margin-top:8px"><strong style="color:var(--bark)">Free Shipping:</strong> On all orders over QAR 200.</p>
            <p style="margin-top:16px">We ship across Qatar and the GCC. International shipping is available on request — please contact us.</p>
        </div>

        <?php elseif ($section === 'returns'): ?>
        <h2 style="font-size:28px;color:var(--bark);margin-bottom:20px">Returns &amp; Refunds</h2>
        <div style="color:var(--muted);line-height:1.9;font-size:14px">
            <p>We want you to be completely happy with your purchase. If for any reason you're not satisfied, we're here to help.</p>
            <p style="margin-top:16px"><strong style="color:var(--bark)">Return Window:</strong> 14 days from date of delivery.</p>
            <p style="margin-top:8px"><strong style="color:var(--bark)">Condition:</strong> Items must be unused and in original packaging.</p>
            <p style="margin-top:8px"><strong style="color:var(--bark)">Custom Orders:</strong> Cannot be returned unless damaged or defective.</p>
            <p style="margin-top:8px"><strong style="color:var(--bark)">Refunds:</strong> Processed within 5–7 business days to original payment method.</p>
            <p style="margin-top:16px">To initiate a return, email us at hello@mildasroses.qa with your order number and reason.</p>
        </div>

        <?php elseif ($section === 'privacy'): ?>
        <h2 style="font-size:28px;color:var(--bark);margin-bottom:20px">Privacy Policy</h2>
        <div style="color:var(--muted);line-height:1.9;font-size:14px">
            <p>Milda's Roses ("we", "us") is committed to protecting your personal information.</p>
            <p style="margin-top:16px"><strong style="color:var(--bark)">Data We Collect:</strong> Name, email, phone, shipping address, and order history.</p>
            <p style="margin-top:8px"><strong style="color:var(--bark)">How We Use It:</strong> To process orders, communicate with you, and improve our services.</p>
            <p style="margin-top:8px"><strong style="color:var(--bark)">Third Parties:</strong> We share data only with shipping partners and payment processors (Stripe, PayPal) as necessary to fulfil your order.</p>
            <p style="margin-top:8px"><strong style="color:var(--bark)">Your Rights:</strong> You may request deletion of your data at any time by emailing us.</p>
            <p style="margin-top:8px"><strong style="color:var(--bark)">Cookies:</strong> We use session cookies for cart functionality only.</p>
        </div>

        <?php else: ?>
        <h2 style="font-size:28px;color:var(--bark);margin-bottom:20px">Terms &amp; Conditions</h2>
        <div style="color:var(--muted);line-height:1.9;font-size:14px">
            <p>By placing an order with Milda's Roses, you agree to the following terms.</p>
            <p style="margin-top:16px"><strong style="color:var(--bark)">Products:</strong> All items are handmade and may vary slightly from photos — this is part of their handcrafted charm.</p>
            <p style="margin-top:8px"><strong style="color:var(--bark)">Pricing:</strong> All prices are in Qatari Riyal (QAR) and include applicable VAT.</p>
            <p style="margin-top:8px"><strong style="color:var(--bark)">Orders:</strong> Orders are confirmed upon successful payment. We reserve the right to cancel orders for any reason, with a full refund.</p>
            <p style="margin-top:8px"><strong style="color:var(--bark)">Intellectual Property:</strong> All designs, photos, and content on this site are the property of Milda's Roses and may not be reproduced without permission.</p>
            <p style="margin-top:8px"><strong style="color:var(--bark)">Governing Law:</strong> These terms are governed by the laws of the State of Qatar.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>
<?php require_once 'includes/footer.php'; ?>
