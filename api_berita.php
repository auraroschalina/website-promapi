
<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config/database.php';

try {
    $sql = "SELECT
                id,
                judul,
                kategori,
                ringkasan,
                gambar,
                tanggal_publikasi
            FROM berita
            WHERE status = 'terbit'
            ORDER BY tanggal_publikasi DESC, created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $berita = $stmt->fetchAll();

    echo json_encode(
        $berita,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        'error' => 'Berita gagal dimuat.'
    ]);
}
