
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Proteksi halaman admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Membuat token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$pesan = "";
$jenis_pesan = "";

// Memproses persetujuan atau penolakan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $member_id = filter_input(INPUT_POST, 'member_id', FILTER_VALIDATE_INT);
    $aksi = $_POST['aksi'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $pesan = "Permintaan tidak valid. Silakan muat ulang halaman.";
        $jenis_pesan = "danger";

    } elseif (!$member_id || !in_array($aksi, ['setujui', 'tolak'], true)) {
        $pesan = "Data permintaan tidak valid.";
        $jenis_pesan = "danger";

    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                SELECT id, nomor_anggota
                FROM members
                WHERE id = :id AND status = 'menunggu'
                FOR UPDATE
            ");

            $stmt->execute([':id' => $member_id]);
            $anggota = $stmt->fetch();

            if (!$anggota) {
                throw new Exception("Data anggota tidak ditemukan atau sudah diproses.");
            }

            if ($aksi === 'setujui') {
                $nomor_anggota = 'PROMAPI-' . date('Y') . '-' .
                    str_pad($member_id, 5, '0', STR_PAD_LEFT);

                $update = $pdo->prepare("
                    UPDATE members
                    SET status = 'aktif',
                        nomor_anggota = :nomor_anggota
                    WHERE id = :id AND status = 'menunggu'
                ");

                $update->execute([
                    ':nomor_anggota' => $nomor_anggota,
                    ':id' => $member_id
                ]);

                $pesan = "Pendaftaran berhasil disetujui. Nomor anggota: " . $nomor_anggota;

            } else {
                $update = $pdo->prepare("
                    UPDATE members
                    SET status = 'ditolak'
                    WHERE id = :id AND status = 'menunggu'
                ");

                $update->execute([':id' => $member_id]);

                $pesan = "Pendaftaran anggota berhasil ditolak.";
            }

            if ($update->rowCount() !== 1) {
                throw new Exception("Status anggota gagal diperbarui.");
            }

            $pdo->commit();
            $jenis_pesan = "success";

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $pesan = $e->getMessage();
            $jenis_pesan = "danger";
        }
    }
}

// Mengambil daftar pendaftaran yang masih menunggu
$stmt = $pdo->prepare("
    SELECT
        id,
        nomor_anggota,
        nama_lengkap,
        email,
        no_hp,
        intansi,
        pekerjaan,
        foto,
        created_at
    FROM members
    WHERE status = 'menunggu'
    ORDER BY created_at ASC
");

$stmt->execute();
$daftar_anggota = $stmt->fetchAll();

$total_menunggu = count($daftar_anggota);

function e($value) {
    return htmlspecialchars((string)($value ?? '-'), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Konfirmasi Pendaftaran | PROMAPI</title>

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

        /* KONTEN UTAMA */
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

        .btn-approve {
            background: #198754;
            color: white;
        }

        .btn-approve:hover {
            background: #146c43;
            color: white;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
        }

        .btn-reject:hover {
            background: #bb2d3b;
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

        /* RESPONSIVE */
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

<!-- SIDEBAR -->
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

    <a href="konfirmasi_pendaftaran.php" class="active">
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

    <div class="mb-4">
        <h3 class="fw-bold" style="color:#0A1F44;">
            Konfirmasi Pendaftaran
        </h3>

        <p class="text-muted">
            Periksa dan kelola permohonan pendaftaran anggota PROMAPI.
        </p>
    </div>

    <!-- Pesan berhasil atau gagal -->
    <?php if ($pesan !== ""): ?>
        <div class="alert alert-<?= e($jenis_pesan) ?> alert-dismissible fade show">
            <?= e($pesan) ?>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    <?php endif; ?>

    <!-- Statistik -->
    <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <small class="text-muted">
                            Menunggu Konfirmasi
                        </small>

                        <h3 class="fw-bold mt-2 mb-0">
                            <?= $total_menunggu ?>
                        </h3>
                    </div>

                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- Tabel Pendaftaran -->
    <div class="content-card">

        <div class="mb-4">
            <h5 class="fw-bold mb-1">
                Daftar Permohonan Anggota
            </h5>

            <small class="text-muted">
                Terdapat <?= $total_menunggu ?> pendaftaran yang menunggu konfirmasi.
            </small>
        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>No.</th>
                        <th>Anggota</th>
                        <th>Email / No. HP</th>
                        <th>Instansi</th>
                        <th>Pekerjaan</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (count($daftar_anggota) > 0): ?>

                        <?php foreach ($daftar_anggota as $index => $anggota): ?>

                            <tr>

                                <td><?= $index + 1 ?></td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">

                                        <?php if (!empty($anggota['foto'])): ?>

                                            <img
                                                src="<?= e('/promapi-website/' . ltrim($anggota['foto'], '/')) ?>"
                                                class="member-photo"
                                                alt="Foto anggota"
                                            >

                                        <?php else: ?>

                                            <div class="member-photo bg-light d-flex align-items-center justify-content-center">
                                                <i class="bi bi-person text-secondary"></i>
                                            </div>

                                        <?php endif; ?>

                                        <div>
    <a href="detail_anggota.php?id=<?= (int)$anggota['id'] ?>&from=konfirmasi"
       class="fw-semibold text-decoration-none"
       style="color:#0A1F44;">

        <?= e($anggota['nama_lengkap']) ?>

    </a>

    <small class="text-muted d-block">
        ID: <?= e($anggota['id']) ?>
    </small>
</div>

                                    </div>
                                </td>

                                <td>
                                    <?= e($anggota['email']) ?><br>
                                    <small class="text-muted">
                                        <?= e($anggota['no_hp']) ?>
                                    </small>
                                </td>

                                <td><?= e($anggota['intansi']) ?></td>

                                <td><?= e($anggota['pekerjaan']) ?></td>

                                <td>
                                    <?= !empty($anggota['created_at'])
                                        ? date('d-m-Y', strtotime($anggota['created_at']))
                                        : '-' ?>
                                </td>

                                <td>
                                    <div class="d-flex flex-column gap-2">

                                        <!-- Tombol Setujui -->
                                        <form method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pendaftaran ini?');">

                                            <input type="hidden"
                                                   name="csrf_token"
                                                   value="<?= e($_SESSION['csrf_token']) ?>">

                                            <input type="hidden"
                                                   name="member_id"
                                                   value="<?= e($anggota['id']) ?>">

                                            <input type="hidden"
                                                   name="aksi"
                                                   value="setujui">

                                            <button type="submit"
                                                    class="btn btn-approve btn-sm w-100">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Setujui
                                            </button>

                                        </form>

                                        <!-- Tombol Tolak -->
                                        <form method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menolak pendaftaran ini?');">

                                            <input type="hidden"
                                                   name="csrf_token"
                                                   value="<?= e($_SESSION['csrf_token']) ?>">

                                            <input type="hidden"
                                                   name="member_id"
                                                   value="<?= e($anggota['id']) ?>">

                                            <input type="hidden"
                                                   name="aksi"
                                                   value="tolak">

                                            <button type="submit"
                                                    class="btn btn-reject btn-sm w-100">
                                                <i class="bi bi-x-circle me-1"></i>
                                                Tolak
                                            </button>

                                        </form>

                                    </div>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Belum ada pendaftaran anggota yang perlu dikonfirmasi.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
