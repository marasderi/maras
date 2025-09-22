<?php 
include 'header.php'; 

// İstatistikler için verileri çekelim
$total_users = $pdo->query("SELECT count(*) FROM users")->fetchColumn();
$total_vendors = $pdo->query("SELECT count(*) FROM users WHERE role = 'vendor'")->fetchColumn();
$total_products = $pdo->query("SELECT count(*) FROM products")->fetchColumn();
$active_products = $pdo->query("SELECT count(*) FROM products WHERE is_active = 1")->fetchColumn();

?>

<div class="dashboard-welcome">
    <h1>Yönetim Paneline Hoş Geldiniz!</h1>
    <p>Sitenizin genel durumunu aşağıda görebilirsiniz.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Toplam Üye</h3>
        <p><?php echo $total_users; ?></p>
    </div>
    <div class="stat-card">
        <h3>Toplam Satıcı</h3>
        <p><?php echo $total_vendors; ?></p>
    </div>
    <div class="stat-card">
        <h3>Toplam Ürün</h3>
        <p><?php echo $total_products; ?></p>
    </div>
    <div class="stat-card">
        <h3>Aktif Ürün</h3>
        <p><?php echo $active_products; ?></p>
    </div>
</div>


<?php include 'footer.php'; ?>
