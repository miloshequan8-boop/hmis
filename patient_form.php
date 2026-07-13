<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$patient = [
    'patient_no' => '', 'first_name' => '', 'last_name' => '', 'gender' => 'Male',
    'date_of_birth' => '', 'phone' => '', 'email' => '', 'address' => '',
    'blood_group' => '', 'allergy_notes' => ''
];

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM patients WHERE id = ?');
    $stmt->execute([$id]);
    $patient = $stmt->fetch();
    if (!$patient) {
        flash('error', 'Patient not found.');
        redirect('patients.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $data = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'gender' => $_POST['gender'] ?? 'Male',
        'date_of_birth' => $_POST['date_of_birth'] ?: null,
        'phone' => trim($_POST['phone'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'blood_group' => trim($_POST['blood_group'] ?? ''),
        'allergy_notes' => trim($_POST['allergy_notes'] ?? ''),
    ];

    if ($data['first_name'] === '' || $data['last_name'] === '') {
        flash('error', 'First name and last name are required.');
        redirect($id ? "patient_form.php?id=$id" : 'patient_form.php');
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE patients SET first_name=?, last_name=?, gender=?, date_of_birth=?, phone=?, email=?, address=?, blood_group=?, allergy_notes=? WHERE id=?');
        $stmt->execute([$data['first_name'], $data['last_name'], $data['gender'], $data['date_of_birth'], $data['phone'], $data['email'], $data['address'], $data['blood_group'], $data['allergy_notes'], $id]);
        flash('success', 'Patient updated successfully.');
        redirect("patient_view.php?id=$id");
    }

    $patientNo = generate_patient_no($pdo);
    $stmt = $pdo->prepare('INSERT INTO patients (patient_no, first_name, last_name, gender, date_of_birth, phone, email, address, blood_group, allergy_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$patientNo, $data['first_name'], $data['last_name'], $data['gender'], $data['date_of_birth'], $data['phone'], $data['email'], $data['address'], $data['blood_group'], $data['allergy_notes']]);
    $newId = (int)$pdo->lastInsertId();
    flash('success', 'Patient registered successfully.');
    redirect("patient_view.php?id=$newId");
}

include __DIR__ . '/includes/header.php';
?>
<section class="card">
    <h1><?= $id ? 'Edit Patient' : 'Add Patient' ?></h1>
    <form method="post">
        <?= csrf_field() ?>
        <div class="form-row">
            <div>
                <label for="first_name">First Name</label>
                <input id="first_name" name="first_name" value="<?= h($patient['first_name']) ?>" required>
            </div>
            <div>
                <label for="last_name">Last Name</label>
                <input id="last_name" name="last_name" value="<?= h($patient['last_name']) ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div>
                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                    <?php foreach (['Male','Female','Other'] as $gender): ?>
                        <option value="<?= h($gender) ?>" <?= $patient['gender'] === $gender ? 'selected' : '' ?>><?= h($gender) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="date_of_birth">Date of Birth</label>
                <input id="date_of_birth" name="date_of_birth" type="date" value="<?= h($patient['date_of_birth']) ?>">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label for="phone">Phone</label>
                <input id="phone" name="phone" value="<?= h($patient['phone']) ?>">
            </div>
            <div>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="<?= h($patient['email']) ?>">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label for="blood_group">Blood Group</label>
                <input id="blood_group" name="blood_group" placeholder="e.g. O+" value="<?= h($patient['blood_group']) ?>">
            </div>
            <div>
                <label for="address">Address</label>
                <input id="address" name="address" value="<?= h($patient['address']) ?>">
            </div>
        </div>
        <label for="allergy_notes">Allergy / Important Notes</label>
        <textarea id="allergy_notes" name="allergy_notes"><?= h($patient['allergy_notes']) ?></textarea>
        <div class="actions">
            <button class="btn" type="submit">Save Patient</button>
            <a class="btn light" href="patients.php">Cancel</a>
        </div>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
