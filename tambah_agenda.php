
<?php
session_start();

require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Agenda - PROMAPI</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
:root {
    --navy: #0A1F44;
    --navy-2: #173B68;
    --yellow: #F2C500;
    --background: #f4f6fa;
}

body {
    margin: 0;
    background: var(--background);
    font-family: Arial, sans-serif;
    color: #263746;
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
.main-content {
    margin-left: 250px;
    padding: 35px;
}

.form-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 3px 15px rgba(0,0,0,.04);
}

.form-label {
    font-weight: 600;
    color: #263746;
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

<!-- SIDEBAR -->
<aside class="sidebar">

    <div class="text-center mb-4">
        <img src="/promapi-website/assets/logo-promapi.png" alt="PROMAPI">
        <h5 class="mt-3 fw-bold">PROMAPI</h5>
    </div>

    <a href="dashboard_admin.php" >
        <i class="bi bi-grid me-2"></i> Dashboard
    </a>

    <a href="konfirmasi_pendaftaran.php">
        <i class="bi bi-person-check me-2"></i> Konfirmasi Pendaftaran
    </a>

      <a href="manajemen_anggota.php" >
        <i class="bi bi-people me-2"></i> Manajemen Anggota
    </a>
    
    <a href="manajemen_berita.php" >
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
<main class="main-content">

    <div class="mb-4">
        <a href="manajemen_agenda.php" class="text-decoration-none">
            <i class="bi bi-arrow-left"></i> Kembali ke Manajemen Agenda
        </a>

        <h3 class="fw-bold mt-3 mb-1">Tambah Agenda</h3>
        <p class="text-secondary">Masukkan informasi kegiatan PROMAPI.</p>
    </div>

    <div class="form-card">

        <form action="proses_tambah_agenda.php" method="POST">

            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="mb-3">
                <label class="form-label">Nama Kegiatan</label>
                <input type="text" name="judul" class="form-control"
                       placeholder="Masukkan nama kegiatan" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Kegiatan</label>
                <select name="jenis" class="form-select" required>
                    <option value="">Pilih jenis kegiatan</option>
                    <option value="Webinar">Webinar</option>
                    <option value="Diskusi">Diskusi</option>
                    <option value="Pertemuan">Pertemuan</option>
                    <option value="Seminar">Seminar</option>
                    <option value="Pelatihan">Pelatihan</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Kegiatan</label>
                    <input type="date" name="tanggal" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Waktu Kegiatan</label>
                    <input type="text" name="waktu" class="form-control"
                           placeholder="Contoh: 09.00 – 12.00 WIB" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Lokasi Kegiatan</label>
                <input type="text" name="lokasi" class="form-control"
                       placeholder="Contoh: Online / Zoom Meeting" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi Kegiatan</label>
                <textarea name="deskripsi" class="form-control" rows="6"
                          placeholder="Tuliskan deskripsi lengkap kegiatan..."></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Status Publikasi</label>
                <select name="status" class="form-select" required>
                    <option value="draft">Draft</option>
                    <option value="terbit">Terbit</option>
                </select>
                <small class="text-secondary">
                    Agenda berstatus terbit akan ditampilkan pada website utama.
                </small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Simpan Agenda
                </button>

                <a href="manajemen_agenda.php" class="btn btn-light border px-4">
                    Batal
                </a>
            </div>

        </form>

    </div>

</main>

</body>
</html>
