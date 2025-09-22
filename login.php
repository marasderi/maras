<?php
include 'includes/header.php';

// Eğer kullanıcı zaten giriş yapmışsa, anasayfaya yönlendir.
if (isset($_SESSION['user_id'])) {
    header("Location: " . SITE_URL);
    exit();
}

$errors = [];
$success_message = '';

// Register'dan gelen başarı mesajını al
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Mesajı gösterdikten sonra sil
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "E-posta ve şifre alanları boş bırakılamaz.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Kullanıcı bulunduysa ve şifre doğruysa
        if ($user && password_verify($password, $user['password'])) {
            // Session bilgilerini ayarla
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];

            // Anasayfaya yönlendir
            header("Location: " . SITE_URL);
            exit();
        } else {
            $errors[] = "Geçersiz e-posta veya şifre.";
        }
    }
}
?>

<div class="form-container">
    <h2>Giriş Yap</h2>
    <p>Hesabınıza erişmek için bilgilerinizi girin.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <p><?php echo $success_message; ?></p>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="email">E-posta Adresi:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Şifre:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Giriş Yap</button>
    </form>
    <p class="form-switch">Hesabın yok mu? <a href="register.php">Hemen Kayıt Ol</a></p>
</div>


<?php include 'includes/footer.php'; ?>
