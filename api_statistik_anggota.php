
<?php
header('Content-Type: application/json; charset=utf-8');

require_once 'config/database.php';

try {
    $query = "SELECT
        COUNT(CASE WHEN status = 'aktif' THEN 1 END) AS total_anggota,
        COUNT(CASE WHEN status = 'menunggu' THEN 1 END) AS total_pendaftar
        FROM members";

    $stmt = $pdo->query($query);
    $data = $stmt->fetch();

    echo json_encode($data);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        'error' => 'Gagal mengambil statistik anggota.'
    ]);
}
?>
