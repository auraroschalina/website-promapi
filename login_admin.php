
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Jika admin sudah login, arahkan ke dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard_admin.php");
    exit;
}

$error = "";

// Membuat token CSRF
if (empty($_SESSION['csrf_login'])) {
    $_SESSION['csrf_login'] = bin2hex(random_bytes(32));
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_login'], $token)) {
        $error = "Sesi tidak valid. Silakan muat ulang halaman.";
    } else {

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $error = "Email dan password wajib diisi.";
        } else {
            $stmt = $pdo->prepare("
                SELECT id, nama, email, password_hash
                FROM admins
                WHERE email = :email
                LIMIT 1
            ");

            $stmt->execute([':email' => $email]);
            $admin = $stmt->fetch();

            if ($admin && password_verify(
                $password,
                $admin['password_hash']
            )) {

                session_regenerate_id(true);

                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nama'] = $admin['nama'];
                $_SESSION['admin_email'] = $admin['email'];

                unset($_SESSION['csrf_login']);

                header("Location: dashboard_admin.php");
                exit;

            } else {
                $error = "Email atau password tidak sesuai.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | PROMAPI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --navy: #0A1F44;
            --navy-light: #173B68;
            --yellow: #F2C500;
        }

        body {
            min-height: 100vh;
            background: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .login-card {
            background: white;
            width: 100%;
            max-width: 430px;
            padding: 38px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,.08);
        }

        .logo {
            width: 90px;
            height: 90px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .btn-login {
            background: var(--navy);
            color: white;
            border: none;
            padding: 12px;
            font-weight: 600;
        }

        .btn-login:hover {
            background: var(--navy-light);
            color: white;
        }

        .form-control {
            padding: 11px;
        }

        .brand-title {
            color: var(--navy);
            font-weight: 700;
        }

        .accent {
            color: #c49f00;
        }
    </style>
</head>

<body>

<div class="login-card">

    <div class="text-center mb-4">

        <img
            src="/promapi-website/assets/logo-promapi.png"
            alt="Logo PROMAPI"
            class="logo"
        >

        <h4 class="brand-title">LOGIN ADMIN</h4>
        <hr class="my-4">
        <p class="text-muted small">
            Silakan masuk untuk mengelola keanggotaan PROMAPI.
        </p>

    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger small">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_login']) ?>"
        >

        <div class="mb-3">
            <label class="form-label fw-semibold">
                Email Admin
            </label>

            <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Masukkan email admin"
                required
            >
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">
                Password
            </label>

            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Masukkan password"
                required
            >
        </div>

        <button type="submit" class="btn btn-login w-100">
            <i class="bi bi-box-arrow-in-right me-2"></i>
            Masuk sebagai Admin
        </button>

    </form>

    <div class="text-center mt-4">
        <a href="index.html" class="text-decoration-none small accent">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Website PROMAPI
        </a>
    </div>

</div>

</body>
</html>
