<?php 
include 'header.php'; 

// --- GENEL İSTATİSTİKLER ---
$total_users = $pdo->query("SELECT count(*) FROM users")->fetchColumn();
$total_vendors = $pdo->query("SELECT count(*) FROM vendors")->fetchColumn();
$total_products = $pdo->query("SELECT count(*) FROM products")->fetchColumn();
$total_views = $pdo->query("SELECT SUM(views) FROM products")->fetchColumn() ?: 0;

// --- DETAYLI RAPORLAR ---
// 1. En Çok Görüntülenen 5 Ürün (Tüm Sitede)
$top_viewed_products = $pdo->query(
    "SELECT name, views FROM products ORDER BY views DESC LIMIT 5"
);

// 2. En Çok Ürünü Olan 5 Satıcı
$top_vendors = $pdo->query(
    "SELECT v.store_name, COUNT(p.id) as product_count 
     FROM vendors v 
     JOIN products p ON v.id = p.vendor_id 
     GROUP BY v.id 
     ORDER BY product_count DESC 
     LIMIT 5"
);

// 3. En Son Kaydolan 5 Üye
$recent_users = $pdo->query(
    "SELECT username, email FROM users ORDER BY created_at DESC LIMIT 5"
);

?>

<div class="dashboard-welcome">
    <h1>Yönetim Paneline Hoş Geldiniz!</h1>
    <p>Sitenizin genel durumunu ve detaylı raporları aşağıda görebilirsiniz.</p>
</div>

<div class="stats-grid">
    <div class="stat-card"><h3>Toplam Üye</h3><p><?php echo $total_users; ?></p></div>
    <div class="stat-card"><h3>Toplam Satıcı</h3><p><?php echo $total_vendors; ?></p></div>
    <div class="stat-card"><h3>Toplam Ürün</h3><p><?php echo $total_products; ?></p></div>
    <div class="stat-card"><h3>Toplam Görüntülenme</h3><p><?php echo $total_views; ?></p></div>
</div>

<div class="reports-grid">
    <div class="report-widget">
        <h3>Sitede En Çok Görüntülenen Ürünler</h3>
        <ul class="report-list">
            <?php while($product = $top_viewed_products->fetch()): ?>
                <li>
                    <span><?php echo htmlspecialchars($product['name']); ?></span>
                    <span class="report-value"><?php echo $product['views']; ?> Görüntülenme</span>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
    <div class="report-widget">
        <h3>En Çok Ürünü Olan Satıcılar</h3>
        <ul class="report-list">
             <?php while($vendor = $top_vendors->fetch()): ?>
                <li>
                    <span><?php echo htmlspecialchars($vendor['store_name']); ?></span>
                    <span class="report-value"><?php echo $vendor['product_count']; ?> Ürün</span>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
    <div class="report-widget">
        <h3>Son Kaydolan Üyeler</h3>
        <ul class="report-list">
             <?php while($user = $recent_users->fetch()): ?>
                <li>
                    <span><?php echo htmlspecialchars($user['username']); ?></span>
                    <span class="report-value"><?php echo htmlspecialchars($user['email']); ?></span>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>

<?php include 'footer.php'; ?>
