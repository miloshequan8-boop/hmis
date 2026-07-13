<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$stmt = $pdo->query('SELECT COUNT(*) FROM users');
$userCount = (int)$stmt->fetchColumn();

if ($userCount === 0) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
    $stmt->execute(['System Administrator', 'admin@hmis.test', $hash, 'admin']);
    $message = 'Admin account created successfully.';
} else {
    $message = 'Admin account already exists. No changes made.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMIS Setup</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<main class="page login-shell">
    <section class="card login-card">
        <h1>HMIS Setup</h1>
        <p><?= h($message) ?></p>
        <p><strong>Login email:</strong> admin@hmis.test</p>
        <p><strong>Password:</strong> admin123</p>
        <a class="btn" href="index.php">Go to Login</a>
    </section>
</main>
</body>
</html>
