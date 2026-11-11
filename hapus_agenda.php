
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

if (!$id) {
    die("ID agenda tidak valid.");
}

try {
    $stmt = $pdo->prepare("DELETE FROM agenda WHERE id = :id");
    $stmt->execute([':id' => $id]);

    header("Location: manajemen_agenda.php?pesan=agenda_dihapus");
    exit;

} catch (PDOException $e) {
    die("Gagal menghapus agenda: " . $e->getMessage());
}
?>
