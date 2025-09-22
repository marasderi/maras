<?php
// Bu sayfanın en başına PHP kodlarını yazıyoruz.
include 'includes/header.php';

// Eğer kullanıcı zaten giriş yapmışsa, anasayfaya yönlendir.
if (isset($_SESSION['user_id'])) {
    header("Location: " . SITE_URL);
    exit();
}

$errors = [];
$success_message = '';

// Form gönderilmiş mi diye kontrol et
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form verilerini al ve temizle
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Doğrulama (Validation)
    if (empty($username)) { $errors[] = "Kullanıcı adı boş bırakılamaz."; }
    if (empty($email)) { $errors[] = "E-posta boş bırakılamaz."; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Geçersiz e-posta formatı."; }
    if (empty($password)) { $errors[] = "Şifre boş bırakılamaz."; }
    if (strlen($password) < 6) { $errors[] = "Şifre en az 6 karakter olmalıdır."; }
    if ($password !== $password_confirm) { $errors[] = "Şifreler uyuşmuyor."; }

    // E-posta veritabanında mevcut mu diye kontrol et
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Bu e-posta adresi zaten kayıtlı.";
        }
    }

    // Hata yoksa, kullanıcıyı veritabanına kaydet
    if (empty($errors)) {
        // Şifreyi güvenli bir şekilde hash'le
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashed_password])) {
            // Başarılı kayıt sonrası login sayfasına yönlendir.
            $_SESSION['success_message'] = "Kaydınız başarıyla oluşturuldu! Şimdi giriş yapabilirsiniz.";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Kayıt sırasında bir hata oluştu. Lütfen tekrar deneyin.";
        }
    }
}
?>

<div class="form-container">
    <h2>Üye Ol</h2>
    <p>Aramıza katılmak için aşağıdaki formu doldurun.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="form-group">
            <label for="username">Kullanıcı Adı:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">E-posta Adresi:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Şifre:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirm">Şifre Tekrar:</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        <button type="submit" class="btn">Kayıt Ol</button>
    </form>
    <p class="form-switch">Zaten bir hesabın var mı? <a href="login.php">Giriş Yap</a></p>
</div>

<?php include 'includes/footer.php'; ?>
