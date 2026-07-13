<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'status') {
    verify_csrf();
    $id = (int)$_POST['id'];
    $status = $_POST['status'];
    if (in_array($status, ['Scheduled','Completed','Cancelled'], true)) {
        $stmt = $pdo->prepare('UPDATE appointments SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
        flash('success', 'Appointment status updated.');
    }
    redirect('appointments.php');
}

$stmt = $pdo->query("SELECT a.*, CONCAT(p.first_name, ' ', p.last_name) AS patient_name, p.patient_no
                    FROM appointments a
                    JOIN patients p ON p.id = a.patient_id
                    ORDER BY a.appointment_date DESC");
$appointments = $stmt->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="card">
    <div class="actions" style="justify-content: space-between; align-items:center;">
        <div>
            <h1>Appointments</h1>
            <p class="muted">Book appointments and update status.</p>
        </div>
        <a class="btn" href="appointment_form.php">+ New Appointment</a>
    </div>
</section>
<section class="card" style="margin-top:1rem;">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Patient</th><th>Department</th><th>Reason</th><th>Status</th><th>Update</th></tr></thead>
            <tbody>
            <?php foreach ($appointments as $row): ?>
                <tr>
                    <td><?= h(date('d M Y H:i', strtotime($row['appointment_date']))) ?></td>
                    <td><?= h($row['patient_no'] . ' - ' . $row['patient_name']) ?></td>
                    <td><?= h($row['department']) ?></td>
                    <td><?= h($row['reason']) ?></td>
                    <td><span class="badge info"><?= h($row['status']) ?></span></td>
                    <td>
                        <form method="post" class="actions">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="status">
                            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                            <select name="status">
                                <?php foreach (['Scheduled','Completed','Cancelled'] as $status): ?>
                                    <option <?= $row['status'] === $status ? 'selected' : '' ?>><?= h($status) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn light" type="submit">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$appointments): ?><tr><td colspan="6">No appointments.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
