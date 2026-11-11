
<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once __DIR__ . '/config/database.php';

// Proteksi halaman admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Ambil data publikasi
$stmt = $pdo->query("
    SELECT * FROM publikasi
    ORDER BY kategori ASC, nama_jurnal ASC
");

$publikasi = $stmt->fetchAll();

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manajemen Publikasi | PROMAPI</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root {
    --navy: #0A1F44;
    --navy-light: #173B68;
    --yellow: #F2C500;
    --bg: #f4f6f9;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: var(--bg);
    font-family: Arial, sans-serif;
    color: #263445;
}

.sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background: var(--navy);
    color: white;
    padding: 25px 18px;
    overflow-y: auto;
}

.sidebar img {
    width: 85px;
    height: 85px;
    object-fit: contain;
    background: white;
    border-radius: 50%;
    padding: 7px;
}

.sidebar a {
    display: block;
    color: #dce5f0;
    text-decoration: none;
    padding: 12px 14px;
    margin-bottom: 6px;
    border-radius: 8px;
    font-size: 14px;
}

.sidebar a:hover,
.sidebar a.active {
    background: var(--navy-light);
    color: var(--yellow);
}

.sidebar hr {
    border-color: #53677f;
}

.admin-main {
    margin-left: 250px;
    padding: 30px;
}

.page-title {
    font-size: 25px;
    font-weight: 700;
    color: var(--navy);
}

.content-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 3px 15px rgba(0,0,0,.05);
}

.btn-add {
    background: var(--navy);
    color: white;
    border: none;
    padding: 10px 17px;
    border-radius: 8px;
    text-decoration: none;
}

.btn-add:hover {
    background: var(--navy-light);
    color: white;
}

.table th {
    background: #f0f3f7;
    color: var(--navy);
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
}

.badge-nasional {
    background: #e5f0ff;
    color: #174a83;
}

.badge-internasional {
    background: #fff3cd;
    color: #765900;
}

@media (max-width: 768px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    .admin-main {
        margin-left: 0;
        padding: 18px;
    }
}
</style>
</head>

<body>

<aside class="sidebar">
    <div class="text-center mb-4">
        <img src="/promapi-website/assets/logo-promapi.png" alt="PROMAPI">
        <h5 class="mt-3 fw-bold">PROMAPI</h5>
    </div>

    <a href="dashboard_admin.php">
        <i class="bi bi-grid me-2"></i> Dashboard
    </a>

    <a href="manajemen_anggota.php">
        <i class="bi bi-people me-2"></i> Manajemen Anggota
    </a>

    <a href="konfirmasi_pendaftaran.php">
        <i class="bi bi-person-check me-2"></i> Konfirmasi Pendaftaran
    </a>

    <hr>

    <a href="manajemen_berita.php">
        <i class="bi bi-newspaper me-2"></i> Manajemen Berita
    </a>

    <a href="manajemen_agenda.php">
        <i class="bi bi-calendar-event me-2"></i> Manajemen Agenda
    </a>

    <a href="manajemen_publikasi.php" class="active">
        <i class="bi bi-journal-text me-2"></i> Manajemen Publikasi
    </a>

    <hr>

    <a href="index.html">
        <i class="bi bi-globe me-2"></i> Lihat Website
    </a>

    <a href="logout_admin.php">
        <i class="bi bi-box-arrow-right me-2"></i> Logout
    </a>
</aside>

<main class="admin-main">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1">Manajemen Publikasi</h1>
            <p class="text-secondary mb-0">
                Kelola daftar jurnal nasional dan internasional PROMAPI.
            </p>
        </div>

        <a href="tambah_publikasi.php" class="btn-add">
            <i class="bi bi-plus-lg me-1"></i> Tambah Jurnal
        </a>
    </div>

    <div class="content-card">

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kategori</th>
                        <th>Nama Jurnal</th>
                        <th>Link Jurnal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (count($publikasi) > 0): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($publikasi as $item): ?>
                    <tr>
                        <td><?= $no++; ?></td>

                        <td>
                            <?php if ($item['kategori'] === 'nasional'): ?>
                                <span class="badge badge-nasional">Nasional</span>
                            <?php else: ?>
                                <span class="badge badge-internasional">Internasional</span>
                            <?php endif; ?>
                        </td>

                        <td><?= e($item['nama_jurnal']); ?></td>

                        <td>
                            <a href="<?= e($item['link_jurnal']); ?>"
                               target="_blank"
                               rel="noopener noreferrer">
                                Lihat Jurnal
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </td>

                        <td>
    <div class="d-flex gap-2">

        <a href="edit_publikasi.php?id=<?= (int)$item['id']; ?>"
           class="btn btn-sm btn-warning">
            <i class="bi bi-pencil-square"></i> Edit
        </a>

        <form action="hapus_publikasi.php" method="POST"
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurnal ini?');">

            <input type="hidden" name="id"
                   value="<?= (int)$item['id']; ?>">

            <input type="hidden" name="csrf_token"
                   value="<?= e($_SESSION['csrf_token']); ?>">

            <button type="submit" class="btn btn-sm btn-danger">
                <i class="bi bi-trash"></i> Hapus
            </button>

        </form>

    </div>
</td>
                    </tr>
                    <?php endforeach; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-secondary">
                            Belum ada data publikasi.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</main>

</body>
</html>
