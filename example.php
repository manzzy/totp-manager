<?php

require __DIR__ . '/vendor/autoload.php';

// use ugw\TOTPManager\TOTPManager;
use ugw\TOTPManager\TOTPManager;

session_start();

$totpManager = new TOTPManager('Proto Dashboard');

// Assume user id or email
$userLabel = 'user@domain.com';

if (!isset($_SESSION['user_totp'])) {
    $_SESSION['user_totp'] = $totpManager->generateSecret($userLabel);
}

$secret = $_SESSION['user_totp']['secret'];
$qrImage = $_SESSION['user_totp']['qr_code'];

$success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userCode = $_POST['otp'] ?? '';
    $success = $totpManager->verifyCode($secret, $userCode);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>TOTP Auth Example</title>
</head>
<body>
    <h2>Scan this QR Code</h2>
    <img src="<?= $qrImage ?>" alt="QR Code" />
    <p>Or manually enter this secret: <strong><?= htmlspecialchars($secret) ?></strong></p>

    <h2>Verify Code</h2>
    <form method="POST">
        <input type="text" name="otp" placeholder="123456" required />
        <button type="submit">Verify</button>
    </form>

    <?php if (!is_null($success)): ?>
        <p style="color: <?= $success ? 'green' : 'red' ?>;">
            <?= $success ? '✅ Valid code!' : '❌ Invalid code!' ?>
        </p>
    <?php endif; ?>
</body>
</html>
