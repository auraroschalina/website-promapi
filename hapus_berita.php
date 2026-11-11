
<?php
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manajemen_berita.php");
    exit;
}

// Validasi CSRF
if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
) {
    die("Permintaan tidak valid.");
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("ID berita tidak valid.");
}

try {
    $stmt = $pdo->prepare("SELECT gambar FROM berita WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $berita = $stmt->fetch();

    if (!$berita) {
        die("Berita tidak ditemukan.");
    }

    // Hapus data dari database
    $stmt = $pdo->prepare("DELETE FROM berita WHERE id = :id");
    $stmt->execute([':id' => $id]);

    // Hapus foto jika berada di folder upload_berita
    $folder = realpath(__DIR__ . '/upload_berita');

    if ($folder && !empty($berita['gambar'])) {
        $foto = realpath(__DIR__ . '/' . $berita['gambar']);

        if (
            $foto &&
            strpos($foto, $folder . DIRECTORY_SEPARATOR) === 0 &&
            is_file($foto)
        ) {
            unlink($foto);
        }
    }

    header("Location: manajemen_berita.php?pesan=berhasil_hapus");
    exit;

} catch (PDOException $e) {
    die("Berita gagal dihapus. Silakan coba kembali.");
}
