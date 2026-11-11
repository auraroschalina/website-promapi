
<?php
require_once __DIR__ . '/config/database.php';

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Ambil ID berita dari URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(404);
    die("Berita tidak ditemukan.");
}

// Ambil berita yang sudah diterbitkan
$stmt = $pdo->prepare("
    SELECT *
    FROM berita
    WHERE id = :id AND status = 'terbit'
    LIMIT 1
");

$stmt->execute(['id' => $id]);
$berita = $stmt->fetch();

if (!$berita) {
    http_response_code(404);
    die("Berita tidak ditemukan atau belum diterbitkan.");
}

// Format tanggal
$tanggal = date(
    'd F Y',
    strtotime($berita['tanggal_publikasi'])
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= e($berita['judul']) ?> | PROMAPI</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

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
    background: #fff;
    color: var(--ink);
    font-family: Arial, sans-serif;
}

/* Navbar */
.news-navbar {
    background: var(--navy);
    padding: 15px 0;
}

.news-navbar .navbar-brand {
    color: #fff;
    font-weight: 800;
    font-size: 22px;
}

.news-navbar .navbar-brand span {
    color: var(--yellow);
}

.news-navbar .nav-link {
    color: #fff;
    font-size: 14px;
}

.news-navbar .nav-link:hover {
    color: var(--yellow);
}

/* Konten */
.article-container {
    max-width: 900px;
    margin: 55px auto 80px;
    padding: 0 20px;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--navy);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 30px;
}

.back-link:hover {
    color: #b38b00;
}

.article-category {
    display: inline-block;
    background: #fff5cc;
    color: var(--navy);
    padding: 7px 14px;
    border-radius: 5px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .5px;
    margin-bottom: 18px;
}

.article-title {
    font-size: clamp(28px, 4vw, 42px);
    line-height: 1.25;
    font-weight: 800;
    color: var(--navy);
    margin-bottom: 18px;
}

.article-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
    color: var(--muted);
    font-size: 13px;
    margin-bottom: 28px;
}

.article-image {
    width: 100%;
    max-height: 480px;
    object-fit: cover;
    border-radius: 14px;
    margin-bottom: 32px;
}

.article-summary {
    font-size: 18px;
    font-weight: 600;
    line-height: 1.8;
    color: #34495e;
    border-left: 4px solid var(--yellow);
    padding-left: 20px;
    margin-bottom: 30px;
}

.article-content {
    font-size: 16px;
    line-height: 1.9;
    color: #354657;
    white-space: pre-line;
    overflow-wrap: anywhere;
}

.article-footer {
    border-top: 1px solid #e8edf2;
    margin-top: 45px;
    padding-top: 25px;
}

.btn-back {
    display: inline-block;
    background: var(--navy);
    color: #fff;
    text-decoration: none;
    padding: 12px 22px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
}

.btn-back:hover {
    background: var(--navy-2);
    color: var(--yellow);
}

/* Footer */
.news-footer {
    background: var(--navy);
    color: #dce5ee;
    padding: 25px 0;
    text-align: center;
    font-size: 13px;
}

.news-footer strong {
    color: var(--yellow);
}

@media(max-width:576px) {
    .article-container {
        margin-top: 35px;
    }

    .article-title {
        font-size: 27px;
    }

    .article-summary {
        font-size: 16px;
    }

    .article-content {
        font-size: 15px;
    }
}
</style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg news-navbar">
    <div class="container">

        <a class="navbar-brand" href="index.html">
            PROMAPI<span>.</span>
        </a>

        <button class="navbar-toggler bg-light"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarBerita">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarBerita">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.html">Beranda</a>
                <a class="nav-link" href="index.html#berita">Berita</a>
                <a class="nav-link" href="index.html#tentang">Tentang Kami</a>
            </div>
        </div>

    </div>
</nav>

<!-- Detail Berita -->
<main class="article-container">

    <a href="index.html#berita" class="back-link">
        <i class="bi bi-arrow-left"></i>
        Kembali ke Berita PROMAPI
    </a>

    <article>

        <span class="article-category">
            <?= e(strtoupper($berita['kategori'])) ?>
        </span>

        <h1 class="article-title">
            <?= e($berita['judul']) ?>
        </h1>

        <div class="article-meta">
            <span>
                <i class="bi bi-calendar3"></i>
                <?= e($tanggal) ?>
            </span>

            <span>
                <i class="bi bi-building"></i>
                PROMAPI
            </span>
        </div>

        <img
            src="<?= e($berita['gambar']) ?>"
            alt="<?= e($berita['judul']) ?>"
            class="article-image"
        >

        <div class="article-summary">
            <?= e($berita['ringkasan']) ?>
        </div>

        <div class="article-content"><?= e($berita['isi_berita']) ?></div>

        <div class="article-footer">
            <a href="index.html#berita" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                Kembali ke Berita
            </a>
        </div>

    </article>

</main>

<!-- Footer -->
<footer class="news-footer">
    <div class="container">
        © <?= date('Y') ?> <strong>PROMAPI</strong> -
        Perkumpulan Profesi Administrator dan Manajer Pendidikan Indonesia.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
