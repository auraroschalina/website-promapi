
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Proteksi halaman admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Pastikan hanya menerima metode POST
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

// Ambil dan validasi data
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$email = trim($_POST['email'] ?? '');
$no_hp = trim($_POST['no_hp'] ?? '');
$intansi = trim($_POST['intansi'] ?? '');
$pekerjaan = trim($_POST['pekerjaan'] ?? '');
$status = $_POST['status'] ?? '';

$status_valid = ['menunggu', 'aktif', 'ditolak'];

// Periksa kelengkapan data
if (
    !$id ||
    $nama_lengkap === '' ||
    $email === '' ||
    $no_hp === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    !in_array($status, $status_valid, true)
) {
    header("Location: edit_anggota.php?id=" . (int)$id . "&pesan=data_tidak_valid");
    exit;
}

try {
    // Pastikan anggota tersedia
    $cek = $pdo->prepare("SELECT id FROM members WHERE id = :id");
    $cek->execute(['id' => $id]);

    if (!$cek->fetch()) {
        header("Location: manajemen_anggota.php?pesan=anggota_tidak_ditemukan");
        exit;
    }

    // Perbarui data anggota di database
    $sql = "
        UPDATE members
        SET
            nama_lengkap = :nama_lengkap,
            email = :email,
            no_hp = :no_hp,
            intansi = :intansi,
            pekerjaan = :pekerjaan,
            status = :status
        WHERE id = :id
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'nama_lengkap' => $nama_lengkap,
        'email' => $email,
        'no_hp' => $no_hp,
        'intansi' => $intansi,
        'pekerjaan' => $pekerjaan,
        'status' => $status,
        'id' => $id
    ]);

    header("Location: manajemen_anggota.php?pesan=berhasil_edit");
    exit;

} catch (PDOException $e) {

    // Tangani email yang sudah digunakan anggota lain
    if ($e->getCode() === '23000') {
        header("Location: edit_anggota.php?id=" . $id . "&pesan=email_digunakan");
        exit;
    }

    error_log($e->getMessage());

    header("Location: edit_anggota.php?id=" . $id . "&pesan=gagal_edit");
    exit;
}
?>
