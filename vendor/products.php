<?php
include 'header.php';

// Silme işlemi
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $product_id_to_delete = intval($_GET['id']);
    // Güvenlik: Bu ürün gerçekten bu satıcıya mı ait?
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND vendor_id = ?");
    $stmt->execute([$product_id_to_delete, $current_vendor_id]);
    // Yönlendirme ile sayfayı yenile ve silme parametrelerini temizle
    header("Location: products.php?status=deleted");
    exit();
}

// Sadece bu satıcıya ait ürünleri çek
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.vendor_id = ? ORDER BY p.created_at DESC");
$stmt->execute([$current_vendor_id]);
$products = $stmt->fetchAll();
?>

<div class="vendor-page-header">
    <h1>Ürünlerim</h1>
    <a href="add-product.php" class="btn">Yeni Ürün Ekle</a>
</div>

<?php if(isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
    <div class="alert alert-success"><p>Ürün başarıyla silindi.</p></div>
<?php elseif(isset($_GET['status']) && $_GET['status'] == 'added'): ?>
    <div class="alert alert-success"><p>Ürün başarıyla eklendi.</p></div>
<?php elseif(isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
     <div class="alert alert-success"><p>Ürün başarıyla güncellendi.</p></div>
<?php endif; ?>


<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Ürün Adı</th>
                <th>Kategori</th>
                <th>Fiyat</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($products): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($product['price']); ?> TL</td>
                        <td><?php echo $product['is_active'] ? 'Aktif' : 'Pasif'; ?></td>
                        <td>
                            <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="action-btn edit">Düzenle</a>
                            <a href="products.php?action=delete&id=<?php echo $product['id']; ?>" class="action-btn delete" onclick="return confirm('Bu ürünü silmek istediğinizden emin misiniz?');">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Henüz hiç ürün eklemediniz.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<?php include 'footer.php'; ?>
