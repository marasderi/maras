<?php
include 'includes/header.php';

// URL'den ürün ID'sini al, eğer yoksa veya sayı değilse hata ver.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // header("Location: products.php"); // veya bir hata sayfası göster
    die("Geçersiz ürün ID'si.");
}
$product_id = intval($_GET['id']);

// İlgili ürünü tüm bilgileriyle çek
$sql = "SELECT p.*, c.name as category_name, v.store_name, v.store_description
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN vendors v ON p.vendor_id = v.id
        WHERE p.id = ? AND p.is_active = 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([$product_id]);
if ($product) {
    $update_views_stmt = $pdo->prepare("UPDATE products SET views = views + 1 WHERE id = ?");
    $update_views_stmt->execute([$product_id]);
}
// Eğer ürün bulunamazsa
if (!$product) {
    die("Ürün bulunamadı veya pasif durumda.");
}

?>

<div class="product-detail-container">
    <div class="product-detail-image">
<img src="<?php echo SITE_URL . '/uploads/products/' . htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    </div>
    <div class="product-detail-info">
        <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="product-detail-vendor">
            Satıcı: <strong><?php echo htmlspecialchars($product['store_name']); ?></strong>
        </div>
        <div class="product-detail-price">
            <?php echo htmlspecialchars($product['price']); ?> TL
        </div>
        <div class="product-description">
            <h3>Ürün Açıklaması</h3>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        </div>
        <div class="product-meta">
            <p><strong>Renk Seçenekleri:</strong> <?php echo htmlspecialchars($product['color']); ?></p>
        </div>
        <a href="<?php echo htmlspecialchars($product['external_url']); ?>" class="btn-buy" target="_blank">Satın Almak İçin Mağazaya Git</a>
        <p class="external-link-info">Bu ürün satıcının kendi sitesine yönlendirilerek satılmaktadır.</p>
    </div>
</div>


<?php include 'includes/footer.php'; ?>
