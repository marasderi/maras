<?php
include 'header.php';

// URL'den ürün ID'sini al
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Geçersiz ürün ID'si.");
}
$product_id = intval($_GET['id']);

// GÜVENLİK KONTROLÜ: Düzenlenmek istenen ürün, giriş yapmış olan bu satıcıya mı ait?
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND vendor_id = ?");
$stmt->execute([$product_id, $current_vendor_id]);
$product = $stmt->fetch();

// Eğer ürün bu satıcıya ait değilse veya bulunamadıysa, işlemi durdur.
if (!$product) {
    die("Ürün bulunamadı veya bu ürünü düzenleme yetkiniz yok.");
}


$errors = [];

// Form güncellenmek için gönderildiyse
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Formdan gelen verileri al
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $color = trim($_POST['color']);
    $external_url = trim($_POST['external_url']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Basit doğrulama
    if (empty($name)) $errors[] = "Ürün adı zorunludur.";
    if (empty($price) || !is_numeric($price)) $errors[] = "Geçerli bir fiyat girin.";
    if (empty($category_id)) $errors[] = "Bir kategori seçmelisiniz.";
    if (empty($external_url)) $errors[] = "Satış URL'si zorunludur.";
    if (!filter_var($external_url, FILTER_VALIDATE_URL)) $errors[] = "Geçerli bir URL girin.";

    if (empty($errors)) {
        // TODO: Resim güncelleme işlemi buraya gelecek.
        
        $sql = "UPDATE products SET 
                    name = ?, 
                    description = ?, 
                    price = ?, 
                    category_id = ?, 
                    color = ?, 
                    external_url = ?, 
                    is_active = ?
                WHERE id = ? AND vendor_id = ?"; // Güvenlik için vendor_id tekrar kontrol ediliyor.
        
        $stmt = $pdo->prepare($sql);
        
        try {
            $stmt->execute([
                $name, 
                $description, 
                $price, 
                $category_id, 
                $color, 
                $external_url, 
                $is_active,
                $product_id,
                $current_vendor_id
            ]);
            header("Location: products.php?status=updated");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Veritabanı hatası: " . $e->getMessage();
        }
    }
    // Hata varsa, form tekrar gösterilirken güncel (ama kaydedilmemiş) veriler kullanılsın.
    $product['name'] = $name;
    $product['description'] = $description;
    $product['price'] = $price;
    $product['category_id'] = $category_id;
    $product['color'] = $color;
    $product['external_url'] = $external_url;
    $product['is_active'] = $is_active;
}

// Kategorileri formda listelemek için çek
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<h1>Ürünü Düzenle: <?php echo htmlspecialchars($product['name']); ?></h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>

<form class="vendor-form" action="edit-product.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Ürün Adı</label>
        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($product['name']); ?>">
    </div>
    <div class="form-group">
        <label for="description">Açıklama</label>
        <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
    </div>
    <div class="form-group">
        <label for="category_id">Kategori</label>
        <select id="category_id" name="category_id" required>
            <option value="">-- Kategori Seçin --</option>
            <?php foreach($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>" <?php if($product['category_id'] == $category['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="price">Fiyat (TL)</label>
        <input type="number" id="price" name="price" step="0.01" required value="<?php echo htmlspecialchars($product['price']); ?>">
    </div>
    <div class="form-group">
        <label for="color">Renkler (Virgülle ayırın)</label>
        <input type="text" id="color" name="color" value="<?php echo htmlspecialchars($product['color']); ?>">
    </div>
    <div class="form-group">
        <label for="external_url">Satın Alma URL'si</label>
        <input type="url" id="external_url" name="external_url" required value="<?php echo htmlspecialchars($product['external_url']); ?>">
    </div>
    <div class="form-group form-check">
        <input type="checkbox" id="is_active" name="is_active" value="1" <?php if($product['is_active']) echo 'checked'; ?>>
        <label for="is_active">Ürün yayında olsun</label>
    </div>
    
    <button type="submit" class="btn">Değişiklikleri Kaydet</button>
    <a href="products.php" style="margin-left:15px;">İptal</a>
</form>

<?php include 'footer.php'; ?>
