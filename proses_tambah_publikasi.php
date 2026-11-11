
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Pastikan formulir dikirim menggunakan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: tambah_publikasi.php");
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

// Ambil data formulir
$kategori = trim($_POST['kategori'] ?? '');
$nama_jurnal = trim($_POST['nama_jurnal'] ?? '');
$link_jurnal = trim($_POST['link_jurnal'] ?? '');

// Validasi input
if (!in_array($kategori, ['nasional', 'internasional'], true)) {
    die("Kategori jurnal tidak valid.");
}

if ($nama_jurnal === '' || strlen($nama_jurnal) > 255) {
    die("Nama jurnal wajib diisi dan maksimal 255 karakter.");
}

if (
    $link_jurnal === '' ||
    strlen($link_jurnal) > 500 ||
    !filter_var($link_jurnal, FILTER_VALIDATE_URL) ||
    !in_array(parse_url($link_jurnal, PHP_URL_SCHEME), ['http', 'https'], true)
) {
    die("Link jurnal tidak valid. Gunakan URL http:// atau https://.");
}

try {
    $sql = "INSERT INTO publikasi
            (kategori, nama_jurnal, link_jurnal)
            VALUES
            (:kategori, :nama_jurnal, :link_jurnal)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':kategori' => $kategori,
        ':nama_jurnal' => $nama_jurnal,
        ':link_jurnal' => $link_jurnal
    ]);

    header("Location: manajemen_publikasi.php?pesan=berhasil");
    exit;

} catch (PDOException $e) {
    error_log("Gagal menambahkan publikasi: " . $e->getMessage());
    die("Data jurnal gagal disimpan. Silakan coba kembali.");
}
