<?php
// Veritabanı ve session'ları başlat
require_once __DIR__ . '/../config/database.php';

// --- ERİŞİM KONTROLÜ ---
// 1. Kullanıcı giriş yapmış mı?
if (!isset($_SESSION['user_id'])) {
    header("Location: " . SITE_URL . "/login.php");
    exit();
}
// 2. Kullanıcının rolü 'vendor' mu?
if ($_SESSION['user_role'] !== 'vendor') {
    // Eğer satıcı değilse, anasayfaya yönlendir ve bir hata mesajı göster.
    $_SESSION['error_message'] = "Bu alana erişim yetkiniz yok.";
    header("Location: " . SITE_URL);
    exit();
}

// Satıcının kendi ID'sini bir değişkene atayalım, sorgularda lazım olacak.
$current_user_id = $_SESSION['user_id'];

// Satıcının vendor bilgilerini (mağaza ID'si vb.) çekelim.
$stmt = $pdo->prepare("SELECT id FROM vendors WHERE user_id = ?");
$stmt->execute([$current_user_id]);
$vendor = $stmt->fetch();
$current_vendor_id = $vendor['id'] ?? null;

// Eğer bir sebepten vendor bilgisi bulunamazsa (veri tutarsızlığı), güvenli çıkış yap.
if (!$current_vendor_id) {
    die("HATA: Satıcı bilgileri bulunamadı. Lütfen yönetici ile iletişime geçin.");
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satıcı Paneli - Deri Pazarı</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>

<header class="main-header">
    <div class="container header-content">
        <div class="logo">
            <a href="<?php echo SITE_URL; ?>/vendor/">Satıcı Paneli</a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="<?php echo SITE_URL; ?>/vendor/products.php">Ürünlerim</a></li>
                <li><a href="#">Siparişler (Yakında)</a></li>
                <li><a href="#">Ayarlar (Yakında)</a></li>
                <li><a href="<?php echo SITE_URL; ?>" target="_blank">Siteyi Görüntüle</a></li>
                <li><a href="<?php echo SITE_URL; ?>/logout.php">Çıkış Yap</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="container">
<div class="vendor-panel-container">
