
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

// Ambil data agenda
try {
    $stmt = $pdo->query("
    SELECT *
    FROM agenda
    ORDER BY tanggal DESC, created_at DESC
");
    $daftarAgenda = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Gagal mengambil data agenda: " . $e->getMessage());
}

function e($value) {
    return htmlspecialchars((string)($value ?? '-'), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manajemen Agenda - PROMAPI</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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

@media(max-width: 768px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    .main-content {
        margin-left: 0;
        padding: 20px;
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

    <a href="konfirmasi_pendaftaran.php">
        <i class="bi bi-person-check me-2"></i> Konfirmasi Pendaftaran
    </a>
    
    <a href="manajemen_anggota.php">
        <i class="bi bi-people me-2"></i> Manajemen Anggota
    </a>



    <a href="manajemen_berita.php">
        <i class="bi bi-newspaper me-2"></i> Manajemen Berita
    </a>

    <a href="manajemen_agenda.php" class="active">
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
<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1">Manajemen Agenda</h3>
            <p class="text-secondary mb-0">Kelola agenda kegiatan PROMAPI.</p>
        </div>

        <a href="tambah_agenda.php" class="btn btn-primary">
            + Tambah Agenda
        </a>
    </div>

    <div class="content-card">

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
    <tr>
        <th>No.</th>
        <th>Nama Kegiatan</th>
        <th>Jenis</th>
        <th>Tanggal</th>
        <th>Waktu</th>
        <th>Lokasi</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
</thead>

                
<tbody>
<?php if (count($daftarAgenda) > 0): ?>
    <?php $no = 1; ?>

    <?php foreach ($daftarAgenda as $agenda): ?>
    <tr>
        <td><?= $no++ ?></td>

        <td>
            <strong><?= e($agenda['judul']) ?></strong>
        </td>

        <td><?= e($agenda['jenis']) ?></td>

        <td><?= date('d-m-Y', strtotime($agenda['tanggal'])) ?></td>

        <td><?= e($agenda['waktu']) ?></td>

        <td><?= e($agenda['lokasi']) ?></td>

        <td>
            <?php if ($agenda['status'] === 'terbit'): ?>
                <span class="badge bg-success">Terbit</span>
            <?php else: ?>
                <span class="badge bg-secondary">Draft</span>
            <?php endif; ?>
        </td>

        <td>
            <div class="d-flex gap-2 flex-wrap">

                <form action="ubah_status_agenda.php" method="POST">
                    <input type="hidden" name="csrf_token"
                           value="<?= e($_SESSION['csrf_token']) ?>">

                    <input type="hidden" name="id"
                           value="<?= (int)$agenda['id'] ?>">

                    <?php if ($agenda['status'] === 'terbit'): ?>
                        <input type="hidden" name="status" value="draft">
                        <button type="submit"
                                class="btn btn-sm btn-warning">
                            Jadikan Draft
                        </button>
                    <?php else: ?>
                        <input type="hidden" name="status" value="terbit">
                        <button type="submit"
                                class="btn btn-sm btn-success">
                            Terbitkan
                        </button>
                    <?php endif; ?>
                </form>

                <form action="hapus_agenda.php" method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus agenda ini?')">

                    <input type="hidden" name="csrf_token"
                           value="<?= e($_SESSION['csrf_token']) ?>">

                    <input type="hidden" name="id"
                           value="<?= (int)$agenda['id'] ?>">

                    <button type="submit" class="btn btn-sm btn-danger">
                        Hapus
                    </button>
                </form>

            </div>
        </td>
    </tr>
    <?php endforeach; ?>

<?php else: ?>
    <tr>
        <td colspan="8" class="text-center py-4">
            Belum ada agenda kegiatan.
        </td>
    </tr>
<?php endif; ?>
</tbody>

            </table>
        </div>

    </div>
</div>

</body>
</html>
