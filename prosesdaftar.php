
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Fungsi untuk menampilkan pesan dan menghentikan proses
function gagal($pesan)
{
    exit(
        '<div style="font-family:Arial;max-width:600px;margin:60px auto;padding:25px;border:1px solid #ddd;border-radius:10px;">'
        . '<h2>Pendaftaran Belum Berhasil</h2>'
        . '<p>' . htmlspecialchars($pesan) . '</p>'
        . '<a href="dashboard.php">Kembali ke formulir</a>'
        . '</div>'
    );
}

// Pastikan formulir dikirim menggunakan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    gagal('Permintaan tidak valid.');
}

// Periksa token keamanan
$token = $_POST['csrf_token'] ?? '';

if (
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    gagal('Sesi formulir tidak valid. Silakan muat ulang halaman dan coba kembali.');
}

// Ambil data formulir
$nama = trim($_POST['nama_lengkap'] ?? '');
$tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
$tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
$alamat_tempat_tinggal = trim($_POST['alamat_tempat_tinggal'] ?? '');
$provinsi = trim($_POST['provinsi'] ?? '');
$kabupaten_kota = trim($_POST['kabupaten_kota'] ?? '');
$kecamatan = trim($_POST['kecamatan'] ?? '');
$kelurahan = trim($_POST['kelurahan'] ?? '');
$no_hp = trim($_POST['no_hp'] ?? '');
$email = trim($_POST['email'] ?? '');
$pekerjaan = trim($_POST['pekerjaan'] ?? '');
$intansi = trim($_POST['intansi'] ?? '');
$pendidikan = $_POST['pendidikan'] ?? [];

// Validasi data wajib
if ($nama === '' || $email === '') {
    gagal('Nama lengkap dan email wajib diisi.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    gagal('Format email tidak valid.');
}

if (($_POST['persetujuan'] ?? '') !== '1') {
    gagal('Anda harus menyetujui pernyataan kebenaran data.');
}

// Validasi tanggal lahir
if ($tanggal_lahir !== '') {
    $tanggal = DateTime::createFromFormat('!Y-m-d', $tanggal_lahir);

    if (!$tanggal || $tanggal->format('Y-m-d') !== $tanggal_lahir) {
        gagal('Format tanggal lahir tidak valid.');
    }
} else {
    $tanggal_lahir = null;
}

// Validasi data pendidikan
$jenjangValid = ['Sarjana', 'Magister', 'Doktor'];
$dataPendidikan = [];

if (!is_array($pendidikan)) {
    gagal('Data pendidikan tidak valid.');
}

foreach ($pendidikan as $jenjang => $data) {

    if (!in_array($jenjang, $jenjangValid, true) || !is_array($data)) {
        gagal('Jenjang pendidikan tidak valid.');
    }

    $jurusan = trim($data['jurusan_prodi'] ?? '');
    $fakultas = trim($data['fakultas'] ?? '');
    $universitas = trim($data['universitas'] ?? '');
    $tahun = trim((string)($data['tahun_lulus'] ?? ''));

    // Lewati jenjang yang seluruh datanya kosong
    if (
        $jurusan === '' &&
        $fakultas === '' &&
        $universitas === '' &&
        $tahun === ''
    ) {
        continue;
    }

    if (
        $tahun !== '' &&
        (!ctype_digit($tahun) || (int)$tahun < 1950 || (int)$tahun > 2100)
    ) {
        gagal('Tahun lulus ' . $jenjang . ' tidak valid.');
    }

    $dataPendidikan[] = [
        'jenjang' => $jenjang,
        'jurusan_prodi' => $jurusan,
        'fakultas' => $fakultas,
        'universitas' => $universitas,
        'tahun_lulus' => $tahun === '' ? null : (int)$tahun
    ];
}

// Persiapkan upload foto
$fotoPath = null;
$fotoTujuan = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        gagal('Foto gagal diunggah. Silakan coba kembali.');
    }

    $foto = $_FILES['foto'];

    // Maksimal 500 KB
    if ($foto['size'] > 500 * 1024) {
        gagal('Ukuran foto maksimal adalah 500 KB.');
    }

    // Periksa tipe file berdasarkan isi file
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($foto['tmp_name']);

    $formatFoto = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($formatFoto[$mime])) {
        gagal('Format foto harus JPG, PNG, atau WEBP.');
    }

    // Buat nama file acak
    $namaFile = bin2hex(random_bytes(16)) . '.' . $formatFoto[$mime];

    $folderUpload = __DIR__ . '/uploads/';

    if (!is_dir($folderUpload)) {
        gagal('Folder uploads belum tersedia.');
    }

    $fotoTujuan = $folderUpload . $namaFile;
    $fotoPath = 'uploads/' . $namaFile;

    if (!move_uploaded_file($foto['tmp_name'], $fotoTujuan)) {
        gagal('Foto tidak dapat disimpan.');
    }
}

// Simpan data ke database
try {

    $pdo->beginTransaction();

    // Simpan data anggota
    $sqlMember = "INSERT INTO members (
        foto,
        nama_lengkap,
        tempat_lahir,
        tanggal_lahir,
        alamat_tempat_tinggal,
        provinsi,
        kabupaten_kota,
        kecamatan,
        kelurahan,
        no_hp,
        email,
        intansi,
        pekerjaan,
        status
    ) VALUES (
        :foto,
        :nama_lengkap,
        :tempat_lahir,
        :tanggal_lahir,
        :alamat_tempat_tinggal,
        :provinsi,
        :kabupaten_kota,
        :kecamatan,
        :kelurahan,
        :no_hp,
        :email,
        :pekerjaan,
        :intansi,
        'menunggu'
    )";

    $stmt = $pdo->prepare($sqlMember);

    $stmt->execute([
        ':foto' => $fotoPath,
        ':nama_lengkap' => $nama,
        ':tempat_lahir' => $tempat_lahir ?: null,
        ':tanggal_lahir' => $tanggal_lahir,
        ':alamat_tempat_tinggal' => $alamat_tempat_tinggal ?: null,
        ':provinsi' => $provinsi ?: null,
        ':kabupaten_kota' => $kabupaten_kota ?: null,
        ':kecamatan' => $kecamatan ?: null,
        ':kelurahan' => $kelurahan ?: null,
        ':no_hp' => $no_hp ?: null,
        ':email' => $email,
        ':intansi' => $intansi ?: null,
        ':pekerjaan' => $pekerjaan ?: null
    ]);

    // Ambil ID anggota yang baru disimpan
    $memberId = $pdo->lastInsertId();

    // Simpan pendidikan jika ada data
    if (!empty($dataPendidikan)) {

        $sqlEducation = "INSERT INTO education (
            member_id,
            jenjang,
            jurusan_prodi,
            fakultas,
            universitas,
            tahun_lulus
        ) VALUES (
            :member_id,
            :jenjang,
            :jurusan_prodi,
            :fakultas,
            :universitas,
            :tahun_lulus
        )";

        $stmtEducation = $pdo->prepare($sqlEducation);

        foreach ($dataPendidikan as $edu) {
            $stmtEducation->execute([
                ':member_id' => $memberId,
                ':jenjang' => $edu['jenjang'],
                ':jurusan_prodi' => $edu['jurusan_prodi'] ?: null,
                ':fakultas' => $edu['fakultas'] ?: null,
                ':universitas' => $edu['universitas'] ?: null,
                ':tahun_lulus' => $edu['tahun_lulus']
            ]);
        }
    }

    // Simpan seluruh perubahan
    $pdo->commit();

    // Hapus token agar formulir tidak dikirim ulang menggunakan sesi yang sama
    unset($_SESSION['csrf_token']);

    echo '
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Pendaftaran Berhasil</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <div class="card shadow-sm mx-auto text-center p-4" style="max-width:550px;">
                <h2 class="text-success">Pendaftaran Berhasil!</h2>
                <p>Terima kasih telah mendaftar sebagai anggota PROMAPI.</p>
                <p>Status pendaftaran Anda saat ini:
                    <strong>Menunggu Verifikasi</strong>
                </p>
                <a href="dashboard.php" class="btn btn-primary">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </body>
    </html>
    ';

} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Hapus foto jika penyimpanan database gagal
    if ($fotoTujuan && file_exists($fotoTujuan)) {
        unlink($fotoTujuan);
    }

    // Tangani email yang sudah digunakan
    if ($e->getCode() === '23000') {
        gagal('Email tersebut sudah terdaftar. Silakan gunakan email lain.');
    }

    error_log($e->getMessage());
    gagal('Terjadi kesalahan saat menyimpan data. Silakan coba kembali.');
}
