<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT username, balance FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Casinova Demo</title>
</head>
<body>
    <h1>Hoş geldin, <?php echo htmlspecialchars($user['username']); ?> 👋</h1>
    <p>Bakiyen: <strong><?php echo (int)$user['balance']; ?></strong> kredi</p>

    <p><a href="game.php">🎰 Kırmızı / Siyah Oyunu</a></p>
    <p><a href="logout.php">Çıkış yap</a></p>
</body>
</html>
