<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$patients = $pdo->query("SELECT id, patient_no, CONCAT(first_name, ' ', last_name) AS name FROM patients ORDER BY first_name")->fetchAll();
$services = $pdo->query('SELECT * FROM services ORDER BY service_name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_bill') {
    verify_csrf();
    $patientId = (int)($_POST['patient_id'] ?? 0);
    $serviceIds = $_POST['service_ids'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $notes = trim($_POST['notes'] ?? '');

    if (!$patientId || empty($serviceIds)) {
        flash('error', 'Select a patient and at least one service.');
        redirect('billing.php');
    }

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO bills (patient_id, notes) VALUES (?, ?)');
        $stmt->execute([$patientId, $notes]);
        $billId = (int)$pdo->lastInsertId();

        $serviceStmt = $pdo->prepare('SELECT * FROM services WHERE id = ?');
        $itemStmt = $pdo->prepare('INSERT INTO bill_items (bill_id, service_id, quantity, unit_price) VALUES (?, ?, ?, ?)');

        foreach ($serviceIds as $serviceId) {
            $sid = (int)$serviceId;
            $qty = max(1, (int)($quantities[$sid] ?? 1));
            $serviceStmt->execute([$sid]);
            $service = $serviceStmt->fetch();
            if ($service) {
                $itemStmt->execute([$billId, $sid, $qty, $service['cost']]);
            }
        }
        $pdo->commit();
        flash('success', 'Bill created successfully.');
    } catch (Exception $e) {
        $pdo->rollBack();
        flash('error', 'Could not create bill: ' . $e->getMessage());
    }
    redirect('billing.php');
}

$stmt = $pdo->query("SELECT b.*, CONCAT(p.first_name, ' ', p.last_name) AS patient_name, p.patient_no,
                    COALESCE(SUM(bi.quantity * bi.unit_price), 0) AS total
                    FROM bills b
                    JOIN patients p ON p.id = b.patient_id
                    LEFT JOIN bill_items bi ON bi.bill_id = b.id
                    GROUP BY b.id
                    ORDER BY b.created_at DESC");
$bills = $stmt->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="grid-2">
    <div class="card">
        <h1>Create Bill</h1>
        <p class="muted">JavaScript calculates the total before PHP saves the bill in MySQL.</p>
        <form id="billForm" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="create_bill">
            <label for="patient_id">Patient</label>
            <select id="patient_id" name="patient_id" required>
                <option value="">Select patient</option>
                <?php foreach ($patients as $p): ?>
                    <option value="<?= (int)$p['id'] ?>"><?= h($p['patient_no'] . ' - ' . $p['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <h3>Services</h3>
            <?php foreach ($services as $service): ?>
                <div class="card" style="box-shadow:none; padding:.8rem; margin-bottom:.5rem;">
                    <label style="margin:0; display:flex; gap:.6rem; align-items:center;">
                        <input class="service-check" style="width:auto;" type="checkbox" name="service_ids[]" value="<?= (int)$service['id'] ?>" data-price="<?= h($service['cost']) ?>">
                        <?= h($service['service_name']) ?> - KES <?= number_format((float)$service['cost']) ?>
                    </label>
                    <label>Quantity</label>
                    <input class="service-qty" type="number" min="1" value="1" name="quantities[<?= (int)$service['id'] ?>]">
                </div>
            <?php endforeach; ?>

            <div class="total-box">Estimated Total: <span id="billTotal">KES 0</span></div>
            <label for="notes">Notes</label>
            <input id="notes" name="notes" placeholder="e.g. Invoice for outpatient visit">
            <div class="actions">
                <button class="btn" type="submit">Save Bill</button>
            </div>
        </form>
    </div>
    <div class="card">
        <h2>Recent Bills</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Date</th><th>Patient</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($bills as $bill): ?>
                    <tr>
                        <td><?= h(date('d M Y', strtotime($bill['bill_date']))) ?></td>
                        <td><?= h($bill['patient_no'] . ' - ' . $bill['patient_name']) ?></td>
                        <td>KES <?= number_format((float)$bill['total']) ?></td>
                        <td><span class="badge warning"><?= h($bill['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$bills): ?><tr><td colspan="4">No bills yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
