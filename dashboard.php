<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$patientCount = (int)$pdo->query('SELECT COUNT(*) FROM patients')->fetchColumn();
$appointmentCount = (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Scheduled'")->fetchColumn();
$visitCount = (int)$pdo->query('SELECT COUNT(*) FROM visits')->fetchColumn();
$unpaidTotal = (float)$pdo->query("SELECT COALESCE(SUM(bi.quantity * bi.unit_price),0) FROM bills b JOIN bill_items bi ON b.id = bi.bill_id WHERE b.status = 'Unpaid'")->fetchColumn();

$stmt = $pdo->query("SELECT a.*, CONCAT(p.first_name, ' ', p.last_name) AS patient_name, p.patient_no
                    FROM appointments a
                    JOIN patients p ON p.id = a.patient_id
                    WHERE a.status = 'Scheduled'
                    ORDER BY a.appointment_date ASC
                    LIMIT 5");
$appointments = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div>
        <h1>Hospital Management Information System Practical</h1>
        <p>This dashboard shows how PHP collects data from MySQL, renders HTML, CSS styles the interface, and JavaScript adds interactivity.</p>
    </div>
    <div class="card">
        <strong>Logged in as:</strong><br>
        <?= h(current_user()['name']) ?><br>
        <span class="muted"><?= h(current_user()['role']) ?></span>
    </div>
</section>

<section class="grid">
    <div class="stat-card"><span>Patients</span><strong><?= $patientCount ?></strong></div>
    <div class="stat-card"><span>Scheduled appointments</span><strong><?= $appointmentCount ?></strong></div>
    <div class="stat-card"><span>Visits recorded</span><strong><?= $visitCount ?></strong></div>
    <div class="stat-card"><span>Unpaid bills</span><strong>KES <?= number_format($unpaidTotal) ?></strong></div>
</section>

<section class="grid-2" style="margin-top: 1rem;">
    <div class="card">
        <h2>Live Patient Search</h2>
        <p class="muted">JavaScript sends your keyword to a PHP API and displays JSON results without reloading the page.</p>
        <input id="livePatientSearch" type="search" placeholder="Search by name, patient number or phone...">
        <div id="livePatientResults" class="search-results"><p class="muted">Type at least 2 characters.</p></div>
    </div>
    <div class="card">
        <h2>Upcoming Appointments</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Date</th><th>Patient</th><th>Department</th></tr></thead>
                <tbody>
                <?php foreach ($appointments as $row): ?>
                    <tr>
                        <td><?= h(date('d M Y H:i', strtotime($row['appointment_date']))) ?></td>
                        <td><?= h($row['patient_no'] . ' - ' . $row['patient_name']) ?></td>
                        <td><?= h($row['department']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$appointments): ?>
                    <tr><td colspan="3">No upcoming appointments.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
