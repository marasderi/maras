<?php
// Veritabanı ve session'ları başlat
require_once __DIR__ . '/../config/database.php';

// --- ADMİN ERİŞİM KONTROLÜ ---
// 1. Kullanıcı giriş yapmış mı?
if (!isset($_SESSION['user_id'])) {
    header("Location: " . SITE_URL . "/login.php");
    exit();
}
// 2. Kullanıcının rolü 'admin' mi?
if ($_SESSION['user_role'] !== 'admin') {
    // Eğer admin değilse, anasayfaya yönlendir ve bir hata mesajı göster.
    $_SESSION['error_message'] = "Bu alana erişim yetkiniz yok.";
    header("Location: " . SITE_URL);
    exit();
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Paneli - Deri Pazarı</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>

<header class="main-header admin-header">
    <div class="container header-content">
        <div class="logo">
            <a href="<?php echo SITE_URL; ?>/admin/">Yönetim Paneli</a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="<?php echo SITE_URL; ?>/admin/index.php">Anasayfa</a></li>
                <li><a href="<?php echo SITE_URL; ?>/admin/users.php">Üyeler</a></li>
                <li><a href="<?php echo SITE_URL; ?>/admin/categories.php">Kategoriler</a></li>
                <li><a href="<?php echo SITE_URL; ?>/admin/products.php">Ürünler</a></li>
                <li><a href="<?php echo SITE_URL; ?>" target="_blank">Siteyi Görüntüle</a></li>
                <li><a href="<?php echo SITE_URL; ?>/logout.php">Çıkış Yap</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="container">
<div class="vendor-panel-container">
