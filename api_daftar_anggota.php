
<?php
header('Content-Type: application/json; charset=utf-8');

require_once 'config/database.php';

try {
    $query = "SELECT
                nama_lengkap,
                intansi,
                kabupaten_kota,
                provinsi
              FROM members
              WHERE status = 'aktif'
              ORDER BY nama_lengkap ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    echo json_encode(
        $stmt->fetchAll(),
        JSON_UNESCAPED_UNICODE
    );

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Data anggota gagal dimuat.'
    ]);
}
?>
