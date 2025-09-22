<?php
include 'includes/header.php';

// Sadece giriş yapmış kullanıcılar bu sayfayı görebilir.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Kullanıcının favori ürünlerini çek
$sql = "SELECT p.*, c.name as category_name, v.store_name 
        FROM products p
        JOIN favorites f ON p.id = f.product_id
        JOIN categories c ON p.category_id = c.id
        JOIN vendors v ON p.vendor_id = v.id
        WHERE f.user_id = ? AND p.is_active = 1
        ORDER BY f.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$products = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>Favorilerim</h1>
    <p>Beğendiğiniz ve daha sonra incelemek için kaydettiğiniz ürünler.</p>
</div>

<div class="product-grid">
    <?php if ($products): ?>
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                 <div class="product-image">
                    <img src="<?php echo SITE_URL . '/uploads/products/' . htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <button class="favorite-btn active" data-product-id="<?php echo $product['id']; ?>">
                        ❤
                    </button>
                 </div>
                 <div class="product-info">
                    <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn-details">Detayları Gör</a>
                 </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Henüz favorilerinize bir ürün eklemediniz.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
