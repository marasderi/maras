<?php
include 'includes/header.php';

// URL'den mağaza (vendor) ID'sini al
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Geçersiz mağaza ID'si.");
}
$vendor_id = intval($_GET['id']);

// Mağaza bilgilerini çek
$stmt = $pdo->prepare("SELECT * FROM vendors WHERE id = ?");
$stmt->execute([$vendor_id]);
$store = $stmt->fetch();

if (!$store) {
    die("Mağaza bulunamadı.");
}

// Bu mağazaya ait aktif ürünleri çek
$product_stmt = $pdo->prepare(
    "SELECT p.*, c.name as category_name 
     FROM products p
     JOIN categories c ON p.category_id = c.id
     WHERE p.vendor_id = ? AND p.is_active = 1 
     ORDER BY p.created_at DESC"
);
$product_stmt->execute([$vendor_id]);
$products = $product_stmt->fetchAll();

?>

<div class="store-profile-header">
    <div class="store-profile-logo">
<?php if (!empty($store['store_logo']) && file_exists('uploads/logos/' . $store['store_logo'])): ?>
    <img src="<?php echo SITE_URL . '/uploads/logos/' . htmlspecialchars($store['store_logo']); ?>" alt="<?php echo htmlspecialchars($store['store_name']); ?> Logo">
<?php else: ?>
    <img src="https://via.placeholder.com/200x200.png?text=<?php echo urlencode(substr($store['store_name'], 0, 1)); ?>" alt="<?php echo htmlspecialchars($store['store_name']); ?> Logo">
<?php endif; ?>    </div>
    <div class="store-profile-info">
        <h1><?php echo htmlspecialchars($store['store_name']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($store['store_description'])); ?></p>
    </div>
</div>

<div class="section-title">
    <h2><?php echo htmlspecialchars($store['store_name']); ?> Mağazasının Ürünleri</h2>
</div>


<div class="product-grid">
    <?php if ($products): ?>
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="<?php echo SITE_URL . '/uploads/products/' . htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="product-info">
                    <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="product-price"><?php echo htmlspecialchars($product['price']); ?> TL</div>
                    <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn-details">Detayları Gör</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Bu mağazanın henüz satışta olan bir ürünü bulunmuyor.</p>
    <?php endif; ?>
</div>


<?php include 'footer.php'; ?>
