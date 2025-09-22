<?php require_once __DIR__ . '/../config/database.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deri Pazarı - Kaliteli El İşi Ürünler</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body data-site-url="<?php echo SITE_URL; ?>">

<header class="main-header">
    <div class="container header-content">
        <div class="logo">
            <a href="<?php echo SITE_URL; ?>">Deri Pazarı</a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="<?php echo SITE_URL; ?>/products.php">Ürünler</a></li>
<li><a href="<?php echo SITE_URL; ?>/stores.php">Mağazalar</a></li>
                
                <?php if (isset($_SESSION['user_id'])): // Kullanıcı giriş yapmışsa burası çalışır ?>
                    
                    <?php // --- YENİ EKLENEN KISIM BAŞLANGIÇ --- ?>
                   <?php if ($_SESSION['user_role'] === 'admin'): ?>
    <li><a href="<?php echo SITE_URL; ?>/admin/">Yönetim Paneli</a></li>
<?php elseif ($_SESSION['user_role'] === 'vendor'): ?>
    <li><a href="<?php echo SITE_URL; ?>/vendor/">Satıcı Paneli</a></li>
<?php endif; ?>
                    <?php // --- YENİ EKLENEN KISIM BİTİŞ --- ?>
<li><a href="<?php echo SITE_URL; ?>/favorites.php">Favorilerim</a></li>
<li>Merhaba, <?php echo htmlspecialchars($_SESSION['username']); ?>!</li>
                    <li>Merhaba, <?php echo htmlspecialchars($_SESSION['username']); ?>!</li>
                    <li><a href="<?php echo SITE_URL; ?>/logout.php">Çıkış Yap</a></li>

                <?php else: // Kullanıcı giriş yapmamışsa burası çalışır ?>
                    
                    <li><a href="<?php echo SITE_URL; ?>/login.php">Giriş Yap</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/register.php" class="btn-register">Kayıt Ol</a></li>
                
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
    <?php
// Eğer kullanıcı giriş yapmışsa, favori ürün ID'lerini bir diziye alalım
$user_favorites = [];
if (isset($_SESSION['user_id'])) {
    $fav_stmt = $pdo->prepare("SELECT product_id FROM favorites WHERE user_id = ?");
    $fav_stmt->execute([$_SESSION['user_id']]);
    $user_favorites = $fav_stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}
?>

<main class="container">
