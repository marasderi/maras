<?php
include 'header.php'; // Bu header içinde satıcının ID'si ($current_vendor_id) zaten tanımlı.

// Mevcut mağaza bilgilerini formda göstermek için çekelim.
$stmt = $pdo->prepare("SELECT * FROM vendors WHERE id = ?");
$stmt->execute([$current_vendor_id]);
$store = $stmt->fetch();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $store_name = trim($_POST['store_name']);
    $store_description = trim($_POST['store_description']);
    $current_logo = $store['store_logo'];

    if (empty($store_name)) {
        $errors[] = "Mağaza adı boş bırakılamaz.";
    }

    // Yeni logo yüklendi mi diye kontrol et
    if (isset($_FILES['store_logo']) && $_FILES['store_logo']['error'] == 0) {
        $target_dir = __DIR__ . "/../uploads/logos/";
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $file_extension = strtolower(pathinfo($_FILES['store_logo']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_types)) {
            $errors[] = "Geçersiz logo tipi. Sadece JPG, JPEG, PNG izin verilir.";
        }
        // ... boyut kontrolü de eklenebilir ...

        if (empty($errors)) {
            $new_logo_name = 'logo_' . $current_vendor_id . '_' . time() . '.' . $file_extension;
            $target_file = $target_dir . $new_logo_name;
            
            if (move_uploaded_file($_FILES['store_logo']['tmp_name'], $target_file)) {
                // Yeni logo yüklendiyse, eski logoyu (eğer varsayılan değilse) sil
                if ($current_logo && $current_logo != 'default_logo.png' && file_exists($target_dir . $current_logo)) {
                    unlink($target_dir . $current_logo);
                }
                $current_logo = $new_logo_name; // Veritabanına kaydedilecek yeni logo adı
            } else {
                $errors[] = "Logo yüklenirken bir hata oluştu.";
            }
        }
    }

    if (empty($errors)) {
        $sql = "UPDATE vendors SET store_name = ?, store_description = ?, store_logo = ? WHERE id = ?";
        $update_stmt = $pdo->prepare($sql);
        $update_stmt->execute([$store_name, $store_description, $current_logo, $current_vendor_id]);
        
        $success = "Ayarlar başarıyla güncellendi!";
        // Güncel bilgileri sayfada anında görmek için tekrar çekelim.
        $stmt->execute([$current_vendor_id]);
        $store = $stmt->fetch();
    }
}

?>

<h1>Mağaza Ayarları</h1>
<p>Mağazanızın adını, açıklamasını ve logosunu buradan güncelleyebilirsiniz.</p>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><p><?php echo $success; ?></p></div>
<?php endif; ?>

<form class="vendor-form" action="settings.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="store_name">Mağaza Adı</label>
        <input type="text" id="store_name" name="store_name" required value="<?php echo htmlspecialchars($store['store_name']); ?>">
    </div>

    <div class="form-group">
        <label for="store_description">Mağaza Açıklaması</label>
        <textarea id="store_description" name="store_description" rows="6"><?php echo htmlspecialchars($store['store_description']); ?></textarea>
    </div>

    <div class="form-group">
        <label>Mevcut Logo</label>
        <div>
            <?php if (!empty($store['store_logo']) && file_exists(__DIR__ . '/../uploads/logos/' . $store['store_logo'])): ?>
                <img src="<?php echo SITE_URL . '/uploads/logos/' . htmlspecialchars($store['store_logo']); ?>" alt="Mevcut Logo" style="max-width: 150px; border-radius: 50%;">
            <?php else: ?>
                <p>Henüz bir logo yüklenmemiş.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="store_logo">Logoyu Değiştir</label>
        <input type="file" id="store_logo" name="store_logo">
    </div>

    <button type="submit" class="btn">Ayarları Kaydet</button>
</form>

<?php include 'footer.php'; ?>
