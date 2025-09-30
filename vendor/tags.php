<?php
include 'header.php';

$success_message = '';
$errors = [];

// Form gönderildiyse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tags_list'])) {
    $tags_input = trim($_POST['tags_list']);

    if (empty($tags_input)) {
        $errors[] = "Lütfen eklemek için en az bir etiket girin.";
    } else {
        // Etiketleri virgül, noktalı virgül veya yeni satıra göre ayır
        $tags_array = preg_split('/[,\n\r;]+/', $tags_input);
        
        $inserted_count = 0;
        $skipped_count = 0;

        // Her bir etiketi veritabanına eklemeyi dene
        foreach ($tags_array as $tag_name) {
            $tag_name = trim($tag_name);

            if (!empty($tag_name)) {
                // Basit bir slug oluştur
                $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $tag_name));
                
                // IGNORE sayesinde, eğer etiket zaten varsa hata vermez ve eklemez.
                $stmt = $pdo->prepare("INSERT IGNORE INTO tags (name, slug) VALUES (?, ?)");
                $stmt->execute([$tag_name, $slug]);

                // Etkilenen satır sayısı 1 ise, yeni etiket eklenmiştir.
                if ($stmt->rowCount() > 0) {
                    $inserted_count++;
                } else {
                    $skipped_count++;
                }
            }
        }
        $success_message = "İşlem tamamlandı! {$inserted_count} yeni etiket eklendi, {$skipped_count} mevcut etiket atlandı.";
    }
}

// Mevcut tüm etiketleri listelemek için çek
$all_tags = $pdo->query("SELECT * FROM tags ORDER BY name ASC")->fetchAll();
?>

<div class="vendor-page-header">
    <h1>Etiket Yönetimi</h1>
</div>

<?php if ($success_message): ?>
    <div class="alert alert-success"><p><?php echo $success_message; ?></p></div>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger"><?php foreach($errors as $error) { echo "<p>$error</p>"; } ?></div>
<?php endif; ?>


<div class="admin-content-split">
    <!-- Sol Taraf: Yeni Etiket Ekleme Formu -->
    <div class="form-container-split">
        <h3>Toplu Etiket Ekle</h3>
        <p>Elindeki etiket listesini aşağıdaki kutuya yapıştır. Etiketleri virgül (,), noktalı virgül (;) veya her birini yeni bir satıra yazarak ayırabilirsin.</p>
        <form action="tags.php" method="POST" class="vendor-form">
            <div class="form-group">
                <label for="tags_list">Etiket Listesi</label>
                <textarea name="tags_list" id="tags_list" rows="15" placeholder="Örnek:
deri ceket,
erkek giyim,
el yapımı; hakiki deri"></textarea>
            </div>
            <button type="submit" class="btn">Etiketleri Ekle</button>
        </form>
    </div>

    <!-- Sağ Taraf: Mevcut Etiketler Listesi -->
    <div class="table-container">
         <h3>Mevcut Etiketler (<?php echo count($all_tags); ?> adet)</h3>
         <div class="tag-cloud-box">
            <?php if ($all_tags): ?>
                <?php foreach ($all_tags as $tag): ?>
                    <span class="tag-item"><?php echo htmlspecialchars($tag['name']); ?></span>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Sistemde henüz hiç etiket bulunmuyor.</p>
            <?php endif; ?>
         </div>
    </div>
</div>

<?php include 'footer.php'; ?>
