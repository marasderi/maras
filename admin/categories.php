<?php 
include 'header.php';

// Yeni Kategori Ekleme
if (isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)); // Basit slug oluşturma
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
        $stmt->execute([$name, $slug]);
        header("Location: categories.php?status=added");
        exit();
    }
}

// Kategori Silme
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_to_delete = intval($_GET['id']);
    // Önce bu kategoride ürün var mı diye kontrol et
    $stmt = $pdo->prepare("SELECT count(*) FROM products WHERE category_id = ?");
    $stmt->execute([$id_to_delete]);
    $product_count = $stmt->fetchColumn();

    if ($product_count == 0) {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id_to_delete]);
        header("Location: categories.php?status=deleted");
    } else {
        header("Location: categories.php?status=error_has_products");
    }
    exit();
}


$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<div class="vendor-page-header">
    <h1>Kategori Yönetimi</h1>
</div>

<?php if(isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
    <div class="alert alert-success"><p>Kategori başarıyla silindi.</p></div>
<?php elseif(isset($_GET['status']) && $_GET['status'] == 'added'): ?>
    <div class="alert alert-success"><p>Kategori başarıyla eklendi.</p></div>
<?php elseif(isset($_GET['status']) && $_GET['status'] == 'error_has_products'): ?>
    <div class="alert alert-danger"><p>HATA: Bu kategoriye ait ürünler olduğu için silinemez.</p></div>
<?php endif; ?>

<div class="admin-content-split">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Kategori Adı</th>
                    <th>Slug</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td><?php echo htmlspecialchars($category['slug']); ?></td>
                        <td>
                            <a href="#" class="action-btn edit">Düzenle</a>
                            <a href="categories.php?action=delete&id=<?php echo $category['id']; ?>" class="action-btn delete" onclick="return confirm('Emin misiniz?');">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="form-container-split">
        <h3>Yeni Kategori Ekle</h3>
        <form action="categories.php" method="POST">
            <div class="form-group">
                <label for="name">Kategori Adı:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <button type="submit" name="add_category" class="btn">Ekle</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
