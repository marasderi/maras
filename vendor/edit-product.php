<?php
// ... (sayfanın başındaki ürün getirme ve yetki kontrolü kodları aynı kalıyor) ...
include 'header.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die("Geçersiz ürün ID'si.");
$product_id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND vendor_id = ?");
$stmt->execute([$product_id, $current_vendor_id]);
$product = $stmt->fetch();
if (!$product) die("Ürün bulunamadı veya bu ürünü düzenleme yetkiniz yok.");

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ... (formdan veri alma aynı)
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $color = trim($_POST['color']);
    $external_url = trim($_POST['external_url']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // ... (doğrulamalar aynı)
    if (empty($name)) $errors[] = "Ürün adı zorunludur.";
    // ...

    $image_name = $product['image']; // Varsayılan olarak mevcut resmi kullan

    // Eğer YENİ bir resim yüklendiyse...
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = __DIR__ . "/../uploads/products/";
        // ... (add-product.php'deki gibi dosya tipi, boyutu kontrolleri) ...
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_types)) $errors[] = "Geçersiz dosya tipi.";

        if (empty($errors)) {
            $new_image_name = uniqid('prod_') . '.' . $file_extension;
            $target_file = $target_dir . $new_image_name;
            
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                // Yeni resim başarıyla yüklendiyse, eski resmi sunucudan sil
                if (!empty($product['image']) && file_exists($target_dir . $product['image'])) {
                    unlink($target_dir . $product['image']);
                }
                $image_name = $new_image_name; // Veritabanına yeni adı kaydet
            } else {
                $errors[] = "Yeni resim yüklenirken bir hata oluştu.";
            }
        }
    }

    if (empty($errors)) {
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, color = ?, external_url = ?, is_active = ?, image = ?
                WHERE id = ? AND vendor_id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$name, $description, $price, $category_id, $color, $external_url, $is_active, $image_name, $product_id, $current_vendor_id]);
            header("Location: products.php?status=updated");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Veritabanı hatası: " . $e->getMessage();
        }
    }
}
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<h1>Ürünü Düzenle: <?php echo htmlspecialchars($product['name']); ?></h1>

<?php if (!empty($errors)): ?> <?php endif; ?>

<form class="vendor-form" action="edit-product.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label>Mevcut Resim</label>
        <img src="<?php echo SITE_URL . '/uploads/products/' . htmlspecialchars($product['image']); ?>" alt="" width="150">
    </div>
    <div class="form-group">
        <label for="product_image">Resmi Değiştir (İsteğe bağlı)</label>
        <input type="file" id="product_image" name="product_image">
    </div>
    <button type="submit" class="btn">Değişiklikleri Kaydet</button>
</form>

<?php include 'footer.php'; ?>
