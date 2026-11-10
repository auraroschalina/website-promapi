
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Proteksi halaman admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Pastikan hanya menerima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manajemen_anggota.php");
    exit;
}

// Validasi CSRF
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    header("Location: manajemen_anggota.php?pesan=csrf_error");
    exit;
}

// Validasi ID anggota
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: manajemen_anggota.php?pesan=id_tidak_valid");
    exit;
}

try {
    // Periksa apakah anggota tersedia
    $cek = $pdo->prepare("
        SELECT id
        FROM members
        WHERE id = :id
    ");

    $cek->execute(['id' => $id]);

    if (!$cek->fetch()) {
        header("Location: manajemen_anggota.php?pesan=anggota_tidak_ditemukan");
        exit;
    }

    // Hapus data anggota
    $hapus = $pdo->prepare("
        DELETE FROM members
        WHERE id = :id
    ");

    $hapus->execute(['id' => $id]);

    header("Location: manajemen_anggota.php?pesan=berhasil_hapus");
    exit;

} catch (PDOException $e) {

    error_log($e->getMessage());

    header("Location: manajemen_anggota.php?pesan=gagal_hapus");
    exit;
}
?>
