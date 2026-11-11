
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Proteksi admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Pastikan request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manajemen_publikasi.php");
    exit;
}

// Validasi CSRF
$csrf_token = $_POST['csrf_token'] ?? '';

if (
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $csrf_token)
) {
    die("Permintaan tidak valid. Silakan kembali dan coba lagi.");
}

// Ambil ID publikasi
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("ID publikasi tidak valid.");
}

try {
    // Hapus data jurnal berdasarkan ID
    $stmt = $pdo->prepare("DELETE FROM publikasi WHERE id = :id");
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() > 0) {
        header("Location: manajemen_publikasi.php?pesan=hapus_berhasil");
        exit;
    }

    header("Location: manajemen_publikasi.php?pesan=data_tidak_ditemukan");
    exit;

} catch (PDOException $e) {
    error_log("Gagal menghapus publikasi: " . $e->getMessage());
    die("Data jurnal gagal dihapus. Silakan coba kembali.");
}
