<?php
// Satıcı panelindeki edit-product.php dosyasının neredeyse aynısı,
// SADECE "vendor_id" kontrolü kaldırılmış hali.
include 'header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die("Geçersiz ürün ID'si.");
$product_id = intval($_GET['id']);

// DİKKAT: vendor_id kontrolü burada YOK! Admin her ürünü düzenleyebilir.
$stmt = $pdo->prepare("SELECT p.*, v.store_name FROM products p JOIN vendors v ON p.vendor_id = v.id WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) die("Ürün bulunamadı.");

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Formdan gelen veriler ve doğrulamalar satıcı paneliyle aynı...
    $name = trim($_POST['name']); //... vs.
    // ...

    $image_name = $product['image'];
    // Resim yükleme mantığı satıcı paneliyle aynı...
    // ...

    if (empty($errors)) {
        // DİKKAT: UPDATE sorgusunda da vendor_id kontrolü YOK!
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, color = ?, external_url = ?, is_active = ?, image = ?
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            // execute içine $current_vendor_id EKLENMEYECEK
            $stmt->execute([$name, /*...,*/ $image_name, $product_id]);
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
<h3 style="color:#555;">Satıcı: <?php echo htmlspecialchars($product['store_name']); ?></h3>

<form class="vendor-form" action="edit-product.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
    </form>

<?php include 'footer.php'; ?>
