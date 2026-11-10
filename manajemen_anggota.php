
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Proteksi halaman admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Pencarian dan filter
$keyword = trim($_GET['keyword'] ?? '');
$status = $_GET['status'] ?? '';

$sql = "
    SELECT
        id,
        nomor_anggota,
        foto,
        nama_lengkap,
        email,
        no_hp,
        intansi,
        pekerjaan,
        status,
        created_at
    FROM members
    WHERE 1=1
";

$params = [];

// Filter pencarian
if ($keyword !== '') {
    $sql .= " AND (nama_lengkap LIKE :nama OR email LIKE :email)";
    $params[':nama'] = "%{$keyword}%";
    $params[':email'] = "%{$keyword}%";
}

// Filter status
if (in_array($status, ['menunggu', 'aktif', 'ditolak'], true)) {
    $sql .= " AND status = :status";
    $params[':status'] = $status;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$anggota = $stmt->fetchAll();

// Menghitung jumlah anggota
$total = $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn();

$totalAktif = $pdo->query("
    SELECT COUNT(*) FROM members WHERE status = 'aktif'
")->fetchColumn();

$totalMenunggu = $pdo->query("
    SELECT COUNT(*) FROM members WHERE status = 'menunggu'
")->fetchColumn();

$totalDitolak = $pdo->query("
    SELECT COUNT(*) FROM members WHERE status = 'ditolak'
")->fetchColumn();

function e($value) {
    return htmlspecialchars((string)($value ?? '-'), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manajemen Anggota | PROMAPI</title>

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

        .stat-card,
        .content-card {
            background: white;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 3px 12px rgba(0,0,0,.04);
        }

        .stat-card {
            height: 100%;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 21px;
        }

        .btn-navy {
            background: var(--navy);
            color: white;
        }

        .btn-navy:hover {
            background: var(--navy-light);
            color: white;
        }

        .table th {
            white-space: nowrap;
            font-size: 13px;
            color: #596579;
        }

        .table td {
            font-size: 13px;
            vertical-align: middle;
        }

        .member-photo {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
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

    <a href="dashboard_admin.php">
        <i class="bi bi-grid me-2"></i> Dashboard
    </a>

    <a href="manajemen_anggota.php" class="active">
        <i class="bi bi-people me-2"></i> Manajemen Anggota
    </a>

    <a href="konfirmasi_pendaftaran.php">
        <i class="bi bi-person-check me-2"></i> Konfirmasi Pendaftaran
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

    <div class="mb-4">
        <h3 class="fw-bold" style="color:#0A1F44;">
            Manajemen Anggota
        </h3>
        <p class="text-muted">
            Kelola dan pantau seluruh data pendaftaran anggota PROMAPI.
        </p>
    </div>

    <!-- Statistik -->
    <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Total Pendaftar</small>
                        <h3 class="fw-bold mt-2"><?= $total ?></h3>
                    </div>
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Anggota Aktif</small>
                        <h3 class="fw-bold mt-2"><?= $totalAktif ?></h3>
                    </div>
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-person-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Menunggu Konfirmasi</small>
                        <h3 class="fw-bold mt-2"><?= $totalMenunggu ?></h3>
                    </div>
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Ditolak</small>
                        <h3 class="fw-bold mt-2"><?= $totalDitolak ?></h3>
                    </div>
                    <div class="stat-icon bg-danger-subtle text-danger">
                        <i class="bi bi-person-x"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Tabel Anggota -->
    <div class="content-card">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h5 class="fw-bold mb-1">Data Anggota PROMAPI</h5>
                <small class="text-muted">
                    Total <?= count($anggota) ?> data ditemukan.
                </small>
            </div>
        </div>

        <!-- Pencarian dan filter -->
        <form method="GET" class="row g-2 mb-4">

            <div class="col-md-6">
                <input
                    type="text"
                    name="keyword"
                    class="form-control"
                    placeholder="Cari nama atau email anggota..."
                    value="<?= e($keyword) ?>"
                >
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="menunggu" <?= $status === 'menunggu' ? 'selected' : '' ?>>
                        Menunggu
                    </option>
                    <option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>
                        Aktif
                    </option>
                    <option value="ditolak" <?= $status === 'ditolak' ? 'selected' : '' ?>>
                        Ditolak
                    </option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-navy flex-grow-1">
                    <i class="bi bi-search me-1"></i> Cari
                </button>

                <a href="manajemen_anggota.php" class="btn btn-outline-secondary">
                    Reset
                </a>
            </div>

        </form>

       
<!-- Tabel -->
<div class="table-responsive">

    <table class="table table-hover align-middle">

        <thead class="table-light">
            <tr>
                <th>No.</th>
                <th>Anggota</th>
                <th>Email</th>
                <th>No. HP</th>
                <th>Instansi</th>
                <th>Pekerjaan</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>

            <?php if (count($anggota) > 0): ?>

                <?php foreach ($anggota as $index => $row): ?>

                    <tr>
                        <td><?= $index + 1 ?></td>

                        <td>
                            <div class="d-flex align-items-center gap-2">

                                <?php if (!empty($row['foto'])): ?>
                                    <img
                                        src="<?= e('/promapi-website/' . ltrim($row['foto'], '/')) ?>"
                                        class="member-photo"
                                        alt="Foto anggota"
                                    >
                                <?php else: ?>
                                    <div class="member-photo bg-light d-flex align-items-center justify-content-center">
                                        <i class="bi bi-person text-secondary"></i>
                                    </div>
                                <?php endif; ?>

                                <div>
                                    <a href="detail_anggota.php?id=<?= (int)$row['id'] ?>&from=manajemen"
                                       class="fw-semibold text-decoration-none"
                                       style="color:#0A1F44;">
                                        <?= e($row['nama_lengkap']) ?>
                                    </a>

                                    <small class="text-muted d-block">
                                        <?= e($row['nomor_anggota'] ?: 'Belum memiliki nomor') ?>
                                    </small>
                                </div>

                            </div>
                        </td>

                        <td><?= e($row['email']) ?></td>
                        <td><?= e($row['no_hp']) ?></td>
                        <td><?= e($row['intansi']) ?></td>
                        <td><?= e($row['pekerjaan']) ?></td>

                        <!-- Status -->
                        <td>
                            <?php
                            $warna = [
                                'aktif' => 'success',
                                'menunggu' => 'warning',
                                'ditolak' => 'danger'
                            ][$row['status']] ?? 'secondary';
                            ?>

                            <span class="badge text-bg-<?= $warna ?>">
                                <?= e(ucfirst($row['status'])) ?>
                            </span>
                        </td>

                        <!-- Aksi -->
                        <td class="text-nowrap text-center">
                            <div class="d-flex justify-content-center align-items-center gap-2">

                                <a href="edit_anggota.php?id=<?= (int)$row['id'] ?>"
                                   class="btn btn-sm btn-warning"
                                   title="Edit Anggota">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form action="hapus_anggota.php"
                                      method="POST"
                                      class="m-0"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus anggota ini? Data yang dihapus tidak dapat dikembalikan.');">

                                    <input type="hidden" name="id"
                                           value="<?= (int)$row['id'] ?>">

                                    <input type="hidden" name="csrf_token"
                                           value="<?= e($_SESSION['csrf_token']) ?>">

                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            title="Hapus Anggota">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </form>

                            </div>
                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Data anggota tidak ditemukan.
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
