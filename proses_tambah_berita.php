
<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Hanya menerima metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: tambah_berita.php");
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
$kategori = trim($_POST['kategori'] ?? '');
$ringkasan = trim($_POST['ringkasan'] ?? '');
$isi_berita = trim($_POST['isi_berita'] ?? '');
$tanggal = trim($_POST['tanggal_publikasi'] ?? '');
$status = trim($_POST['status'] ?? 'draft');

// Validasi data wajib
$kategori_valid = [
    'Organisasi',
    'Kegiatan',
    'Pendidikan',
    'Informasi',
    'Pengumuman'
];

if (
    $judul === '' ||
    !in_array($kategori, $kategori_valid, true) ||
    $ringkasan === '' ||
    $isi_berita === '' ||
    !in_array($status, ['draft', 'terbit'], true)
) {
    die("Data berita belum lengkap atau tidak valid.");
}

// Validasi tanggal
$date = DateTime::createFromFormat('!Y-m-d', $tanggal);

if (!$date || $date->format('Y-m-d') !== $tanggal) {
    die("Tanggal publikasi tidak valid.");
}

// Pastikan foto diunggah
if (
    !isset($_FILES['gambar']) ||
    $_FILES['gambar']['error'] !== UPLOAD_ERR_OK
) {
    die("Foto berita wajib diunggah. Periksa kembali file yang dipilih.");
}

// Validasi ukuran foto maksimal 2 MB
if ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
    die("Ukuran foto terlalu besar. Maksimal 2 MB.");
}

// Validasi tipe file foto
$tmp_file = $_FILES['gambar']['tmp_name'];

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($tmp_file);

$allowed_types = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp'
];

if (!isset($allowed_types[$mime]) || !getimagesize($tmp_file)) {
    die("Format foto tidak valid. Gunakan JPG, PNG, atau WEBP.");
}

// Buat folder penyimpanan jika belum tersedia
$folder = __DIR__ . '/uploads_berita/';

if (!is_dir($folder)) {
    mkdir($folder, 0755, true);
}

// Buat nama file unik
$extension = $allowed_types[$mime];
$nama_file = bin2hex(random_bytes(16)) . '.' . $extension;

$tujuan_file = $folder . $nama_file;
$path_database = 'uploads_berita/' . $nama_file;

// Pindahkan foto
if (!move_uploaded_file($tmp_file, $tujuan_file)) {
    die("Foto gagal diunggah. Periksa folder uploads.");
}

try {
    // Simpan berita ke database
    $sql = "INSERT INTO berita
            (judul, kategori, ringkasan, isi_berita, gambar,
             tanggal_publikasi, status)
            VALUES
            (:judul, :kategori, :ringkasan, :isi_berita, :gambar,
             :tanggal_publikasi, :status)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':judul' => $judul,
        ':kategori' => $kategori,
        ':ringkasan' => $ringkasan,
        ':isi_berita' => $isi_berita,
        ':gambar' => $path_database,
        ':tanggal_publikasi' => $tanggal,
        ':status' => $status
    ]);

    header("Location: manajemen_berita.php?pesan=berhasil_tambah");
    exit;

} catch (PDOException $e) {
    // Hapus foto jika penyimpanan database gagal
    if (file_exists($tujuan_file)) {
        unlink($tujuan_file);
    }

    die("Berita gagal disimpan ke database. Silakan periksa kembali.");
}
