
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

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Berita - PROMAPI</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

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

.admin-main {
    margin-left: 250px;
    padding: 30px;
}

.topbar {
    margin-bottom: 25px;
}

.topbar h2 {
    color: var(--navy);
    font-size: 25px;
    font-weight: 700;
}

.form-card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,.04);
}

.form-label {
    font-size: 13px;
    font-weight: 700;
    color: #263746;
}

.form-control,
.form-select {
    padding: 11px 13px;
    font-size: 14px;
}

.form-text {
    font-size: 12px;
}

.btn-save {
    background: var(--navy);
    color: white;
    border: none;
    padding: 11px 22px;
    border-radius: 7px;
}

.btn-save:hover {
    background: var(--navy-2);
    color: white;
}

.image-preview {
    display: none;
    width: 100%;
    max-width: 400px;
    max-height: 250px;
    object-fit: cover;
    border-radius: 10px;
    margin-top: 15px;
}

@media(max-width:768px) {
    .admin-sidebar {
        position: relative;
        width: 100%;
        min-height: auto;
    }

    .admin-main {
        margin-left: 0;
        padding: 20px 12px;
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


    
    <a href="manajemen_berita.php" class="active">
        <i class="bi bi-newspaper me-2"></i> Manajemen Berita
    </a>

    <a href="manajemen_agenda.php">
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

<!-- KONTEN -->
<main class="admin-main">

    <div class="topbar">
        <h2>Tambah Berita</h2>
        <p class="text-secondary">
            Masukkan informasi berita yang akan dipublikasikan di website PROMAPI.
        </p>
    </div>

    <div class="form-card">

        <form action="proses_tambah_berita.php"
              method="POST"
              enctype="multipart/form-data">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= e($_SESSION['csrf_token']) ?>">

            <div class="mb-4">
                <label class="form-label">Judul Berita</label>
                <input type="text"
                       name="judul"
                       class="form-control"
                       placeholder="Masukkan judul berita"
                       maxlength="255"
                       required>
            </div>

            <div class="row g-3 mb-4">

                <div class="col-md-6">
                    <label class="form-label">Kategori Berita</label>
                    <select name="kategori" class="form-select" required>
                        <option value="">Pilih kategori</option>
                        <option value="Organisasi">Organisasi</option>
                        <option value="Kegiatan">Kegiatan</option>
                        <option value="Pendidikan">Pendidikan</option>
                        <option value="Informasi">Informasi</option>
                        <option value="Pengumuman">Pengumuman</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tanggal Publikasi</label>
                    <input type="date"
                           name="tanggal_publikasi"
                           class="form-control"
                           value="<?= date('Y-m-d') ?>"
                           required>
                </div>

            </div>

            <div class="mb-4">
                <label class="form-label">Foto Berita</label>
                <input type="file"
                       name="gambar"
                       id="gambar"
                       class="form-control"
                       accept=".jpg,.jpeg,.png,.webp"
                       required>

                <div class="form-text">
                    Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                </div>

                <img id="preview"
                     class="image-preview"
                     alt="Preview foto berita">
            </div>

            <div class="mb-4">
                <label class="form-label">Ringkasan Berita</label>
                <textarea name="ringkasan"
                          class="form-control"
                          rows="3"
                          placeholder="Tuliskan ringkasan singkat berita..."
                          required></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Isi Berita Lengkap</label>
                <textarea name="isi_berita"
                          class="form-control"
                          rows="12"
                          placeholder="Tuliskan isi berita secara lengkap..."
                          required></textarea>

                <div class="form-text">
                    Pisahkan paragraf menggunakan baris baru.
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Status Publikasi</label>
                <select name="status" class="form-select" required>
                    <option value="draft">Simpan sebagai Draft</option>
                    <option value="terbit">Terbitkan Berita</option>
                </select>
            </div>

            <hr>

            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <a href="manajemen_berita.php"
                   class="btn btn-light px-4">
                    Batal
                </a>

                <button type="submit" class="btn-save">
                    <i class="bi bi-save"></i>
                    Simpan Berita
                </button>
            </div>

        </form>

    </div>

</main>

<script>
document.getElementById('gambar').addEventListener('change', function() {
    const file = this.files[0];
    const preview = document.getElementById('preview');

    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
        preview.removeAttribute('src');
    }
});
</script>

</body>
</html>
