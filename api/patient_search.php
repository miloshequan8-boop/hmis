<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

header('Content-Type: application/json');
$q = trim($_GET['q'] ?? '');

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$like = '%' . $q . '%';
$stmt = $pdo->prepare("SELECT id, patient_no, CONCAT(first_name, ' ', last_name) AS full_name, gender, phone
                       FROM patients
                       WHERE patient_no LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR phone LIKE ?
                       ORDER BY first_name
                       LIMIT 10");
$stmt->execute([$like, $like, $like, $like]);
echo json_encode($stmt->fetchAll());
