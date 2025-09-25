<?php
// Hataları başlangıçta gizle, çünkü config dosyası henüz yok.
error_reporting(0);
ini_set('display_errors', 0);

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$config_file_path = 'config/database.php';
$sql_file_path = 'deri_pazar_kurulum.sql';
$errors = [];

// Kurulum zaten yapılmış mı diye kontrol et
if (file_exists($config_file_path) && $step != 3) {
    die('Kurulum zaten yapılmış. Devam etmek için <strong>config/database.php</strong> dosyasını sunucudan silmeniz gerekir.');
}

if ($step == 2 && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $db_host = trim($_POST['db_host']);
    $db_name = trim($_POST['db_name']);
    $db_user = trim($_POST['db_user']);
    $db_pass = trim($_POST['db_pass']);
    $site_url = rtrim(trim($_POST['site_url']), '/');

    // 1. Veritabanı bağlantısını test et
    try {
        $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        $errors[] = "Veritabanı bağlantısı kurulamadı! Lütfen bilgileri kontrol edin. Hata: " . $e->getMessage();
    }

    // 2. SQL dosyasını oku ve çalıştır
    if (empty($errors)) {
        if (!file_exists($sql_file_path) || !is_readable($sql_file_path)) {
            $errors[] = "Kurulum dosyası '{$sql_file_path}' bulunamadı veya okunamıyor!";
        } else {
            try {
                $sql = file_get_contents($sql_file_path);
                $pdo->exec($sql);
            } catch (PDOException $e) {
                $errors[] = "Veritabanı tabloları oluşturulurken bir hata oluştu: " . $e->getMessage();
            }
        }
    }

    // 3. Config dosyasını oluştur
    if (empty($errors)) {
        if (!is_dir('config')) {
            mkdir('config', 0755, true);
        }
        $config_template = <<<EOT
<?php
// Bu dosya kurulum sihirbazı tarafından otomatik olarak oluşturulmuştur.
define('DB_HOST', '{$db_host}');
define('DB_NAME', '{$db_name}');
define('DB_USER', '{$db_user}');
define('DB_PASS', '{$db_pass}');
define('SITE_URL', '{$site_url}');

try {
    \$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    \$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException \$e) {
    die("HATA: Veritabanı bağlantısı kurulamadı. Lütfen config/database.php dosyasını kontrol edin.");
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
EOT;
        if (!file_put_contents($config_file_path, $config_template)) {
             $errors[] = "Config dosyası '{$config_file_path}' oluşturulamadı! Lütfen 'config' klasörünün yazma izinlerini (CHMOD 755 veya 777) kontrol edin.";
        }
    }

    if (empty($errors)) {
        header('Location: install.php?step=3');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Deri Pazarı - Kurulum Sihirbazı</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f0f2f5; color: #333; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; }
        .installer { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        h1 { text-align: center; color: #1a2b4d; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; margin-bottom: 5px; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn { display: block; width: 100%; padding: 12px; background-color: #007bff; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; text-align: center; text-decoration: none; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        p { line-height: 1.6; }
    </style>
</head>
<body>
    <div class="installer">
        <h1>Deri Pazarı Kurulumu</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <strong>Kurulum sırasında hata oluştu:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <a href="install.php" class="btn">Tekrar Dene</a>
        <?php elseif ($step == 1): ?>
            <p>Başlamadan önce, hosting panelinizden boş bir veritabanı, bir kullanıcı oluşturup bu kullanıcıyı veritabanına atadığınızdan emin olun.</p>
            <form action="install.php?step=2" method="POST">
                <div class="form-group">
                    <label for="db_host">Veritabanı Sunucusu</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                </div>
                <div class="form-group">
                    <label for="db_name">Veritabanı Adı</label>
                    <input type="text" id="db_name" name="db_name" required>
                </div>
                <div class="form-group">
                    <label for="db_user">Kullanıcı Adı</label>
                    <input type="text" id="db_user" name="db_user" required>
                </div>
                <div class="form-group">
                    <label for="db_pass">Şifre</label>
                    <input type="password" id="db_pass" name="db_pass">
                </div>
                 <div class="form-group">
                    <label for="site_url">Site Tam Adresi (URL)</label>
                    <input type="url" id="site_url" name="site_url" value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); ?>" required>
                </div>
                <button type="submit" class="btn">Kurulumu Başlat</button>
            </form>
        <?php elseif ($step == 3): ?>
            <div class="alert alert-success">
                <strong>Kurulum Başarıyla Tamamlandı!</strong>
            </div>
             <div class="alert alert-warning">
                <strong>ÇOK ÖNEMLİ:</strong> Lütfen sunucunuzdan <strong>install.php</strong> ve <strong>deri_pazar_kurulum.sql</strong> dosyalarını şimdi silin!
            </div>
            <a href="index.php" class="btn">Siteyi Görüntüle</a>
        <?php endif; ?>
    </div>
</body>
</html>

