<?php
require 'config.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Kullanıcı adı ve şifre zorunludur.";
    } else {
        // Kullanıcı var mı?
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Bu kullanıcı adı zaten alınmış.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, balance) VALUES (?, ?, 1000)");
            $stmt->execute([$username, $hash]);
            header("Location: login.php");
            exit;
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kayıt Ol - Casinova</title>
</head>
<body>
    <h1>Kayıt Ol</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Kullanıcı adı:<br>
            <input type="text" name="username">
        </label><br><br>

        <label>Şifre:<br>
            <input type="password" name="password">
        </label><br><br>

        <button type="submit">Kayıt Ol</button>
    </form>

    <p>Zaten hesabın var mı? <a href="login.php">Giriş yap</a></p>
</body>
</html>
