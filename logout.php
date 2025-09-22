<?php
// Session'ı başlat
session_start();

// Tüm session verilerini temizle
$_SESSION = array();

// Session'ı yok et
session_destroy();

// Anasayfaya yönlendir
// SITE_URL'i kullanabilmek için config'i dahil edelim.
require_once 'config/database.php';
header("Location: " . SITE_URL);
exit();
?>
