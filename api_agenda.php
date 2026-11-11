
<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config/database.php';

try {
    $sql = "SELECT
                id,
                judul,
                jenis,
                tanggal,
                waktu,
                lokasi,
                deskripsi
            FROM agenda
            WHERE status = 'terbit'
            AND tanggal >= CURDATE()
            ORDER BY tanggal ASC, created_at ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    echo json_encode(
        $stmt->fetchAll(),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Agenda gagal dimuat.'
    ]);
}
?>
