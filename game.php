<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kullanıcıyı çek
$stmt = $pdo->prepare("SELECT id, username, balance FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bet_amount = (int)($_POST['bet_amount'] ?? 0);
    $choice     = $_POST['choice'] ?? '';

    if ($bet_amount <= 0) {
        $message = "Bahis miktarı 0'dan büyük olmalı.";
    } elseif ($bet_amount > $user['balance']) {
        $message = "Yetersiz bakiye.";
    } elseif (!in_array($choice, ['kirmizi', 'siyah'])) {
        $message = "Geçersiz seçim.";
    } else {
        // 0 = kirmizi, 1 = siyah
        $random = mt_rand(0, 1);
        $result_color = $random === 0 ? 'kirmizi' : 'siyah';

        if ($choice === $result_color) {
            // Kazanırsa: +bet_amount
            $user['balance'] += $bet_amount;
            $result_text = "Kazandın! Sonuç: " . strtoupper($result_color);
            $result = 'win';
        } else {
            // Kaybederse: -bet_amount
            $user['balance'] -= $bet_amount;
            $result_text = "Kaybettin... Sonuç: " . strtoupper($result_color);
            $result = 'lose';
        }

        // Bakiyeyi güncelle
        $upd = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $upd->execute([$user['balance'], $user['id']]);

        // Bet kaydı
        $ins = $pdo->prepare("INSERT INTO bets (user_id, game_name, bet_amount, user_choice, result, balance_after)
                              VALUES (?, 'kirmizi_siyah', ?, ?, ?, ?)");
        $ins->execute([$user['id'], $bet_amount, $choice, $result, $user['balance']]);

        $message = $result_text . " | Yeni bakiyen: " . $user['balance'];
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kırmızı / Siyah Oyunu - Casinova</title>
</head>
<body>
    <h1>Kırmızı / Siyah Oyunu</h1>
    <p>Kullanıcı: <strong><?php echo htmlspecialchars($user['username']); ?></strong></p>
    <p>Bakiyen: <strong><?php echo (int)$user['balance']; ?></strong> kredi</p>

    <?php if ($message): ?>
        <p style="color:blue;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Bahis miktarı:<br>
            <input type="number" name="bet_amount" min="1">
        </label><br><br>

        <label>
            <input type="radio" name="choice" value="kirmizi"> Kırmızı
        </label>
        <label>
            <input type="radio" name="choice" value="siyah"> Siyah
        </label><br><br>

        <button type="submit">Oyna</button>
    </form>

    <p><a href="index.php">← Anasayfa</a></p>
    <p><a href="logout.php">Çıkış yap</a></p>
</body>
</html>
