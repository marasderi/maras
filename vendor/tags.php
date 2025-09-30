<?php
// Veritabanı ve session'ları başlat
require_once __DIR__ . '/../config/database.php';

// --- ERİŞİM KONTROLÜ ---
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'vendor') {
    header("Location: " . SITE_URL . "/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id FROM vendors WHERE user_id = ?");
$stmt->execute([$current_user_id]);
$vendor = $stmt->fetch();
$current_vendor_id = $vendor['id'] ?? null;

if (!$current_vendor_id) {
    die("HATA: Satıcı bilgileri bulunamadı.");
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
                <li><a href="<?php echo SITE_URL; ?>/vendor/tags.php">Etiket Yönetimi</a></li>
                <li><a href="<?php echo SITE_URL; ?>/vendor/settings.php">Ayarlar</a></li>
                <li><a href="<?php echo SITE_URL; ?>" target="_blank">Siteyi Görüntüle</a></li>
                <li><a href="<?php echo SITE_URL; ?>/logout.php">Çıkış Yap</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="container">
<div class="vendor-panel-container">
