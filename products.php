<?php
include 'includes/header.php';

// --- FİLTRELEME İÇİN VERİLERİ HAZIRLA ---

// URL'den gelen GET parametrelerini güvenli bir şekilde alalım.
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$sort_order = $_GET['sort'] ?? 'newest'; // Varsayılan: en yeni
$search_term = $_GET['search'] ?? '';

// --- DİNAMİK SQL SORGUSUNU OLUŞTUR ---

// Sorgunun temel, değişmeyen kısmı
$sql = "SELECT p.*, c.name as category_name, v.store_name 
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN vendors v ON p.vendor_id = v.id
        WHERE p.is_active = 1";

// PDO'da prepared statements için kullanılacak parametre dizisi
$params = [];

// Kategori filtresi eklendiyse
if ($category_id > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
}

// Arama terimi girildiyse (ürün adı VEYA açıklamasında ara)
if (!empty($search_term)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%" . $search_term . "%";
    $params[] = "%" . $search_term . "%";
}

// Sıralama mantığı
// SQL Injection'ı önlemek için sıralama değerini beyaz listeye alıyoruz (whitelist)
$orderBy = " ORDER BY p.created_at DESC"; // Varsayılan
if ($sort_order == 'price_asc') {
    $orderBy = " ORDER BY p.price ASC";
} elseif ($sort_order == 'price_desc') {
    $orderBy = " ORDER BY p.price DESC";
}
$sql .= $orderBy;


// Hazırlanan sorguyu çalıştır
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Kenar çubuğundaki kategori listesi için kategorileri çek
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

?>

<div class="page-content">
    <aside class="sidebar">
        <h3>Filtrele</h3>
        
        <form action="products.php" method="GET" class="filter-form">
            <div class="filter-group">
                <h4>Arama</h4>
                <input type="search" name="search" placeholder="Ürün adı ara..." value="<?php echo htmlspecialchars($search_term); ?>">
            </div>

            <div class="filter-group">
                <h4>Kategori</h4>
                <ul>
                    <li class="<?php if ($category_id == 0) echo 'active'; ?>">
                        <a href="products.php">Tümü</a>
                    </li>
                    <?php foreach ($categories as $category): ?>
                        <li class="<?php if ($category_id == $category['id']) echo 'active'; ?>">
                            <a href="products.php?category=<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <?php if ($category_id > 0): ?>
                <input type="hidden" name="category" value="<?php echo $category_id; ?>">
            <?php endif; ?>
            <?php if (!empty($search_term)): ?>
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_term); ?>">
            <?php endif; ?>

            <div class="filter-group">
                 <h4>Sırala</h4>
                 <select name="sort" onchange="this.form.submit()">
                    <option value="newest" <?php if ($sort_order == 'newest') echo 'selected'; ?>>En Yeni Ürünler</option>
                    <option value="price_asc" <?php if ($sort_order == 'price_asc') echo 'selected'; ?>>Fiyata Göre Artan</option>
                    <option value="price_desc" <?php if ($sort_order == 'price_desc') echo 'selected'; ?>>Fiyata Göre Azalan</option>
                 </select>
            </div>
            
            <button type="submit" class="btn">Filtrele</button>

        </form>
    </aside>

    <section class="main-content">
        <h2>
            <?php
            // Sayfa başlığını filtreye göre dinamik yapalım
            if ($category_id > 0) {
                foreach ($categories as $cat) { if ($cat['id'] == $category_id) echo htmlspecialchars($cat['name']); }
            } elseif (!empty($search_term)) {
                echo '"' . htmlspecialchars($search_term) . '" için arama sonuçları';
            } else {
                echo 'Tüm Ürünler';
            }
            ?>
        </h2>
        
        <div class="product-grid">
            <?php if ($products): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                               <?php if (isset($_SESSION['user_id'])): // Sadece giriş yapmışsa butonu göster ?>
    <button class="favorite-btn <?php echo in_array($product['id'], $user_favorites) ? 'active' : ''; ?>" 
            data-product-id="<?php echo $product['id']; ?>">
        ❤
    </button>
<?php endif; ?> 
<img src="<?php echo SITE_URL . '/uploads/products/' . htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
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
                <p>Aradığınız kriterlere uygun ürün bulunamadı.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
