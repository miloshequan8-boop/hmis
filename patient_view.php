<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM patients WHERE id = ?');
$stmt->execute([$id]);
$patient = $stmt->fetch();
if (!$patient) {
    flash('error', 'Patient not found.');
    redirect('patients.php');
}

$stmt = $pdo->prepare('SELECT * FROM appointments WHERE patient_id = ? ORDER BY appointment_date DESC LIMIT 10');
$stmt->execute([$id]);
$appointments = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT * FROM visits WHERE patient_id = ? ORDER BY visit_date DESC LIMIT 10');
$stmt->execute([$id]);
$visits = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div>
        <h1><?= h($patient['first_name'] . ' ' . $patient['last_name']) ?></h1>
        <p class="muted">Patient No: <?= h($patient['patient_no']) ?> | Age: <?= h(age_from_dob($patient['date_of_birth'])) ?> | Gender: <?= h($patient['gender']) ?></p>
        <p><strong>Phone:</strong> <?= h($patient['phone']) ?> <strong>Email:</strong> <?= h($patient['email']) ?></p>
        <p><strong>Blood group:</strong> <?= h($patient['blood_group']) ?> <strong>Address:</strong> <?= h($patient['address']) ?></p>
        <p><strong>Notes:</strong> <?= h($patient['allergy_notes']) ?></p>
    </div>
    <div class="card">
        <div class="actions">
            <a class="btn secondary" href="patient_form.php?id=<?= (int)$patient['id'] ?>">Edit Patient</a>
            <a class="btn" href="appointment_form.php?patient_id=<?= (int)$patient['id'] ?>">Book Appointment</a>
            <a class="btn light" href="visit_form.php?patient_id=<?= (int)$patient['id'] ?>">Record Visit</a>
        </div>
    </div>
</section>

<section class="grid-2">
    <div class="card">
        <h2>Recent Appointments</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Date</th><th>Department</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($appointments as $row): ?>
                    <tr>
                        <td><?= h(date('d M Y H:i', strtotime($row['appointment_date']))) ?></td>
                        <td><?= h($row['department']) ?></td>
                        <td><span class="badge info"><?= h($row['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$appointments): ?><tr><td colspan="3">No appointments yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <h2>Recent Visits</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Date</th><th>Doctor</th><th>Diagnosis</th></tr></thead>
                <tbody>
                <?php foreach ($visits as $row): ?>
                    <tr>
                        <td><?= h(date('d M Y H:i', strtotime($row['visit_date']))) ?></td>
                        <td><?= h($row['doctor_name']) ?></td>
                        <td><?= h($row['diagnosis']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$visits): ?><tr><td colspan="3">No visits yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
