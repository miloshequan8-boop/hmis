<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM patients WHERE id = ?');
    $stmt->execute([$id]);
    flash('success', 'Patient record deleted.');
    redirect('patients.php');
}

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE patient_no LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? ORDER BY created_at DESC");
    $stmt->execute([$like, $like, $like, $like]);
} else {
    $stmt = $pdo->query('SELECT * FROM patients ORDER BY created_at DESC');
}
$patients = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<section class="card">
    <div class="actions" style="justify-content: space-between; align-items: center;">
        <div>
            <h1>Patient Registration</h1>
            <p class="muted">Create, search, view, update and delete patient records.</p>
        </div>
        <a class="btn" href="patient_form.php">+ Add Patient</a>
    </div>
    <form method="get" class="actions">
        <input name="q" value="<?= h($q) ?>" placeholder="Search patient no, name or phone">
        <button class="btn secondary" type="submit">Search</button>
        <a class="btn light" href="patients.php">Reset</a>
    </form>
</section>

<section class="card" style="margin-top: 1rem;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Patient No</th><th>Name</th><th>Gender</th><th>Age</th><th>Phone</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?= h($patient['patient_no']) ?></td>
                    <td><?= h($patient['first_name'] . ' ' . $patient['last_name']) ?></td>
                    <td><?= h($patient['gender']) ?></td>
                    <td><?= h(age_from_dob($patient['date_of_birth'])) ?></td>
                    <td><?= h($patient['phone']) ?></td>
                    <td>
                        <div class="actions">
                            <a class="btn light" href="patient_view.php?id=<?= (int)$patient['id'] ?>">View</a>
                            <a class="btn secondary" href="patient_form.php?id=<?= (int)$patient['id'] ?>">Edit</a>
                            <form method="post" style="display:inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$patient['id'] ?>">
                                <button class="btn danger" data-confirm="Delete this patient record?" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$patients): ?>
                <tr><td colspan="6">No patients found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
