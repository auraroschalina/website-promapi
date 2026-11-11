
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Proteksi halaman admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Mengambil statistik anggota
$total = $pdo->query("
    SELECT COUNT(*) FROM members
")->fetchColumn();

$aktif = $pdo->query("
    SELECT COUNT(*) FROM members WHERE status = 'aktif'
")->fetchColumn();

$menunggu = $pdo->query("
    SELECT COUNT(*) FROM members WHERE status = 'menunggu'
")->fetchColumn();

$ditolak = $pdo->query("
    SELECT COUNT(*) FROM members WHERE status = 'ditolak'
")->fetchColumn();

// Mengambil 5 pendaftaran terbaru
$stmt = $pdo->query("
    SELECT id, nama_lengkap, email, intansi, pekerjaan, status, created_at
    FROM members
    ORDER BY created_at DESC
    LIMIT 5
");

$pendaftaran = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | PROMAPI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --navy: #0A1F44;
            --navy-light: #173B68;
            --yellow: #F2C500;
        }

        body {
            background: #f4f6f9;
            font-family: Arial, sans-serif;
        }

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

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .stat-card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,.04);
            height: 100%;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 23px;
        }

        .table-card {
            background: white;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 3px 12px rgba(0,0,0,.04);
        }

        .btn-navy {
            background: var(--navy);
            color: white;
        }

        .btn-navy:hover {
            background: var(--navy-light);
            color: white;
        }

        @media(max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }

            .main-content {
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

    <a href="dashboard_admin.php" class="active">
        <i class="bi bi-grid me-2"></i> Dashboard
    </a>

    <a href="konfirmasi_pendaftaran.php">
        <i class="bi bi-person-check me-2"></i> Konfirmasi Pendaftaran
    </a>
    
    <a href="manajemen_anggota.php">
        <i class="bi bi-people me-2"></i> Manajemen Anggota
    </a>


    
    <a href="manajemen_berita.php">
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

<main class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold" style="color:#0A1F44;">
                Dashboard Admin
            </h3>
            <p class="text-muted mb-0">
                Selamat datang, <?= htmlspecialchars($_SESSION['admin_nama']) ?>.
            </p>
        </div>

        <span class="badge text-bg-light p-3">
            <i class="bi bi-shield-check me-1"></i>
            Administrator
        </span>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-2">Total Pendaftar</p>
                        <h3 class="fw-bold"><?= $total ?></h3>
                    </div>
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-2">Anggota Aktif</p>
                        <h3 class="fw-bold"><?= $aktif ?></h3>
                    </div>
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-person-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-2">Menunggu Konfirmasi</p>
                        <h3 class="fw-bold"><?= $menunggu ?></h3>
                    </div>
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-2">Ditolak</p>
                        <h3 class="fw-bold"><?= $ditolak ?></h3>
                    </div>
                    <div class="stat-icon bg-danger-subtle text-danger">
                        <i class="bi bi-person-x"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="table-card">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1">Pendaftaran Terbaru</h5>
                <small class="text-muted">Data diambil langsung dari database.</small>
            </div>

            <a href="manajemen_anggota.php" class="btn btn-navy btn-sm">
                Lihat Semua Anggota
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Instansi</th>
                        <th>Pekerjaan</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($pendaftaran): ?>
                        <?php foreach ($pendaftaran as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['intansi'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['pekerjaan'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    $warna = [
                                        'aktif' => 'success',
                                        'menunggu' => 'warning',
                                        'ditolak' => 'danger'
                                    ][$row['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge text-bg-<?= $warna ?>">
                                        <?= htmlspecialchars(ucfirst($row['status'])) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Belum ada pendaftaran anggota.
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
