
<?php
require_once __DIR__ . '/config/database.php';

$nama = "Admin PROMAPI";
$email = "adminpromapi@gmail.com";

// Ganti dengan password pilihanmu sendiri
$password = "11111111";

try {
    // Periksa apakah email sudah terdaftar
    $cek = $pdo->prepare(
        "SELECT id FROM admins WHERE email = :email"
    );
    $cek->execute([':email' => $email]);

    if ($cek->fetch()) {
        die("Akun admin dengan email tersebut sudah tersedia.");
    }

    // Amankan password menggunakan password_hash
    $password_hash = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $stmt = $pdo->prepare("
        INSERT INTO admins (nama, email, password_hash)
        VALUES (:nama, :email, :password_hash)
    ");

    $stmt->execute([
        ':nama' => $nama,
        ':email' => $email,
        ':password_hash' => $password_hash
    ]);

    echo "Akun admin berhasil dibuat!";

} catch (PDOException $e) {
    die("Gagal membuat akun admin: " . $e->getMessage());
}
