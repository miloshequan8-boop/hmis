<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        login_user($user);
        flash('success', 'Welcome back, ' . $user['name'] . '.');
        redirect('dashboard.php');
    }

    flash('error', 'Invalid email or password. Try admin@hmis.test / admin123 after running setup.php.');
    redirect('index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h(APP_NAME) ?> Login</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<main class="page login-shell">
    <section class="card login-card">
        <h1>Matibabu Hospital Login</h1>
        <p class="muted">Matibabu Hospital Login</p>
        <?php $flash = get_flash(); if ($flash): ?>
            <div class="alert <?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
        <?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required value="admin@hmis.test">

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required value="admin123">

            <div class="actions">
                <button class="btn" type="submit">Login</button>
                <a class="btn light" href="setup.php">Run setup</a>
            </div>
        </form>
    </section>
</main>
</body>
</html>
