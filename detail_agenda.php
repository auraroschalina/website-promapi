
<?php
require_once __DIR__ . '/config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("Agenda tidak ditemukan.");
}

try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM agenda
        WHERE id = :id
        AND status = 'terbit'
        LIMIT 1
    ");

    $stmt->execute([':id' => $id]);
    $agenda = $stmt->fetch();

    if (!$agenda) {
        http_response_code(404);
        die("Agenda tidak ditemukan atau belum dipublikasikan.");
    }

} catch (PDOException $e) {
    die("Gagal mengambil detail agenda.");
}

function e($value) {
    return htmlspecialchars((string)($value ?? '-'), ENT_QUOTES, 'UTF-8');
}

$tanggal = new DateTime($agenda['tanggal']);
$namaBulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
    4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September',
    10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$tanggalFormat = $tanggal->format('d') . ' ' .
                 $namaBulan[(int)$tanggal->format('n')] . ' ' .
                 $tanggal->format('Y');
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($agenda['judul']) ?> - PROMAPI</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
:root {
    --navy: #102c4d;
    --navy-2: #173f68;
    --yellow: #f2c532;
    --ink: #17283b;
    --muted: #6d7b89;
    --soft: #f6f8fa;
}

body {
    margin: 0;
    background: #fff;
    color: var(--ink);
    font-family: Arial, sans-serif;
}

.navbar-agenda {
    background: var(--navy);
    padding: 17px 0;
}

.navbar-agenda .navbar-brand {
    color: white;
    font-weight: 700;
    font-size: 22px;
}

.navbar-agenda .navbar-brand span {
    color: var(--yellow);
}

.navbar-agenda .nav-link {
    color: white;
}

.navbar-agenda .nav-link:hover {
    color: var(--yellow);
}

.agenda-header {
    background: var(--soft);
    padding: 65px 0 45px;
}

.agenda-label {
    color: #9a7510;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.agenda-title {
    color: var(--navy);
    font-size: clamp(28px, 4vw, 42px);
    font-weight: 800;
    margin: 15px 0;
}

.agenda-meta {
    color: var(--muted);
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
}

.agenda-content {
    max-width: 850px;
    margin: 0 auto;
    padding: 55px 20px 80px;
}

.agenda-description {
    font-size: 17px;
    line-height: 1.9;
    color: #263746;
    white-space: pre-line;
}

.btn-back {
    background: var(--navy);
    color: white;
    border-radius: 7px;
    padding: 11px 20px;
    text-decoration: none;
    display: inline-block;
}

.btn-back:hover {
    background: var(--navy-2);
    color: white;
}

.site-footer {
    background: var(--navy);
    color: #dce5ee;
    padding: 25px 0;
    text-align: center;
}
</style>
</head>

<body>

<nav class="navbar-agenda">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="index.html" class="navbar-brand text-decoration-none">
            PROMAPI<span>.</span>
        </a>

        <a href="index.html" class="nav-link">
            <i class="bi bi-house me-1"></i> Beranda
        </a>
    </div>
</nav>

<header class="agenda-header">
    <div class="container">
        <span class="agenda-label">
            <?= e($agenda['jenis']) ?>
        </span>

        <h1 class="agenda-title">
            <?= e($agenda['judul']) ?>
        </h1>

        <div class="agenda-meta">
            <span>
                <i class="bi bi-calendar-event me-2"></i>
                <?= e($tanggalFormat) ?>
            </span>

            <span>
                <i class="bi bi-clock me-2"></i>
                <?= e($agenda['waktu']) ?>
            </span>

            <span>
                <i class="bi bi-geo-alt me-2"></i>
                <?= e($agenda['lokasi']) ?>
            </span>
        </div>
    </div>
</header>

<main class="agenda-content">

    <h4 class="fw-bold mb-4">Deskripsi Kegiatan</h4>

    <div class="agenda-description">
        <?= e($agenda['deskripsi'] ?: 'Belum ada deskripsi kegiatan.') ?>
    </div>

    <div class="mt-5">
        <a href="index.html#agenda" class="btn-back">
            <i class="bi bi-arrow-left me-2"></i>
            Kembali ke Agenda
        </a>
    </div>

</main>

<footer class="site-footer">
    <div class="container">
        <small>&copy; <?= date('Y') ?> PROMAPI. Seluruh hak cipta dilindungi.</small>
    </div>
</footer>

</body>
</html>
