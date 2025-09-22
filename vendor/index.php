<?php 
include 'header.php'; 

// --- SATICIYA ÖZEL İSTATİSTİKLER ---

// 1. Toplam Ürün Görüntülenmesi
$total_views_stmt = $pdo->prepare("SELECT SUM(views) FROM products WHERE vendor_id = ?");
$total_views_stmt->execute([$current_vendor_id]);
$total_views = $total_views_stmt->fetchColumn() ?: 0;

// 2. Toplam Ürün Favorilenmesi
$total_favs_stmt = $pdo->prepare(
    "SELECT COUNT(f.id) FROM favorites f JOIN products p ON f.product_id = p.id WHERE p.vendor_id = ?"
);
$total_favs_stmt->execute([$current_vendor_id]);
$total_favorites = $total_favs_stmt->fetchColumn() ?: 0;

// 3. Toplam Ürün Sayısı
$total_products_stmt = $pdo->prepare("SELECT COUNT(id) FROM products WHERE vendor_id = ?");
$total_products_stmt->execute([$current_vendor_id]);
$total_products = $total_products_stmt->fetchColumn() ?: 0;

// 4. En Çok Görüntülenen 5 Ürünü
$top_viewed_products = $pdo->prepare(
    "SELECT name, views FROM products WHERE vendor_id = ? ORDER BY views DESC LIMIT 5"
);
$top_viewed_products->execute([$current_vendor_id]);

// 5. En Çok Favorilenen 5 Ürünü
$top_favorited_products = $pdo->prepare(
    "SELECT p.name, COUNT(f.id) as fav_count 
     FROM products p 
     LEFT JOIN favorites f ON p.id = f.product_id 
     WHERE p.vendor_id = ? 
     GROUP BY p.id 
     ORDER BY fav_count DESC 
     LIMIT 5"
);
$top_favorited_products->execute([$current_vendor_id]);

?>

<div class="dashboard-welcome">
    <h1>Hoş Geldiniz, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Mağazanızın performansını aşağıda görebilirsiniz.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Toplam Ürün Görüntülenme</h3>
        <p><?php echo $total_views; ?></p>
    </div>
    <div class="stat-card">
        <h3>Toplam Favorilenme</h3>
        <p><?php echo $total_favorites; ?></p>
    </div>
    <div class="stat-card">
        <h3>Listelenen Ürün Sayısı</h3>
        <p><?php echo $total_products; ?></p>
    </div>
</div>

<div class="reports-grid">
    <div class="report-widget">
        <h3>En Çok Görüntülenen Ürünleriniz</h3>
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
        <h3>En Çok Favorilenen Ürünleriniz</h3>
        <ul class="report-list">
             <?php while($product = $top_favorited_products->fetch()): ?>
                <li>
                    <span><?php echo htmlspecialchars($product['name']); ?></span>
                    <span class="report-value"><?php echo $product['fav_count']; ?> Favori</span>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>


<?php include 'footer.php'; ?>
