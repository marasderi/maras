<?php include 'header.php'; ?>

<div class="dashboard-welcome">
    <h1>Hoş Geldiniz, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Sol menüden ürünlerinizi yönetebilir veya ayarlarınızı güncelleyebilirsiniz.</p>
    <div class="dashboard-actions">
        <a href="products.php" class="btn">Ürünlerimi Yönet</a>
        <a href="add-product.php" class="btn btn-secondary">Yeni Ürün Ekle</a>
    </div>
</div>

<?php include 'footer.php'; ?>
