<?php
// Sabitleri Tanımla
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // XAMPP varsayılan kullanıcı adı
define('DB_PASS', '');     // XAMPP varsayılan şifre (boş)
define('DB_NAME', 'deri_pazar');

// Site URL'i (sonradan lazım olacak)
define('SITE_URL', 'http://localhost/deri-pazari');

// PDO ile veritabanı bağlantısı
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Sonuçları array olarak almak için
} catch (PDOException $e) {
    die("HATA: Veritabanı bağlantısı kurulamadı. " . $e->getMessage());
}

// Session'ları başlat (login ve sepet için her sayfada lazım olacak)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
