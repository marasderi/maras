<?php require_once __DIR__ . '/../config/database.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deri Pazarı - Kaliteli El İşi Ürünler</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>

<header class="main-header">
    <div class="container header-content">
        <div class="logo">
            <a href="<?php echo SITE_URL; ?>">Deri Pazarı</a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="#">Ürünler</a></li>
                <li><a href="#">Mağazalar</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li>Merhaba, <?php echo htmlspecialchars($_SESSION['username']); ?>!</li>
                    <li><a href="<?php echo SITE_URL; ?>/logout.php">Çıkış Yap</a></li>
                <?php else: ?>
                    <li><a href="<?php echo SITE_URL; ?>/login.php">Giriş Yap</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/register.php" class="btn-register">Kayıt Ol</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main class="container">
