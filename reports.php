<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$rows = [
    'Total registered patients' => $pdo->query('SELECT COUNT(*) FROM patients')->fetchColumn(),
    'Scheduled appointments' => $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Scheduled'")->fetchColumn(),
    'Completed appointments' => $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Completed'")->fetchColumn(),
    'Clinical visits' => $pdo->query('SELECT COUNT(*) FROM visits')->fetchColumn(),
    'Bills issued' => $pdo->query('SELECT COUNT(*) FROM bills')->fetchColumn(),
    'Unpaid bill value' => 'KES ' . number_format((float)$pdo->query("SELECT COALESCE(SUM(bi.quantity * bi.unit_price),0) FROM bills b JOIN bill_items bi ON b.id = bi.bill_id WHERE b.status = 'Unpaid'")->fetchColumn()),
];
include __DIR__ . '/includes/header.php';
?>
<section class="card">
    <h1>HMIS Reports</h1>
    <p class="muted">A report page uses SQL aggregate functions such as COUNT and SUM.</p>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Indicator</th><th>Value</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $label => $value): ?>
                <tr><td><?= h($label) ?></td><td><strong><?= h((string)$value) ?></strong></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
