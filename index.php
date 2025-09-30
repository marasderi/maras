<?php 
include 'includes/header.php'; 

// --- ANA SAYFA VERİLERİNİ ÇEK ---

// 1. Yeni Eklenen Son 6 Ürün
$latest_products_stmt = $pdo->query(
    "SELECT p.id, p.name, p.image, v.store_name 
     FROM products p
     JOIN vendors v ON p.vendor_id = v.id
     WHERE p.is_active = 1 
     ORDER BY p.created_at DESC 
     LIMIT 6"
);
$latest_products = $latest_products_stmt->fetchAll();

// 2. Öne Çıkan Mağazalar (En çok ürünü olan ilk 3)
$featured_stores_stmt = $pdo->query(
    "SELECT v.id, v.store_name, v.store_logo, COUNT(p.id) as product_count
     FROM vendors v
     LEFT JOIN products p ON v.id = p.vendor_id AND p.is_active = 1
     GROUP BY v.id
     ORDER BY product_count DESC
     LIMIT 3"
);
$featured_stores = $featured_stores_stmt->fetchAll();

// 3. En Beğenilen Ürünler (En çok favorilenen ilk 3)
$top_favorites_stmt = $pdo->query(
    "SELECT p.id, p.name, p.image, COUNT(f.id) as fav_count
     FROM products p
     LEFT JOIN favorites f ON p.id = f.product_id
     WHERE p.is_active = 1
     GROUP BY p.id
     ORDER BY fav_count DESC
     LIMIT 3"
);
$top_favorites = $top_favorites_stmt->fetchAll();
?>

<div class="hero-section">
    <h1><?php echo htmlspecialchars($settings['site_name']); ?></h1>
    <p><?php echo htmlspecialchars($settings['site_description']); ?></p>
    <a href="products.php" class="hero-btn">Koleksiyonu Keşfet</a>
</div>

<div class="home-grid">

    <div class="home-widget">
        <div class="widget-header">
            <span class="icon">⭐</span>
            <h3>Öne Çıkan Mağazalar</h3>
        </div>
        <ul class="widget-list">
            <?php if (!empty($featured_stores)): foreach($featured_stores as $store): ?>
            <li>
                <a href="store-profile.php?id=<?php echo $store['id']; ?>">
                    <?php if (!empty($store['store_logo']) && file_exists('uploads/logos/' . $store['store_logo'])): ?>
                        <img src="<?php echo SITE_URL . '/uploads/logos/' . htmlspecialchars($store['store_logo']); ?>" alt="Logo">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/150x150.png?text=<?php echo urlencode(substr($store['store_name'], 0, 1)); ?>" alt="Logo">
                    <?php endif; ?>
                    <div class="item-info">
                        <strong><?php echo htmlspecialchars($store['store_name']); ?></strong>
                        <span><?php echo $store['product_count']; ?> Ürün</span>
                    </div>
                </a>
            </li>
            <?php endforeach; else: ?>
                <li><p style="padding: 10px;">Henüz öne çıkan mağaza yok.</p></li>
            <?php endif; ?>
        </ul>
        <div class="widget-footer">
            <a href="stores.php">Tüm Mağazalar →</a>
        </div>
    </div>

    <div class="home-widget">
        <div class="widget-header">
            <span class="icon">❤️</span>
            <h3>En Beğenilenler</h3>
        </div>
        <ul class="widget-list">
            <?php if (!empty($top_favorites)): foreach($top_favorites as $product): ?>
            <li>
                <a href="product-details.php?id=<?php echo $product['id']; ?>">
                    <img src="<?php echo SITE_URL . '/uploads/products/' . htmlspecialchars($product['image']); ?>" alt="Ürün Resmi">
                    <div class="item-info">
                        <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                        <span><?php echo $product['fav_count']; ?> beğeni</span>
                    </div>
                </a>
            </li>
            <?php endforeach; else: ?>
                <li><p style="padding: 10px;">Henüz beğenilen ürün yok.</p></li>
            <?php endif; ?>
        </ul>
    </div>
    
    <div class="home-widget">
        <div class="widget-header">
            <span class="icon">✨</span>
            <h3>Yeni Eklenenler</h3>
        </div>
        <ul class="widget-list">
             <?php if (!empty($latest_products)): foreach($latest_products as $product): ?>
            <li>
                <a href="product-details.php?id=<?php echo $product['id']; ?>">
                    <img src="<?php echo SITE_URL . '/uploads/products/' . htmlspecialchars($product['image']); ?>" alt="Ürün Resmi">
                    <div class="item-info">
                        <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                        <span><?php echo htmlspecialchars($product['store_name']); ?></span>
                    </div>
                </a>
            </li>
            <?php endforeach; else: ?>
                <li><p style="padding: 10px;">Henüz yeni ürün eklenmemiş.</p></li>
            <?php endif; ?>
        </ul>
        <div class="widget-footer">
            <a href="products.php">Tüm Ürünler →</a>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>
```eof
---
### **2. Yönetici Paneli Ana Sayfası (`admin/index.php`)**
Bu, admin panelinin yeni, "cıvıl cıvıl" ve tıklanabilir ana sayfasıdır.

**Konum:** `public_html/admin/index.php`
```php:Yenilenmiş Admin Paneli (Son Hali):admin/index.php
<?php 
include 'header.php'; 

// --- GENEL İSTATİSTİKLER ---
$total_users = $pdo->query("SELECT count(*) FROM users")->fetchColumn();
$total_vendors = $pdo->query("SELECT count(*) FROM vendors")->fetchColumn();
$total_products = $pdo->query("SELECT count(*) FROM products")->fetchColumn();
$total_views = $pdo->query("SELECT SUM(views) FROM products")->fetchColumn() ?: 0;
?>

<div class="panel-welcome-header">
    <h1>Yönetim Paneline Hoş Geldiniz!</h1>
    <p>Sitenizin genel durumunu ve kısayolları buradan yönetebilirsiniz.</p>
</div>

<div class="stats-grid">
    <a href="users.php" class="stat-card">
        <h3>Toplam Üye</h3>
        <p><?php echo $total_users; ?></p>
    </a>
    <a href="users.php?role=vendor" class="stat-card">
        <h3>Toplam Satıcı</h3>
        <p><?php echo $total_vendors; ?></p>
    </a>
    <a href="products.php" class="stat-card">
        <h3>Toplam Ürün</h3>
        <p><?php echo $total_products; ?></p>
    </a>
    <a href="products.php" class="stat-card">
        <h3>Toplam Görüntülenme</h3>
        <p><?php echo $total_views; ?></p>
    </a>
</div>

<?php include 'footer.php'; ?>
```eof
---
### **3. Satıcı Paneli Ana Sayfası (`vendor/index.php`)**
Bu da satıcı panelinin yeni, modern ve tıklanabilir ana sayfasıdır.

**Konum:** `public_html/vendor/index.php`
```php:Yenilenmiş Satıcı Paneli (Son Hali):vendor/index.php
<?php 
include 'header.php'; 

// --- SATICIYA ÖZEL İSTATİSTİKLER ---
$total_views = $pdo->prepare("SELECT SUM(views) FROM products WHERE vendor_id = ?");
$total_views->execute([$current_vendor_id]);
$total_views = $total_views->fetchColumn() ?: 0;

$total_favs = $pdo->prepare("SELECT COUNT(f.id) FROM favorites f JOIN products p ON f.product_id = p.id WHERE p.vendor_id = ?");
$total_favs->execute([$current_vendor_id]);
$total_favorites = $total_favs->fetchColumn() ?: 0;

$total_products = $pdo->prepare("SELECT COUNT(id) FROM products WHERE vendor_id = ?");
$total_products->execute([$current_vendor_id]);
$total_products = $total_products->fetchColumn() ?: 0;
?>

<div class="panel-welcome-header">
    <h1>Hoş Geldiniz, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Mağazanızın performansını anlık olarak takip edin ve ürünlerinizi yönetin.</p>
</div>

<div class="stats-grid">
    <a href="products.php" class="stat-card">
        <h3>Ürün Görüntülenme</h3>
        <p><?php echo $total_views; ?></p>
    </a>
    <a href="#" class="stat-card"> <h3>Toplam Favorilenme</h3>
        <p><?php echo $total_favorites; ?></p>
    </a>
    <a href="products.php" class="stat-card">
        <h3>Listelenen Ürün</h3>
        <p><?php echo $total_products; ?></p>
    </a>
     <a href="add-product.php" class="stat-card">
        <h3>Yeni Ürün Ekle</h3>
        <p style="font-size: 36px;">+</p>
    </a>
</div>

<?php include 'footer.php'; ?>
```eof
