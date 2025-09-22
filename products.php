<?php
include 'includes/header.php';

// Temel SQL Sorgusu
$sql = "SELECT p.*, c.name as category_name, v.store_name 
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN vendors v ON p.vendor_id = v.id
        WHERE p.is_active = 1";

// TODO: Filtreleme mantığı buraya eklenecek

// Şimdilik tüm ürünleri çekiyoruz
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll();

?>

<div class="page-content">
    <aside class="sidebar">
        <h3>Filtrele</h3>
        <div class="filter-group">
            <h4>Kategori</h4>
            <ul>
                <li><a href="#">Tümü</a></li>
                <?php
                    // Kategorileri veritabanından çek ve listele
                    $cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
                    while ($category = $cat_stmt->fetch()) {
                        echo '<li><a href="#">' . htmlspecialchars($category['name']) . '</a></li>';
                    }
                ?>
            </ul>
        </div>
        <div class="filter-group">
            <h4>Fiyat Aralığı</h4>
            <input type="range" min="0" max="5000" value="5000">
        </div>
        <div class="filter-group">
            <h4>Renk</h4>
            </div>
    </aside>

    <section class="main-content">
        <h2>Tüm Ürünler</h2>
        <div class="product-grid">
            <?php if ($products): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://via.placeholder.com/300x300.png?text=Deri+Urun" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-vendor"><?php echo htmlspecialchars($product['store_name']); ?></p>
                            <div class="product-price"><?php echo htmlspecialchars($product['price']); ?> TL</div>
                            <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn-details">Detayları Gör</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Gösterilecek ürün bulunamadı.</p>
            <?php endif; ?>
        </div>
    </section>
</div>


<?php include 'includes/footer.php'; ?>
