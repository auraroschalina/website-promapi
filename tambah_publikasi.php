
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
<title>Tambah Publikasi | PROMAPI</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root {
    --navy: #0A1F44;
    --navy-light: #173B68;
    --yellow: #F2C500;
    --bg: #f4f6f9;
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
    padding: 28px;
    box-shadow: 0 3px 15px rgba(0,0,0,.05);
    max-width: 850px;
}

.form-label {
    font-weight: 600;
    color: var(--navy);
}

.form-control,
.form-select {
    padding: 11px 13px;
    border-radius: 8px;
}

.btn-save {
    background: var(--navy);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
}

.btn-save:hover {
    background: var(--navy-light);
    color: white;
}

.btn-back {
    color: #526174;
    text-decoration: none;
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

    <div class="mb-4">
        <a href="manajemen_publikasi.php" class="btn-back">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>

        <h1 class="page-title mt-3 mb-1">Tambah Jurnal</h1>
        <p class="text-secondary mb-0">
            Masukkan informasi jurnal yang akan ditampilkan pada website PROMAPI.
        </p>
    </div>

    <div class="content-card">

        <form action="proses_tambah_publikasi.php" method="POST">

            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

            <div class="mb-4">
                <label class="form-label">Kategori Jurnal</label>
                <select name="kategori" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="nasional">Jurnal Nasional</option>
                    <option value="internasional">Jurnal Internasional</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Nama Jurnal</label>
                <input type="text"
                       name="nama_jurnal"
                       class="form-control"
                       placeholder="Masukkan nama jurnal"
                       maxlength="255"
                       required>
            </div>

            <div class="mb-4">
                <label class="form-label">Link Jurnal</label>
                <input type="url"
                       name="link_jurnal"
                       class="form-control"
                       placeholder="https://contoh.com/jurnal"
                       maxlength="500"
                       required>
                <small class="text-secondary">
                    Masukkan alamat URL lengkap yang diawali https:// atau http://
                </small>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="manajemen_publikasi.php" class="btn btn-light">
                    Batal
                </a>

                <button type="submit" class="btn-save">
                    <i class="bi bi-save me-1"></i> Simpan Jurnal
                </button>
            </div>

        </form>

    </div>

</main>

</body>
</html>
