<?php
require_once 'config/database.php';

header('Content-Type: application/json'); // Cevabın JSON formatında olacağını belirtelim.

// Kullanıcı giriş yapmamışsa işlem yapma.
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Lütfen giriş yapın.']);
    exit();
}

// Gelen ürün ID'sini al
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['product_id']) || !is_numeric($data['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz ürün ID.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = intval($data['product_id']);

// Ürün favorilerde mi diye kontrol et
$stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
$is_favorite = $stmt->fetch();

try {
    if ($is_favorite) {
        // Zaten favorilerdeyse, kaldır.
        $delete_stmt = $pdo->prepare("DELETE FROM favorites WHERE id = ?");
        $delete_stmt->execute([$is_favorite['id']]);
        echo json_encode(['success' => true, 'action' => 'removed']);
    } else {
        // Favorilerde değilse, ekle.
        $insert_stmt = $pdo->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
        $insert_stmt->execute([$user_id, $product_id]);
        echo json_encode(['success' => true, 'action' => 'added']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'İşlem sırasında bir hata oluştu.']);
}
