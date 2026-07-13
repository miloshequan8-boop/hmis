<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$stmt = $pdo->query("SELECT v.*, CONCAT(p.first_name, ' ', p.last_name) AS patient_name, p.patient_no
                    FROM visits v
                    JOIN patients p ON p.id = v.patient_id
                    ORDER BY v.visit_date DESC");
$visits = $stmt->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="card">
    <div class="actions" style="justify-content: space-between; align-items:center;">
        <div>
            <h1>Clinical Visits</h1>
            <p class="muted">Record diagnosis and treatment notes for a patient visit.</p>
        </div>
        <a class="btn" href="visit_form.php">+ Record Visit</a>
    </div>
</section>
<section class="card" style="margin-top:1rem;">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Patient</th><th>Doctor</th><th>Diagnosis</th><th>Treatment Notes</th></tr></thead>
            <tbody>
            <?php foreach ($visits as $row): ?>
                <tr>
                    <td><?= h(date('d M Y H:i', strtotime($row['visit_date']))) ?></td>
                    <td><?= h($row['patient_no'] . ' - ' . $row['patient_name']) ?></td>
                    <td><?= h($row['doctor_name']) ?></td>
                    <td><?= h($row['diagnosis']) ?></td>
                    <td><?= h($row['treatment_notes']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$visits): ?><tr><td colspan="5">No visits recorded.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
