
<?php
require_once __DIR__ . '/config/database.php';

// Ambil kategori dari URL
$kategori = $_GET['kategori'] ?? 'nasional';

// Validasi kategori
if (!in_array($kategori, ['nasional', 'internasional'], true)) {
    $kategori = 'nasional';
}

// Tentukan judul halaman
$judul = $kategori === 'nasional'
    ? 'Jurnal Nasional'
    : 'Jurnal Internasional';

// Ambil data jurnal berdasarkan kategori
$stmt = $pdo->prepare("
    SELECT id, nama_jurnal, link_jurnal
    FROM publikasi
    WHERE kategori = :kategori
    ORDER BY nama_jurnal ASC
");

$stmt->execute([':kategori' => $kategori]);
$publikasi = $stmt->fetchAll();

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= e($judul); ?> | PROMAPI</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root {
    --navy: #102c4d;
    --navy-2: #173f68;
    --yellow: #f2c532;
    --ink: #17283b;
    --muted: #6d7b89;
    --soft: #f6f8fa;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    color: var(--ink);
    background: #fff;
}

.navbar {
    background: white;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
}

.navbar-brand img {
    height: 48px;
    width: auto;
    object-fit: contain;
}

.nav-link {
    color: var(--navy) !important;
    font-weight: 500;
}

.nav-link:hover,
.nav-link.active {
    color: #b18b00 !important;
}

.btn-primary-nav {
    background: var(--navy);
    color: white !important;
    border-radius: 7px;
    padding: 9px 17px;
    text-decoration: none;
}

.btn-primary-nav:hover {
    background: var(--navy-2);
}

.page-banner {
    background: var(--navy);
    color: white;
    padding: 85px 0 75px;
}

.page-banner .eyebrow {
    color: var(--yellow);
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.page-banner h1 {
    font-size: clamp(30px, 4vw, 44px);
    font-weight: 700;
    margin: 15px 0;
}

.page-banner p {
    color: #d5dfeb;
    max-width: 650px;
    margin-bottom: 0;
    line-height: 1.8;
}

.publication-section {
    padding: 65px 0 85px;
    min-height: 400px;
}

.publication-card {
    background: white;
    border: 1px solid #e8edf2;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(16,44,77,.05);
}

.table thead th {
    background: var(--navy);
    color: white;
    font-weight: 600;
    padding: 15px;
    white-space: nowrap;
}

.table tbody td {
    padding: 15px;
    vertical-align: middle;
}

.table tbody tr:hover {
    background: var(--soft);
}

.journal-link {
    color: var(--navy);
    font-weight: 600;
    text-decoration: none;
}

.journal-link:hover {
    color: #b18b00;
    text-decoration: underline;
}

.empty-state {
    text-align: center;
    padding: 45px 15px;
    color: var(--muted);
}

.empty-state i {
    font-size: 40px;
    color: #aab6c3;
    display: block;
    margin-bottom: 12px;
}

.site-footer {
    background: var(--navy);
    color: white;
    padding: 25px 0;
    text-align: center;
}

.site-footer p {
    color: #d5dfeb;
    margin: 0;
    font-size: 14px;
}

@media(max-width: 767px) {
    .page-banner {
        padding: 65px 0 55px;
    }

    .publication-section {
        padding: 40px 0 60px;
    }

    .publication-card {
        padding: 15px;
    }
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">

        <a class="navbar-brand" href="index.html">
            <img src="assets/logo-promapi.png" alt="Logo PROMAPI">
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">

                <li class="nav-item">
                    <a class="nav-link" href="index.html#beranda">Beranda</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#"
                       role="button" data-bs-toggle="dropdown">
                        Profil
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="index.html#tentang">Tentang Organisasi</a></li>
                        <li><a class="dropdown-item" href="index.html#visi-misi">Visi, Misi & Tujuan</a></li>
                        <li><a class="dropdown-item" href="index.html#struktur">Struktur Organisasi</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="index.html#berita">Berita</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="index.html#agenda">Agenda</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle active" href="#"
                       role="button" data-bs-toggle="dropdown">
                        Publikasi
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item"
                               href="publikasi.php?kategori=nasional">
                                Jurnal Nasional
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="publikasi.php?kategori=internasional">
                                Jurnal Internasional
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="index.html#galeri">Galeri</a>
                </li>

                <li class="nav-item ms-lg-2">
                    <a class="btn btn-primary-nav" href="dashboard.php">
                        Daftar <i class="bi bi-arrow-up-right"></i>
                    </a>
                </li>

            </ul>
        </div>

    </div>
</nav>

<!-- JUDUL HALAMAN -->
<section class="page-banner">
    <div class="container">
        <span class="eyebrow">Publikasi PROMAPI</span>
        <h1><?= e($judul); ?></h1>
        <p>
            Daftar publikasi jurnal yang menjadi bagian dari dokumentasi
            karya ilmiah dan pengembangan keilmuan di bidang administrasi
            dan manajemen pendidikan.
        </p>
    </div>
</section>

<!-- DAFTAR JURNAL -->
<section class="publication-section">
    <div class="container">

        <div class="publication-card">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <div>
                    <h4 class="fw-bold mb-1"><?= e($judul); ?></h4>
                    <p class="text-secondary mb-0">
                        Daftar jurnal publikasi PROMAPI
                    </p>
                </div>
            </div>

            <?php if (count($publikasi) > 0): ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="80">No.</th>
                                <th>Nama Jurnal</th>
                                <th width="180">Link Jurnal</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $no = 1; ?>

                            <?php foreach ($publikasi as $item): ?>
                                <tr>
                                    <td><?= $no++; ?></td>

                                    <td>
                                        <strong><?= e($item['nama_jurnal']); ?></strong>
                                    </td>

                                    <td>
                                        <a class="journal-link"
                                           href="<?= e($item['link_jurnal']); ?>"
                                           target="_blank"
                                           rel="noopener noreferrer">
                                            Kunjungi Jurnal
                                            <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

            <?php else: ?>

                <div class="empty-state">
                    <i class="bi bi-journal-x"></i>
                    <h5>Belum Ada Data Jurnal</h5>
                    <p class="mb-0">
                        Data <?= e(strtolower($judul)); ?> belum tersedia.
                    </p>
                </div>

            <?php endif; ?>

        </div>

    </div>
</section>

<!-- FOOTER -->
<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y'); ?> PROMAPI. Seluruh hak cipta dilindungi.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
