
<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Proteksi halaman admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Pencarian dan filter
$keyword = trim($_GET['keyword'] ?? '');
$status = $_GET['status'] ?? '';

$sql = "SELECT * FROM berita WHERE 1=1";
$params = [];

if ($keyword !== '') {
    $sql .= " AND (judul LIKE :keyword OR kategori LIKE :keyword)";
    $params['keyword'] = "%$keyword%";
}

if (in_array($status, ['draft', 'terbit'], true)) {
    $sql .= " AND status = :status";
    $params['status'] = $status;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$daftar_berita = $stmt->fetchAll();

// Statistik
$total = (int) $pdo->query("SELECT COUNT(*) FROM berita")->fetchColumn();
$total_terbit = (int) $pdo->query("SELECT COUNT(*) FROM berita WHERE status='terbit'")->fetchColumn();
$total_draft = (int) $pdo->query("SELECT COUNT(*) FROM berita WHERE status='draft'")->fetchColumn();

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manajemen Berita - PROMAPI</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
:root {
    --navy: #0A1F44;
    --navy-2: #173B68;
    --yellow: #F2C500;
    --background: #f4f6fa;
    --text: #263746;
    --muted: #718096;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: var(--background);
    color: var(--text);
    font-family: Arial, sans-serif;
}

 /* SIDEBAR */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: var(--navy);
            position: fixed;
            top: 0;
            left: 0;
            padding: 25px 18px;
            color: white;
        }

        .sidebar img {
            width: 75px;
            height: 75px;
            object-fit: contain;
            background: white;
            border-radius: 12px;
            padding: 5px;
        }

        .sidebar a {
            display: block;
            color: #dce4f0;
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 8px;
            margin-top: 8px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: var(--navy-light);
            color: var(--yellow);
        }
.admin-main {
    margin-left: 250px;
    padding: 30px;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 28px;
}

.topbar h2 {
    color: var(--navy);
    font-size: 25px;
    font-weight: 700;
    margin: 0;
}

.admin-name {
    color: var(--muted);
    font-size: 13px;
}

.stat-card {
    background: white;
    padding: 22px;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,.04);
    height: 100%;
}

.stat-card .stat-label {
    color: var(--muted);
    font-size: 13px;
}

.stat-card h3 {
    color: var(--navy);
    font-size: 28px;
    font-weight: 700;
    margin: 8px 0 0;
}

.content-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 3px 15px rgba(0,0,0,.04);
}

.content-card h5 {
    color: var(--navy);
    font-weight: 700;
}

.table th {
    background: #f5f7fa;
    color: #526174;
    font-size: 12px;
    white-space: nowrap;
}

.table td {
    font-size: 13px;
    vertical-align: middle;
}

.news-thumb {
    width: 75px;
    height: 55px;
    object-fit: cover;
    border-radius: 7px;
}

.badge-terbit {
    background: #d9f4e5;
    color: #167345;
}

.badge-draft {
    background: #fff1cc;
    color: #8b6400;
}

.btn-add {
    background: var(--navy);
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 7px;
    text-decoration: none;
    font-size: 13px;
}

.btn-add:hover {
    background: var(--navy-2);
    color: white;
}

@media(max-width: 768px) {
    .admin-sidebar {
        position: relative;
        width: 100%;
        min-height: auto;
    }

    .admin-main {
        margin-left: 0;
        padding: 20px 12px;
    }

    .topbar {
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<aside class="sidebar">

    <div class="text-center mb-4">
        <img src="/promapi-website/assets/logo-promapi.png" alt="PROMAPI">
        <h5 class="mt-3 fw-bold">PROMAPI</h5>
    </div>

    <a href="dashboard_admin.php" >
        <i class="bi bi-grid me-2"></i> Dashboard
    </a>

    <a href="konfirmasi_pendaftaran.php" >
        <i class="bi bi-person-check me-2"></i> Konfirmasi Pendaftaran
    </a>
    
    <a href="manajemen_anggota.php">
        <i class="bi bi-people me-2"></i> Manajemen Anggota
    </a>

    
    <a href="manajemen_berita.php"class="active">
        <i class="bi bi-newspaper me-2"></i> Manajemen Berita
    </a>

    <a href="manajemen_agenda.php" >
        <i class="bi bi-calendar-event me-2"></i> Manajemen Agenda
    </a>

    <hr>

    <a href="index.html">
        <i class="bi bi-globe me-2"></i> Lihat Website
    </a>

    <a href="logout_admin.php">
        <i class="bi bi-box-arrow-right me-2"></i> Logout
    </a>


</aside>

<!-- KONTEN UTAMA -->
<main class="admin-main">

    <div class="topbar">
        <div>
            <h2>Manajemen Berita</h2>
            <div class="admin-name mt-1">
                Kelola informasi dan publikasi PROMAPI
            </div>
        </div>

        <div class="admin-name">
            <i class="bi bi-person-circle"></i>
            <?= e($_SESSION['admin_nama'] ?? 'Administrator') ?>
        </div>
    </div>

    <!-- STATISTIK -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">
                    <i class="bi bi-newspaper"></i> Total Berita
                </div>
                <h3><?= $total ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">
                    <i class="bi bi-check-circle"></i> Berita Terbit
                </div>
                <h3><?= $total_terbit ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">
                    <i class="bi bi-file-earmark"></i> Berita Draft
                </div>
                <h3><?= $total_draft ?></h3>
            </div>
        </div>

    </div>

    <!-- DAFTAR BERITA -->
    <div class="content-card">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

            <div>
                <h5 class="mb-1">Daftar Berita</h5>
                <small class="text-secondary">
                    Kelola seluruh berita PROMAPI di sini.
                </small>
            </div>

            <a href="tambah_berita.php" class="btn-add">
                <i class="bi bi-plus-lg"></i> Tambah Berita
            </a>

        </div>

        <!-- PENCARIAN -->
        <form method="GET" class="row g-2 mb-4">

            <div class="col-md-6">
                <input type="text"
                       name="keyword"
                       class="form-control"
                       placeholder="Cari judul atau kategori berita..."
                       value="<?= e($keyword) ?>">
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="terbit" <?= $status === 'terbit' ? 'selected' : '' ?>>
                        Terbit
                    </option>
                    <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>
                        Draft
                    </option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="manajemen_berita.php" class="btn btn-light">
                    Reset
                </a>
            </div>

        </form>

        <!-- TABEL -->
        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Foto</th>
                        <th>Judul Berita</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (count($daftar_berita) > 0): ?>

                    <?php foreach ($daftar_berita as $index => $item): ?>

                        <tr>
                            <td><?= $index + 1 ?></td>

                            <td>
                                <img
                                    src="<?= e($item['gambar']) ?>"
                                    alt="Foto berita"
                                    class="news-thumb"
                                >
                            </td>

                            <td style="min-width:200px;">
                                <strong><?= e($item['judul']) ?></strong>
                            </td>

                            <td><?= e($item['kategori']) ?></td>

                            <td>
                                <?= date('d-m-Y', strtotime($item['tanggal_publikasi'])) ?>
                            </td>

                            <td>
                                <?php if ($item['status'] === 'terbit'): ?>
                                    <span class="badge badge-terbit">
                                        Terbit
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-draft">
                                        Draft
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td style="min-width:130px;">
                                <div class="d-flex gap-1">

                                    <a href="edit_berita.php?id=<?= (int)$item['id'] ?>"
                                       class="btn btn-sm btn-warning"
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="hapus_berita.php"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus berita ini?')">

                                        <input type="hidden" name="id"
                                               value="<?= (int)$item['id'] ?>">

                                        <input type="hidden" name="csrf_token"
                                               value="<?= e($_SESSION['csrf_token']) ?>">

                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>

                                </div>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="7" class="text-center py-5 text-secondary">
                            <i class="bi bi-newspaper fs-2 d-block mb-2"></i>
                            Belum ada berita yang tersedia.
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
