
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Proteksi halaman admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Mengambil ID anggota
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$from = $_GET['from'] ?? 'manajemen';

if (!in_array($from, ['manajemen', 'konfirmasi'], true)) {
    $from = 'manajemen';
}

if (!$id) {
    die("ID anggota tidak valid.");
}

// Mengambil data anggota
$stmt = $pdo->prepare("
    SELECT
        id,
        nomor_anggota,
        foto,
        nama_lengkap,
        tempat_lahir,
        tanggal_lahir,
        alamat_tempat_tinggal,
        provinsi,
        kabupaten_kota,
        kecamatan,
        kelurahan,
        no_hp,
        email,
        intansi,
        pekerjaan,
        status,
        created_at
    FROM members
    WHERE id = :id
    LIMIT 1
");

$stmt->execute([':id' => $id]);
$anggota = $stmt->fetch();

if (!$anggota) {
    die("Data anggota tidak ditemukan.");
}

// Mengambil riwayat pendidikan
$stmtPendidikan = $pdo->prepare("
    SELECT
        jenjang,
        jurusan_prodi,
        fakultas,
        universitas,
        tahun_lulus
    FROM education
    WHERE member_id = :member_id
    ORDER BY FIELD(jenjang, 'Sarjana', 'Magister', 'Doktor')
");

$stmtPendidikan->execute([':member_id' => $id]);
$pendidikan = $stmtPendidikan->fetchAll();

function e($value) {
    return htmlspecialchars((string)($value ?? '-'), ENT_QUOTES, 'UTF-8');
}

// Pengaturan status
$statusWarna = [
    'aktif' => 'success',
    'menunggu' => 'warning',
    'ditolak' => 'danger'
];

$warna = $statusWarna[$anggota['status']] ?? 'secondary';

// Link kembali
$linkKembali = $from === 'konfirmasi'
    ? 'konfirmasi_pendaftaran.php'
    : 'manajemen_anggota.php';

$namaHalamanKembali = $from === 'konfirmasi'
    ? 'Konfirmasi Pendaftaran'
    : 'Manajemen Anggota';

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Anggota | PROMAPI</title>

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

        /* KONTEN */
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .content-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 3px 12px rgba(0,0,0,.04);
            margin-bottom: 22px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .profile-photo {
            width: 115px;
            height: 115px;
            border-radius: 15px;
            object-fit: cover;
            border: 3px solid #e9edf3;
        }

        .profile-placeholder {
            width: 115px;
            height: 115px;
            border-radius: 15px;
            background: #edf1f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 45px;
            color: #8994a5;
        }

        .section-title {
            color: var(--navy);
            font-weight: 700;
            font-size: 17px;
            margin-bottom: 22px;
            padding-bottom: 12px;
            border-bottom: 1px solid #edf0f4;
        }

        .info-label {
            color: #7b8494;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .info-value {
            color: #25334a;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            overflow-wrap: anywhere;
        }

        .education-card {
            border: 1px solid #edf0f4;
            border-radius: 10px;
            padding: 18px;
            height: 100%;
        }

        .education-icon {
            width: 42px;
            height: 42px;
            background: #edf2fa;
            color: var(--navy);
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 20px;
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

            .profile-header {
                flex-direction: column;
                align-items: flex-start;
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
        <small>Panel Administrator</small>
    </div>

    <a href="dashboard_admin.php">
        <i class="bi bi-grid me-2"></i> Dashboard
    </a>

    <a href="manajemen_anggota.php"
       class="<?= $from === 'manajemen' ? 'active' : '' ?>">
        <i class="bi bi-people me-2"></i> Manajemen Anggota
    </a>

    <a href="konfirmasi_pendaftaran.php"
       class="<?= $from === 'konfirmasi' ? 'active' : '' ?>">
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

<!-- KONTEN UTAMA -->
<main class="main-content">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <h3 class="fw-bold" style="color:#0A1F44;">
                Detail Anggota
            </h3>

            <p class="text-muted mb-0">
                Informasi lengkap data anggota PROMAPI.
            </p>
        </div>

        <a href="<?= e($linkKembali) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke <?= e($namaHalamanKembali) ?>
        </a>

    </div>

    <!-- Profil Anggota -->
    <div class="content-card">

        <div class="profile-header">

            <?php if (!empty($anggota['foto'])): ?>

                <img
                    src="<?= e('/promapi-website/' . ltrim($anggota['foto'], '/')) ?>"
                    class="profile-photo"
                    alt="Foto anggota"
                >

            <?php else: ?>

                <div class="profile-placeholder">
                    <i class="bi bi-person"></i>
                </div>

            <?php endif; ?>

            <div>

                <h4 class="fw-bold mb-2" style="color:#0A1F44;">
                    <?= e($anggota['nama_lengkap']) ?>
                </h4>

                <div class="text-muted mb-2">
                    <?= e($anggota['email']) ?>
                </div>

                <span class="badge text-bg-<?= e($warna) ?>">
                    <?= e(ucfirst($anggota['status'])) ?>
                </span>

                <div class="mt-2 small text-muted">
                    Nomor Anggota:
                    <strong>
                        <?= e($anggota['nomor_anggota'] ?: 'Belum memiliki nomor') ?>
                    </strong>
                </div>

            </div>

        </div>

    </div>

    <!-- Data Pribadi -->
    <div class="content-card">

        <h5 class="section-title">
            <i class="bi bi-person-vcard me-2"></i>
            Informasi Pribadi
        </h5>

        <div class="row">

            <div class="col-md-6">
                <div class="info-label">Nama Lengkap</div>
                <div class="info-value">
                    <?= e($anggota['nama_lengkap']) ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-label">Email</div>
                <div class="info-value">
                    <?= e($anggota['email']) ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-label">Tempat Lahir</div>
                <div class="info-value">
                    <?= e($anggota['tempat_lahir']) ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-label">Tanggal Lahir</div>
                <div class="info-value">
                    <?= !empty($anggota['tanggal_lahir'])
                        ? date('d F Y', strtotime($anggota['tanggal_lahir']))
                        : '-' ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-label">Nomor HP</div>
                <div class="info-value">
                    <?= e($anggota['no_hp']) ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-label">Tanggal Pendaftaran</div>
                <div class="info-value">
                    <?= !empty($anggota['created_at'])
                        ? date('d F Y', strtotime($anggota['created_at']))
                        : '-' ?>
                </div>
            </div>

        </div>

    </div>

    <!-- Alamat -->
    <div class="content-card">

        <h5 class="section-title">
            <i class="bi bi-geo-alt me-2"></i>
            Informasi Alamat
        </h5>

        <div class="row">

            <div class="col-md-12">
                <div class="info-label">Alamat Lengkap</div>
                <div class="info-value">
                    <?= nl2br(e($anggota['alamat_tempat_tinggal'])) ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-label">Provinsi</div>
                <div class="info-value">
                    <?= e($anggota['provinsi']) ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-label">Kabupaten/Kota</div>
                <div class="info-value">
                    <?= e($anggota['kabupaten_kota']) ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-label">Kecamatan</div>
                <div class="info-value">
                    <?= e($anggota['kecamatan']) ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-label">Kelurahan/Desa</div>
                <div class="info-value">
                    <?= e($anggota['kelurahan']) ?>
                </div>
            </div>

        </div>

    </div>

    <!-- Informasi Pekerjaan -->
    <div class="content-card">

        <h5 class="section-title">
            <i class="bi bi-briefcase me-2"></i>
            Informasi Pekerjaan
        </h5>

        <div class="row">

            <div class="col-md-6">
                <div class="info-label">Instansi</div>
                <div class="info-value">
                    <?= e($anggota['intansi']) ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-label">Pekerjaan/Jabatan</div>
                <div class="info-value">
                    <?= e($anggota['pekerjaan']) ?>
                </div>
            </div>

        </div>

    </div>

    <!-- Riwayat Pendidikan -->
    <div class="content-card">

        <h5 class="section-title">
            <i class="bi bi-mortarboard me-2"></i>
            Riwayat Pendidikan
        </h5>

        <div class="row g-3">

            <?php if (count($pendidikan) > 0): ?>

                <?php foreach ($pendidikan as $edu): ?>

                    <div class="col-md-4">

                        <div class="education-card">

                            <div class="d-flex align-items-center gap-3 mb-3">

                                <div class="education-icon">
                                    <i class="bi bi-mortarboard"></i>
                                </div>

                                <div>
                                    <div class="fw-bold" style="color:#0A1F44;">
                                        <?= e($edu['jenjang']) ?>
                                    </div>

                                    <small class="text-muted">
                                        Pendidikan
                                    </small>
                                </div>

                            </div>

                            <div class="info-label">Program Studi/Jurusan</div>
                            <div class="info-value">
                                <?= e($edu['jurusan_prodi']) ?>
                            </div>

                            <div class="info-label">Fakultas</div>
                            <div class="info-value">
                                <?= e($edu['fakultas']) ?>
                            </div>

                            <div class="info-label">Universitas</div>
                            <div class="info-value">
                                <?= e($edu['universitas']) ?>
                            </div>

                            <div class="info-label">Tahun Lulus</div>
                            <div class="info-value mb-0">
                                <?= e($edu['tahun_lulus']) ?>
                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="col-12">
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Belum ada data pendidikan yang tersedia.
                    </div>
                </div>

            <?php endif; ?>

        </div>

    </div>

</main>

</body>
</html>
