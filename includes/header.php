<?php
require_once __DIR__ . '/helpers.php';
$flash = get_flash();
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h(APP_NAME) ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<header class="topbar">
    <div class="brand">
        <span class="brand-mark">+</span>
        <span><?= h(APP_NAME) ?></span>
    </div>
    <?php if ($user): ?>
    <button class="nav-toggle" type="button" aria-label="Toggle navigation">☰</button>
    <nav class="main-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="patients.php">Patients</a>
        <a href="appointments.php">Appointments</a>
        <a href="visits.php">Visits</a>
        <a href="billing.php">Billing</a>
        <a href="reports.php">Reports</a>
        <a href="logout.php">Logout</a>
    </nav>
    <?php endif; ?>
</header>

<main class="page">
    <?php if ($flash): ?>
        <div class="alert <?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
    <?php endif; ?>
