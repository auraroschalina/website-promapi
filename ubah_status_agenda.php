
<?php
session_start();

require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manajemen_agenda.php");
    exit;
}

if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
) {
    die("Permintaan tidak valid.");
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$status = $_POST['status'] ?? '';

if (!$id || !in_array($status, ['draft', 'terbit'], true)) {
    die("Data agenda tidak valid.");
}

try {
    $stmt = $pdo->prepare("
        UPDATE agenda
        SET status = :status
        WHERE id = :id
    ");

    $stmt->execute([
        ':status' => $status,
        ':id' => $id
    ]);

    header("Location: manajemen_agenda.php?pesan=status_diubah");
    exit;

} catch (PDOException $e) {
    die("Gagal mengubah status agenda: " . $e->getMessage());
}
?>
