<?php
include 'header.php';

// --- İŞLEMLER ---

// 1. Ürün DURUMUNU DEĞİŞTİRME (Aktif/Pasif yapma)
if (isset($_GET['action']) && $_GET['action'] == 'toggle_status' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $current_status = intval($_GET['status']);
    $new_status = $current_status == 1 ? 0 : 1; // Durumu tersine çevir

    $stmt = $pdo->prepare("UPDATE products SET is_active = ? WHERE id = ?");
    $stmt->execute([$new_status, $product_id]);
    header("Location: products.php?status=toggled");
    exit();
}

// 2. Ürün SİLME
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    // TODO: Ürüne ait resmi de sunucudan silmek iyi bir pratiktir.
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    header("Location: products.php?status=deleted");
    exit();
}


// --- LİSTELEME ---

$search_term = $_GET['search'] ?? '';

// Temel sorgu (tüm ürünler)
$sql = "SELECT p.*, v.store_name, c.name as category_name 
        FROM products p
        JOIN vendors v ON p.vendor_id = v.id
        JOIN categories c ON p.category_id = c.id";
$params = [];

// Arama yapıldıysa
if (!empty($search_term)) {
    $sql .= " WHERE p.name LIKE ?";
    $params[] = "%" . $search_term . "%";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div class="vendor-page-header">
    <h1>Tüm Ürünleri Yönet</h1>
    <form action="products.php" method="GET" class="admin-search-form">
        <input type="search" name="search" placeholder="Ürün adı ile ara..." value="<?php echo htmlspecialchars($search_term); ?>">
        <button type="submit">Ara</button>
    </form>
</div>

<?php // Durum mesajları
if(isset($_GET['status'])) {
    if ($_GET['status'] == 'toggled') echo '<div class="alert alert-success"><p>Ürün durumu başarıyla değiştirildi.</p></div>';
    if ($_GET['status'] == 'deleted') echo '<div class="alert alert-success"><p>Ürün başarıyla silindi.</p></div>';
    if ($_GET['status'] == 'updated') echo '<div class="alert alert-success"><p>Ürün başarıyla güncellendi.</p></div>';
}
?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Ürün Adı</th>
                <th>Mağaza</th>
                <th>Kategori</th>
                <th>Fiyat</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($products as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['store_name']); ?></td>
                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                <td><?php echo $product['price']; ?> TL</td>
                <td>
                    <?php if ($product['is_active']): ?>
                        <a href="?action=toggle_status&id=<?php echo $product['id'];?>&status=1" class="status-btn active">Aktif</a>
                    <?php else: ?>
                        <a href="?action=toggle_status&id=<?php echo $product['id'];?>&status=0" class="status-btn inactive">Pasif</a>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="action-btn edit">Düzenle</a>
                    <a href="?action=delete&id=<?php echo $product['id']; ?>" class="action-btn delete" onclick="return confirm('Bu ürünü kalıcı olarak silmek istediğinizden emin misiniz?');">Sil</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
