
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Proteksi admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Pastikan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: tambah_agenda.php");
    exit;
}

// Validasi CSRF
if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
) {
    die("Permintaan tidak valid. Silakan kembali dan coba lagi.");
}

// Ambil data formulir
$judul = trim($_POST['judul'] ?? '');
$jenis = trim($_POST['jenis'] ?? '');
$tanggal = trim($_POST['tanggal'] ?? '');
$waktu = trim($_POST['waktu'] ?? '');
$lokasi = trim($_POST['lokasi'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$status = $_POST['status'] ?? 'draft';

// Validasi data wajib
if (
    $judul === '' ||
    $jenis === '' ||
    $tanggal === '' ||
    $waktu === '' ||
    $lokasi === ''
) {
    die("Semua kolom wajib harus diisi.");
}

// Validasi tanggal
$date = DateTime::createFromFormat('Y-m-d', $tanggal);

if (!$date || $date->format('Y-m-d') !== $tanggal) {
    die("Format tanggal tidak valid.");
}

// Validasi status
if (!in_array($status, ['draft', 'terbit'], true)) {
    die("Status agenda tidak valid.");
}

try {
    $sql = "INSERT INTO agenda
            (judul, jenis, tanggal, waktu, lokasi, deskripsi, status)
            VALUES
            (:judul, :jenis, :tanggal, :waktu, :lokasi, :deskripsi, :status)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':judul' => $judul,
        ':jenis' => $jenis,
        ':tanggal' => $tanggal,
        ':waktu' => $waktu,
        ':lokasi' => $lokasi,
        ':deskripsi' => $deskripsi,
        ':status' => $status
    ]);

    header("Location: manajemen_agenda.php?pesan=berhasil");
    exit;

} catch (PDOException $e) {
    die("Gagal menyimpan agenda: " . $e->getMessage());
}
?>
