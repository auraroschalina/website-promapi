
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Proteksi halaman admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Fungsi keamanan output
function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Ambil ID anggota dari URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: manajemen_anggota.php");
    exit;
}

// Ambil data anggota
$stmt = $pdo->prepare("
    SELECT
        id,
        nomor_anggota,
        nama_lengkap,
        email,
        no_hp,
        intansi,
        pekerjaan,
        status
    FROM members
    WHERE id = :id
");

$stmt->execute(['id' => $id]);
$anggota = $stmt->fetch();

if (!$anggota) {
    echo "Data anggota tidak ditemukan.";
    exit;
}

// Siapkan token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota - PROMAPI</title>

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

        .content-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .form-label {
            font-weight: 600;
            color: #344054;
        }

        .btn-navy {
            background: var(--navy);
            color: white;
        }

        .btn-navy:hover {
            background: var(--navy-light);
            color: white;
        }
    </style>
</head>

<body>

<!-- Sidebar -->
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

<!-- Konten -->
<main class="main-content">

    <div class="mb-4">
        <h3 class="fw-bold" style="color:#0A1F44;">
            Edit Data Anggota
        </h3>
        <p class="text-muted">
            Perbarui informasi anggota PROMAPI melalui formulir berikut.
        </p>
    </div>

    <div class="content-card">

        <form action="proses_edit_anggota.php" method="POST">

            <input type="hidden" name="id"
                   value="<?= (int)$anggota['id'] ?>">

            <input type="hidden" name="csrf_token"
                   value="<?= e($_SESSION['csrf_token']) ?>">

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Nomor Anggota</label>
                    <input type="text" class="form-control"
                           value="<?= e($anggota['nomor_anggota'] ?: 'Belum memiliki nomor') ?>"
                           readonly>
                    <small class="text-muted">
                        Nomor anggota tidak dapat diubah melalui formulir ini.
                    </small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap"
                           class="form-control"
                           value="<?= e($anggota['nama_lengkap']) ?>"
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email"
                           class="form-control"
                           value="<?= e($anggota['email']) ?>"
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nomor HP</label>
                    <input type="text" name="no_hp"
                           class="form-control"
                           value="<?= e($anggota['no_hp']) ?>"
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Instansi</label>
                    <input type="text" name="intansi"
                           class="form-control"
                           value="<?= e($anggota['intansi']) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Pekerjaan</label>
                    <input type="text" name="pekerjaan"
                           class="form-control"
                           value="<?= e($anggota['pekerjaan']) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status Anggota</label>
                    <select name="status" class="form-select" required>
                        <option value="menunggu"
                            <?= $anggota['status'] === 'menunggu' ? 'selected' : '' ?>>
                            Menunggu
                        </option>

                        <option value="aktif"
                            <?= $anggota['status'] === 'aktif' ? 'selected' : '' ?>>
                            Aktif
                        </option>

                        <option value="ditolak"
                            <?= $anggota['status'] === 'ditolak' ? 'selected' : '' ?>>
                            Ditolak
                        </option>
                    </select>
                </div>

            </div>

            <hr class="my-4">

            <div class="d-flex gap-2 justify-content-end">
                <a href="manajemen_anggota.php"
                   class="btn btn-light border">
                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali
                </a>

                <button type="submit" class="btn btn-navy">
                    <i class="bi bi-save me-1"></i>
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

</main>

</body>
</html>
