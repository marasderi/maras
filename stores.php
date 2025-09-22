<?php
include 'includes/header.php';

// Tüm satıcıları (mağazaları) çekiyoruz.
// Sadece en az bir aktif ürünü olan satıcıları listelemek daha mantıklı olabilir,
// ama şimdilik tüm satıcıları listeleyelim.
$stores = $pdo->query("SELECT * FROM vendors ORDER BY store_name ASC")->fetchAll();

?>

<div class="page-header">
    <h1>Tüm Mağazalar</h1>
    <p>Platformumuzdaki tüm el emeği ürünlerin ustalarını keşfedin.</p>
</div>

<div class="store-grid">
    <?php if ($stores): ?>
        <?php foreach ($stores as $store): ?>
            <a href="store-profile.php?id=<?php echo $store['id']; ?>" class="store-card-link">
                <div class="store-card">
                    <div class="store-logo">
                        <img src="https://via.placeholder.com/150x150.png?text=<?php echo urlencode(substr($store['store_name'], 0, 1)); ?>" alt="<?php echo htmlspecialchars($store['store_name']); ?> Logo">
                    </div>
                    <div class="store-info">
                        <h3><?php echo htmlspecialchars($store['store_name']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($store['store_description'], 0, 100)) . '...'; ?></p>
                        <span>Mağazayı Ziyaret Et →</span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Henüz sisteme kayıtlı bir mağaza bulunmuyor.</p>
    <?php endif; ?>
</div>


<?php include 'footer.php'; ?>
