<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$patients = $pdo->query("SELECT id, patient_no, CONCAT(first_name, ' ', last_name) AS name FROM patients ORDER BY first_name")->fetchAll();
$selectedPatient = (int)($_GET['patient_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $patientId = (int)($_POST['patient_id'] ?? 0);
    $appointmentDate = str_replace('T', ' ', $_POST['appointment_date'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $reason = trim($_POST['reason'] ?? '');

    if (!$patientId || !$appointmentDate || $department === '' || $reason === '') {
        flash('error', 'Please complete all required fields.');
        redirect('appointment_form.php');
    }

    $stmt = $pdo->prepare('INSERT INTO appointments (patient_id, appointment_date, department, reason) VALUES (?, ?, ?, ?)');
    $stmt->execute([$patientId, $appointmentDate, $department, $reason]);
    flash('success', 'Appointment booked successfully.');
    redirect('appointments.php');
}
include __DIR__ . '/includes/header.php';
?>
<section class="card">
    <h1>Book Appointment</h1>
    <form method="post">
        <?= csrf_field() ?>
        <label for="patient_id">Patient</label>
        <select id="patient_id" name="patient_id" required>
            <option value="">Select patient</option>
            <?php foreach ($patients as $p): ?>
                <option value="<?= (int)$p['id'] ?>" <?= $selectedPatient === (int)$p['id'] ? 'selected' : '' ?>><?= h($p['patient_no'] . ' - ' . $p['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="form-row">
            <div>
                <label for="appointment_date">Date and Time</label>
                <input id="appointment_date" name="appointment_date" type="datetime-local" required>
            </div>
            <div>
                <label for="department">Department</label>
                <select id="department" name="department" required>
                    <option>Outpatient</option>
                    <option>Emergency</option>
                    <option>Laboratory</option>
                    <option>Pharmacy</option>
                    <option>Maternity</option>
                    <option>Radiology</option>
                </select>
            </div>
        </div>
        <label for="reason">Reason</label>
        <input id="reason" name="reason" required placeholder="e.g. Fever, review, lab test">
        <div class="actions">
            <button class="btn" type="submit">Save Appointment</button>
            <a class="btn light" href="appointments.php">Cancel</a>
        </div>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
