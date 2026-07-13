<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$patients = $pdo->query("SELECT id, patient_no, CONCAT(first_name, ' ', last_name) AS name FROM patients ORDER BY first_name")->fetchAll();
$selectedPatient = (int)($_GET['patient_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $patientId = (int)($_POST['patient_id'] ?? 0);
    $visitDate = str_replace('T', ' ', $_POST['visit_date'] ?? '');
    $doctorName = trim($_POST['doctor_name'] ?? '');
    $diagnosis = trim($_POST['diagnosis'] ?? '');
    $notes = trim($_POST['treatment_notes'] ?? '');

    if (!$patientId || !$visitDate || $doctorName === '' || $diagnosis === '') {
        flash('error', 'Please complete all required fields.');
        redirect('visit_form.php');
    }

    $stmt = $pdo->prepare('INSERT INTO visits (patient_id, visit_date, doctor_name, diagnosis, treatment_notes) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$patientId, $visitDate, $doctorName, $diagnosis, $notes]);
    flash('success', 'Visit recorded successfully.');
    redirect('visits.php');
}
include __DIR__ . '/includes/header.php';
?>
<section class="card">
    <h1>Record Clinical Visit</h1>
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
                <label for="visit_date">Visit Date and Time</label>
                <input id="visit_date" name="visit_date" type="datetime-local" required>
            </div>
            <div>
                <label for="doctor_name">Doctor / Clinician Name</label>
                <input id="doctor_name" name="doctor_name" required placeholder="e.g. Dr. Karanja">
            </div>
        </div>
        <label for="diagnosis">Diagnosis / Assessment</label>
        <input id="diagnosis" name="diagnosis" required placeholder="e.g. Malaria suspected, routine review">
        <label for="treatment_notes">Treatment Notes</label>
        <textarea id="treatment_notes" name="treatment_notes" placeholder="Write brief treatment plan or referral notes"></textarea>
        <div class="actions">
            <button class="btn" type="submit">Save Visit</button>
            <a class="btn light" href="visits.php">Cancel</a>
        </div>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
