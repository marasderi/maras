<?php
include 'header.php';

// --- İŞLEMLER ---

// 1. ROL GÜNCELLEME İŞLEMİ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_role'])) {
    $user_id_to_update = intval($_POST['user_id']);
    $new_role = $_POST['role'];

    // Rollerin güvenli olduğundan emin olalım (whitelist)
    $allowed_roles = ['member', 'vendor', 'admin'];
    if (in_array($new_role, $allowed_roles)) {
        
        // Kullanıcının mevcut rolünü alalım
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$user_id_to_update]);
        $current_role = $stmt->fetchColumn();

        // Eğer bir üye, satıcıya yükseltiliyorsa:
        if ($new_role === 'vendor' && $current_role === 'member') {
            // Önce vendors tablosunda kaydı var mı diye kontrol et (olmamalı ama garanti olsun)
            $stmt = $pdo->prepare("SELECT count(*) FROM vendors WHERE user_id = ?");
            $stmt->execute([$user_id_to_update]);
            if ($stmt->fetchColumn() == 0) {
                // Yoksa, varsayılan bilgilerle bir satıcı/mağaza profili oluştur.
                $user_info_stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
                $user_info_stmt->execute([$user_id_to_update]);
                $username = $user_info_stmt->fetchColumn();
                
                $vendor_stmt = $pdo->prepare("INSERT INTO vendors (user_id, store_name, store_description) VALUES (?, ?, ?)");
                $vendor_stmt->execute([$user_id_to_update, $username . ' Mağazası', 'Bu mağaza için henüz bir açıklama girilmedi.']);
            }
        }
        
        // Son olarak kullanıcının rolünü users tablosunda güncelle
        $update_stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $update_stmt->execute([$new_role, $user_id_to_update]);
        
        header("Location: users.php?status=updated");
        exit();
    }
}


// 2. KULLANICI SİLME İŞLEMİ
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id_to_delete = intval($_GET['id']);

    // Adminin kendini silmesini engelle
    if ($user_id_to_delete == $_SESSION['user_id']) {
        header("Location: users.php?status=error_self_delete");
        exit();
    }
    
    // Veritabanındaki CASCADE kuralı sayesinde, bu kullanıcı silindiğinde
    // vendors tablosundaki ilgili satıcı kaydı da otomatik silinecektir.
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id_to_delete]);

    header("Location: users.php?status=deleted");
    exit();
}


// --- SAYFA İÇERİĞİ ---

// Tüm kullanıcıları listele
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<div class="vendor-page-header">
    <h1>Üye Yönetimi</h1>
</div>

<?php // Durum mesajlarını göster
if(isset($_GET['status'])){
    $status = $_GET['status'];
    if($status == 'updated') echo '<div class="alert alert-success"><p>Kullanıcı rolü başarıyla güncellendi.</p></div>';
    if($status == 'deleted') echo '<div class="alert alert-success"><p>Kullanıcı başarıyla silindi.</p></div>';
    if($status == 'error_self_delete') echo '<div class="alert alert-danger"><p>HATA: Kendi yönetici hesabınızı silemezsiniz.</p></div>';
}
?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Kullanıcı Adı</th>
                <th>E-posta</th>
                <th>Rol</th>
                <th>Kayıt Tarihi</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <form class="role-form" action="users.php" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <select name="role">
                                <option value="member" <?php if ($user['role'] == 'member') echo 'selected'; ?>>Üye (Member)</option>
                                <option value="vendor" <?php if ($user['role'] == 'vendor') echo 'selected'; ?>>Satıcı (Vendor)</option>
                                <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Yönetici (Admin)</option>
                            </select>
                            <button type="submit" name="change_role">Kaydet</button>
                        </form>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                    <td>
                        <a href="users.php?action=delete&id=<?php echo $user['id']; ?>" class="action-btn delete" onclick="return confirm('Bu kullanıcıyı ve (varsa) tüm mağaza bilgilerini kalıcı olarak silmek istediğinizden emin misiniz?');">Sil</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
