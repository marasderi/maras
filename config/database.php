<?php
// Veritabanı Bilgileri
define('DB_HOST', 'localhost'); // Genellikle 'localhost'tur. Değilse hosting firmanızdan öğrenin.
define('DB_NAME', 'marasderi'); // DİKKAT: cPanel'de ön ek oluştuysa tam adını yazın (örn: kullanici_marasderi)
define('DB_USER', 'marasderi'); // DİKKAT: cPanel'de ön ek oluştuysa tam adını yazın (örn: kullanici_marasderi)
define('DB_PASS', 'marasderi');

// Site URL'i (Bu kısmı kendi alan adınızla değiştirmelisiniz)
define('SITE_URL', 'http://www.marasderi.com');

// PDO ile veritabanı bağlantısı
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("HATA: Veritabanı bağlantısı kurulamadı. " . $e->getMessage());
}

// Session'ları başlat
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
