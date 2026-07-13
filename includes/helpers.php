<?php
function h(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $message;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        echo '<h1>Invalid form token</h1><p>Please go back and try again.</p>';
        exit;
    }
}

function age_from_dob(?string $dob): string
{
    if (!$dob) return 'N/A';
    try {
        $birth = new DateTime($dob);
        return (string)$birth->diff(new DateTime())->y;
    } catch (Exception $e) {
        return 'N/A';
    }
}

function generate_patient_no(PDO $pdo): string
{
    do {
        $patientNo = 'PAT-' . date('Y') . '-' . random_int(1000, 9999);
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE patient_no = ?');
        $stmt->execute([$patientNo]);
    } while ((int)$stmt->fetchColumn() > 0);

    return $patientNo;
}
