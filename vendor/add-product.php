<?php
include 'header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Formdan gelen verileri al
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $color = trim($_POST['color']);
    $external_url = trim($_POST['external_url']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // ... (Diğer doğrulamalar aynı kalıyor)
    if (empty($name)) $errors[] = "Ürün adı zorunludur.";
    if (empty($price) || !is_numeric($price)) $errors[] = "Geçerli bir fiyat girin.";
    if (empty($category_id)) $errors[] = "Bir kategori seçmelisiniz.";
    if (empty($external_url)) $errors[] = "Satış URL'si zorunludur.";
    
    // --- RESİM YÜKLEME MANTIĞI ---
    $image_name = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = __DIR__ . "/../uploads/products/";
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        
        // Benzersiz bir dosya adı oluştur
        $image_name = uniqid('prod_') . '.' . $file_extension;
        $target_file = $target_dir . $image_name;

        // Dosya tipi kontrolü
        if (!in_array($file_extension, $allowed_types)) {
            $errors[] = "Geçersiz dosya tipi. Sadece JPG, JPEG, PNG ve GIF dosyalarına izin verilir.";
        }
        // Dosya boyutu kontrolü (örn: 5MB)
        if ($_FILES['product_image']['size'] > 5 * 1024 * 1024) {
            $errors[] = "Dosya boyutu çok büyük. Maksimum 5MB olabilir.";
        }
    } else {
        $errors[] = "Ürün resmi zorunludur.";
    }


    if (empty($errors)) {
        // Resmi sunucuya taşı
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
            $sql = "INSERT INTO products (vendor_id, category_id, name, description, price, color, image, external_url, is_active) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([ $current_vendor_id, $category_id, $name, $description, $price, $color, $image_name, $external_url, $is_active ]);
                header("Location: products.php?status=added");
                exit();
            } catch (PDOException $e) {
                $errors[] = "Veritabanı hatası: " . $e->getMessage();
            }
        } else {
            $errors[] = "Resim yüklenirken bir hata oluştu.";
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<h1>Yeni Ürün Ekle</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>

<form class="vendor-form" action="add-product.php" method="POST" enctype="multipart/form-data">
    
    <div class="form-group">
        <label for="name">Ürün Adı</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="product_image">Ürün Resmi</label>
        <input type="file" id="product_image" name="product_image" required>
    </div>
    
    <div class="form-group">
        <label for="description">Açıklama</label>
        <textarea id="description" name="description" rows="5"></textarea>
    </div>
    <div class="form-group">
        <label for="category_id">Kategori</label>
        <select id="category_id" name="category_id" required>
            <option value="">-- Kategori Seçin --</option>
            <?php foreach($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="price">Fiyat (TL)</label>
        <input type="number" id="price" name="price" step="0.01" required>
    </div>
    <div class="form-group">
        <label for="color">Renkler (Virgülle ayırın)</label>
        <input type="text" id="color" name="color">
    </div>
    <div class="form-group">
        <label for="external_url">Satın Alma URL'si</label>
        <input type="url" id="external_url" name="external_url" required>
    </div>
    <div class="form-group form-check">
        <input type="checkbox" id="is_active" name="is_active" value="1" checked>
        <label for="is_active">Ürün yayında olsun</label>
    </div>
    
    <button type="submit" class="btn">Ürünü Ekle</button>
</form>

<?php include 'footer.php'; ?>
