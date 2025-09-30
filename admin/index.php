<?php 
include 'header.php'; 

// --- GENEL İSTATİSTİKLER (Tıklanabilir kartlar için) ---
$total_users = $pdo->query("SELECT count(*) FROM users")->fetchColumn();
$total_vendors = $pdo->query("SELECT count(*) FROM vendors")->fetchColumn();
$total_products = $pdo->query("SELECT count(*) FROM products")->fetchColumn();
$total_views = $pdo->query("SELECT SUM(views) FROM products")->fetchColumn() ?: 0;

// --- DETAYLI RAPORLAR ---
// 1. En Çok Görüntülenen 5 Ürün (Tüm Sitede)
$top_viewed_products_stmt = $pdo->query(
    "SELECT name, views FROM products ORDER BY views DESC LIMIT 5"
);
$top_viewed_products = $top_viewed_products_stmt->fetchAll();

// 2. En Çok Ürünü Olan 5 Satıcı
$top_vendors_stmt = $pdo->query(
    "SELECT v.store_name, COUNT(p.id) as product_count 
     FROM vendors v 
     JOIN products p ON v.id = p.vendor_id 
     GROUP BY v.id 
     ORDER BY product_count DESC 
     LIMIT 5"
);
$top_vendors = $top_vendors_stmt->fetchAll();

// 3. En Son Kaydolan 5 Üye (Tarih bilgisi de eklendi)
$recent_users_stmt = $pdo->query(
    "SELECT username, email, created_at FROM users ORDER BY created_at DESC LIMIT 5"
);
$recent_users = $recent_users_stmt->fetchAll();
?>

<!-- "Cıvıl Cıvıl" Karşılama Başlığı -->
<div class="panel-welcome-header">
    <h1>Yönetim Paneline Hoş Geldiniz!</h1>
    <p>Sitenizin genel durumunu ve detaylı raporları aşağıda görebilirsiniz.</p>
</div>

<!-- Tıklanabilir ve İkonlu İstatistik Kutuları -->
<div class="stats-grid">
    <a href="users.php" class="stat-card">
        <div class="stat-icon user-icon">👤</div>
        <div class="stat-info">
            <h3>Toplam Üye</h3>
            <p><?php echo $total_users; ?></p>
        </div>
    </a>
    <a href="users.php?role=vendor" class="stat-card">
        <div class="stat-icon vendor-icon">🏪</div>
        <div class="stat-info">
            <h3>Toplam Satıcı</h3>
            <p><?php echo $total_vendors; ?></p>
        </div>
    </a>
    <a href="products.php" class="stat-card">
        <div class="stat-icon product-icon">📦</div>
        <div class="stat-info">
            <h3>Toplam Ürün</h3>
            <p><?php echo $total_products; ?></p>
        </div>
    </a>
    <a href="products.php" class="stat-card">
        <div class="stat-icon view-icon">👀</div>
        <div class="stat-info">
            <h3>Toplam Görüntülenme</h3>
            <p><?php echo $total_views; ?></p>
        </div>
    </a>
</div>

<!-- Detaylı Rapor Widget'ları -->
<div class="home-grid" style="margin-top: 40px;">
    <div class="home-widget">
        <div class="widget-header"><span class="icon">🔥</span><h3>En Çok Görüntülenenler</h3></div>
        <ul class="widget-list">
            <?php foreach($top_viewed_products as $product): ?>
            <li><a><div class="item-info"><strong><?php echo htmlspecialchars($product['name']); ?></strong><span><?php echo $product['views']; ?> Görüntülenme</span></div></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="home-widget">
        <div class="widget-header"><span class="icon">🏆</span><h3>En Aktif Satıcılar</h3></div>
        <ul class="widget-list">
             <?php foreach($top_vendors as $vendor): ?>
            <li><a><div class="item-info"><strong><?php echo htmlspecialchars($vendor['store_name']); ?></strong><span><?php echo $vendor['product_count']; ?> Ürün</span></div></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="home-widget">
        <div class="widget-header"><span class="icon">🚀</span><h3>Son Kaydolan Üyeler</h3></div>
        <ul class="widget-list">
             <?php foreach($recent_users as $user): ?>
            <li><a><div class="item-info"><strong><?php echo htmlspecialchars($user['username']); ?></strong><span><?php echo date('d M Y', strtotime($user['created_at'])); ?></span></div></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="widget-footer"><a href="users.php">Tüm Üyeler →</a></div>
    </div>
</div>

<?php include 'footer.php'; ?>

