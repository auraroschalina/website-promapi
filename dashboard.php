<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portal Keanggotaan PROMAPI</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root {
    --navy: #0A1F44;
    --navy-light: #173B68;
    --yellow: #F2C500;
    --bg: #f4f6f9;
}

* { box-sizing: border-box; }

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: var(--bg);
    color: #333;
    font-size: 13px;
}

/* SIDEBAR */
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 240px;
    height: 100vh;
    background: white;
    border-right: 1px solid #ddd;
    z-index: 1000;
    transition: .3s;
}

.brand {
    height: 65px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 1px solid #ddd;
    font-weight: bold;
    color: var(--navy);
    font-size: 17px;
}

.brand img {
    width: 38px;
    height: 38px;
    object-fit: contain;
    margin-right: 8px;
}

.sidebar-content { padding: 12px; }

.portal-label {
    padding: 12px 5px;
    border-bottom: 1px solid #eee;
    margin-bottom: 15px;
    color: #555;
}

.menu-link {
    display: block;
    padding: 11px 12px;
    color: #444;
    text-decoration: none;
    border-radius: 4px;
    margin-bottom: 5px;
    cursor: pointer;
}

.menu-link i {
    margin-right: 7px;
}

.menu-link:hover,
.menu-link.active {
    background: var(--navy);
    color: white;
}

/* MAIN */
.main {
    margin-left: 240px;
    transition: .3s;
}

.topbar {
    height: 52px;
    background: var(--navy);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    color: white;
}

.toggle-btn {
    background: transparent;
    border: 1px solid white;
    color: white;
    padding: 5px 12px;
    border-radius: 4px;
}

.topbar a {
    color: white;
    text-decoration: none;
}

.content {
    padding: 20px;
}

.page-title {
    font-size: 21px;
    font-weight: bold;
    color: var(--navy);
    margin-bottom: 20px;
}

/* DASHBOARD CARDS */
.stat-card {
    border-radius: 5px;
    padding: 18px;
    color: white;
    min-height: 105px;
    position: relative;
    overflow: hidden;
}

.stat-card h2 {
    font-size: 27px;
    font-weight: bold;
    margin: 0;
}

.stat-card p {
    margin: 8px 0 0;
}

.stat-card i {
    position: absolute;
    right: 15px;
    top: 20px;
    font-size: 48px;
    opacity: .18;
}

.card-navy { background: var(--navy); }
.card-yellow { background: var(--yellow); color: var(--navy); }
.card-blue { background: var(--navy-light); }

/* CONTENT CARD */
.panel {
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-top: 20px;
    padding: 15px;
}

.panel-title {
    font-size: 15px;
    font-weight: bold;
    margin-bottom: 15px;
    color: var(--navy);
}

.section-page {
    display: none;
}

.section-page.active {
    display: block;
}

/* TABLE */
.table th {
    background: #f0f2f5;
    color: var(--navy);
    white-space: nowrap;
}

.table td, .table th {
    padding: 11px;
    vertical-align: middle;
}

.table-responsive {
    overflow-x: auto;
}

/* FORM */
.form-label {
    font-weight: bold;
    font-size: 12px;
    margin-bottom: 5px;
}

.form-control, .form-select {
    font-size: 13px;
    border-radius: 3px;
}

.form-control:focus, .form-select:focus {
    border-color: var(--navy);
    box-shadow: 0 0 0 2px rgba(10,31,68,.1);
}

.form-group {
    margin-bottom: 15px;
}

.form-heading {
    font-size: 15px;
    font-weight: bold;
    padding: 10px;
    margin-bottom: 15px;
    background: var(--navy);
    color: white;
    border-radius: 3px;
}

.btn-navy {
    background: var(--navy);
    color: white;
    border: none;
}

.btn-navy:hover {
    background: var(--navy-light);
    color: white;
}

.btn-gold {
    background: var(--yellow);
    color: var(--navy);
    border: none;
    font-weight: bold;
}

.btn-gold:hover {
    background: #dfb600;
    color: var(--navy);
}

/* PROFILE PHOTO */
.photo-box {
    width: 120px;
    height: 120px;
    border: 1px solid #ddd;
    background: #f4f4f4;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-bottom: 10px;
}

.photo-box i {
    font-size: 75px;
    color: #aaa;
}

.photo-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* EDUCATION */
.education-card {
    background: #343a40;
    padding: 15px;
    color: white;
    border-radius: 4px;
    margin-bottom: 15px;
}

.education-card .form-label {
    color: white;
}

/* CONFIRMATION */
.instruction {
    line-height: 1.8;
    font-size: 13px;
}

.email-banner {
    background: var(--yellow);
    color: var(--navy);
    padding: 17px;
    border-radius: 4px;
    font-size: 23px;
    margin-top: 20px;
}

.warning-banner {
    background: #dc3545;
    color: white;
    padding: 13px;
    border-radius: 4px;
    margin-top: 12px;
    font-size: 11px;
}

/* RESPONSIVE */
@media(max-width: 768px) {
    .sidebar {
        width: 240px;
        transform: translateX(-100%);
    }

    .sidebar.show {
        transform: translateX(0);
    }

    .main {
        margin-left: 0;
    }

    .content {
        padding: 12px;
    }

    .topbar {
        padding: 0 12px;
    }
    
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">

    <div class="brand">
       <img src="/promapi-website/assets/logo-promapi.png"
     alt="Logo PROMAPI"
     class="logo">
        PROMAPI
    </div>

    <div class="sidebar-content">

        <div class="portal-label">
            <i class="bi bi-globe"></i>
            Portal Keanggotaan
        </div>

        <a class="menu-link active" data-page="dashboard">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a class="menu-link" data-page="anggota">
            <i class="bi bi-people-fill"></i> Anggota PROMAPI
        </a>

        <a class="menu-link" data-page="pendaftaran">
            <i class="bi bi-pencil-square"></i> Pendaftaran
        </a>

        <a class="menu-link" data-page="konfirmasi">
            <i class="bi bi-cash-stack"></i> Konfirmasi Pendaftaran
        </a>

        <a class="menu-link" data-page="login">
            <i class="bi bi-box-arrow-in-right"></i> Login
        </a>

        <hr>

        <a href="index.html" class="menu-link">
            <i class="bi bi-house"></i> Kembali ke Website
        </a>

    </div>
</aside>


<!-- MAIN -->
<div class="main">

    <!-- NAVBAR -->
    <nav class="topbar">
        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>

        <a href="index.html">
            <i class="bi bi-globe"></i> Website PROMAPI
        </a>
    </nav>


    <div class="content">

        <!-- DASHBOARD -->
        <section class="section-page active" id="dashboard">

            <h2 class="page-title">
                PROMAPI
                <small class="d-block text-secondary fs-6">
                    Perkumpulan Profesi Manajer dan Administrator Pendidikan
                </small>
            </h2>

            <div class="row g-3">

                <div class="col-md-4">
                    <div class="stat-card card-navy">
                        <h2 id="totalAnggota">0</h2>
                        <p>Anggota Aktif</p>
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="stat-card card-yellow">
                        <h2 id="totalPendaftar">0</h2>
                        <p>Pendaftaran Anggota Baru</p>
                        <i class="bi bi-pencil-square"></i>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="stat-card card-blue">
                        <h2><i class="bi bi-geo-alt-fill"></i></h2>
                        <p>Sekretariat PROMAPI</p>
                        <i class="bi bi-map"></i>
                    </div>
                </div>

            </div>

            <div class="panel">
                <div class="panel-title">
                    <i class="bi bi-geo-alt-fill"></i>
                    Sekretariat PROMAPI
                </div>

                <p>Alamat sekretariat organisasi PROMAPI, Indonesia.</p>

               
<div class="location-card">

<div class="map-wrapper position-relative">

   
<div style="
    position: relative;
    width: 100%;
    height: 350px;
    border-radius: 12px;
    overflow: hidden;
">

    <!-- PETA -->
    <iframe
        src="https://www.openstreetmap.org/export/embed.html?bbox=107.5850%2C-6.8675%2C107.6035%2C-6.8540&layer=mapnik&marker=-6.861116%2C107.594332"
        style="
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
            z-index: 1;
        "
        loading="lazy">
    </iframe>

    <!-- KOTAK INFORMASI DI POJOK KIRI BAWAH -->
    <div style="
        position: absolute;
        bottom: 15px;
        left: 15px;
        width: 290px;
        max-width: calc(100% - 30px);
        background: #ffffff;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.25);
        z-index: 999;
    ">

        <div style="
            color: #0A1F44;
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 8px;
        ">
            <i class="bi bi-geo-alt-fill"></i>
            Lokasi PROMAPI
        </div>

        <div style="
            font-size: 12px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 12px;
        ">
            Universitas Pendidikan Indonesia (UPI)<br>
            Jl. Dr. Setiabudhi No. 229,<br>
            Isola, Sukasari, Kota Bandung,<br>
            Jawa Barat 40154.
        </div>

        <a
            href="https://www.google.com/maps/dir/?api=1&destination=-6.861116%2C107.594332"
            target="_blank"
            rel="noopener noreferrer"
            style="
                display: inline-block;
                background: #0A1F44;
                color: #ffffff;
                text-decoration: none;
                padding: 8px 12px;
                border-radius: 6px;
                font-size: 12px;
                font-weight: 600;
            ">
            <i class="bi bi-map"></i>
            Lanjutkan ke Maps
            <i class="bi bi-arrow-up-right"></i>
        </a>

    </div>

</div>


</div>


            </div>

        </section>


        
<!-- ANGGOTA -->
<section class="section-page" id="anggota">
    <div class="panel">

        <div class="d-flex justify-content-between mb-3">
            <h5 class="panel-title">
                <i class="bi bi-people-fill"></i>
                Daftar Anggota PROMAPI
            </h5>

            <button class="btn btn-sm btn-secondary" onclick="resetFilter()">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" id="filterNama"
                    class="form-control"
                    placeholder="Nama Anggota...">
            </div>

            <div class="col-md-3">
                <input type="text" id="filterKerja"
                    class="form-control"
                    placeholder="Tempat Kerja...">
            </div>

            <div class="col-md-3">
                <select class="form-select" id="filterProvinsi">
                    <option value="">- Provinsi -</option>
                    <option>Jawa Barat</option>
                    <option>Jawa Tengah</option>
                    <option>DKI Jakarta</option>
                    <option>Jawa Timur</option>
                </select>
            </div>

            <div class="col-md-3">
                <button class="btn btn-navy w-100" onclick="filterAnggota()">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tableAnggota">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Anggota</th>
                        <th>Tempat Kerja</th>
                        <th>Kabupaten/Kota</th>
                        <th>Provinsi</th>
                    </tr>
                </thead>

                <tbody id="daftarAnggota">
                    <tr>
                        <td colspan="5" class="text-center">
                            Memuat data anggota...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</section>



        <!-- PENDAFTARAN -->
        <section class="section-page" id="pendaftaran">

            <div class="panel">

                <h5 class="panel-title">
                    <i class="bi bi-pencil-square"></i>
                    PENDAFTARAN ANGGOTA PROMAPI
                </h5>


<form id="formPendaftaran"
      action="prosesdaftar.php"
      method="POST"
      enctype="multipart/form-data">

<input type="hidden"
           name="csrf_token"
           value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="photo-box" id="photoPreview">
                <i class="bi bi-person-fill"></i>
            </div>

            <label class="form-label">Upload Pas Foto</label>
            <input type="file"
                   class="form-control"
                   id="foto"
                   name="foto"
                   accept=".jpg,.jpeg,.png,.webp">

            <small class="text-muted">
                Format JPG/PNG, maksimal 500 KB.
            </small>
        </div>
    </div>

    <div class="row g-3">

        <!-- DATA DIRI -->
        <div class="col-lg-6">
            <h5 class="text-primary border-bottom pb-2">
                Data Diri
            </h5>

            <div class="form-group">
                <label class="form-label">Nama Lengkap dan Gelar</label>
                <input type="text"
                       name="nama_lengkap"
                       class="form-control"
                       required>
            </div>

            <div class="row">
                <div class="col-md-7 form-group">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text"
                           name="tempat_lahir"
                           class="form-control">
                </div>

                <div class="col-md-5 form-group">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date"
                           name="tanggal_lahir"
                           class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Tempat Tinggal</label>
                <textarea name="alamat_tempat_tinggal"
                          class="form-control"
                          rows="2"></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="form-label">Provinsi</label>
                    <input type="text"
                           name="provinsi"
                           class="form-control">
                </div>

                <div class="col-md-6 form-group">
                    <label class="form-label">Kabupaten/Kota</label>
                    <input type="text"
                           name="kabupaten_kota"
                           class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="form-label">Kecamatan</label>
                    <input type="text"
                           name="kecamatan"
                           class="form-control">
                </div>

                <div class="col-md-6 form-group">
                    <label class="form-label">Kelurahan</label>
                    <input type="text"
                           name="kelurahan"
                           class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="form-label">Nomor HP</label>
                    <input type="tel"
                           name="no_hp"
                           class="form-control">
                </div>

                <div class="col-md-6 form-group">
                    <label class="form-label">Email</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Pekerjaan</label>
                <input type="text"
                       name="pekerjaan"
                       class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label">Intansi</label>
                <input type="text"
                       name="intansi"
                        class="form-control">
            </div>
        </div>

        <!-- DATA PENDIDIKAN -->
        <div class="col-lg-6">

            <!-- SARJANA -->
            <div class="education-card">
                <h5>Sarjana</h5>

                <div class="form-group">
                    <label class="form-label">Jurusan / Prodi</label>
                    <input type="text"
                           name="pendidikan[Sarjana][jurusan_prodi]"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Fakultas</label>
                    <input type="text"
                           name="pendidikan[Sarjana][fakultas]"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Universitas</label>
                    <input type="text"
                           name="pendidikan[Sarjana][universitas]"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Tahun Lulus</label>
                    <input type="number"
                           name="pendidikan[Sarjana][tahun_lulus]"
                           class="form-control"
                           min="1950"
                           max="2100">
                </div>
            </div>

            <!-- MAGISTER -->
            <div class="education-card">
                <h5>Magister</h5>

                <div class="form-group">
                    <label class="form-label">Jurusan / Prodi</label>
                    <input type="text"
                           name="pendidikan[Magister][jurusan_prodi]"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Fakultas</label>
                    <input type="text"
                           name="pendidikan[Magister][fakultas]"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Universitas</label>
                    <input type="text"
                           name="pendidikan[Magister][universitas]"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Tahun Lulus</label>
                    <input type="number"
                           name="pendidikan[Magister][tahun_lulus]"
                           class="form-control"
                           min="1950"
                           max="2100">
                </div>
            </div>

            <!-- DOKTOR -->
            <div class="education-card">
                <h5>Doktor</h5>

                <div class="form-group">
                    <label class="form-label">Jurusan / Prodi</label>
                    <input type="text"
                           name="pendidikan[Doktor][jurusan_prodi]"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Fakultas</label>
                    <input type="text"
                           name="pendidikan[Doktor][fakultas]"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Universitas</label>
                    <input type="text"
                           name="pendidikan[Doktor][universitas]"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Tahun Lulus</label>
                    <input type="number"
                           name="pendidikan[Doktor][tahun_lulus]"
                           class="form-control"
                           min="1950"
                           max="2100">
                </div>
            </div>

        </div>
    </div>

    <hr>

    <div class="form-check mb-3">
        <input class="form-check-input"
               type="checkbox"
               name="persetujuan"
               value="1"
               required>

        <label class="form-check-label">
            Saya menyatakan bahwa data yang diisi benar.
        </label>
    </div>

    <button type="submit" class="btn btn-gold px-4">
        <i class="bi bi-send"></i> Kirim Pendaftaran
    </button>

    <button type="reset" class="btn btn-secondary px-4">
        Reset
    </button>

</form>

            </div>

        </section>


        <!-- KONFIRMASI -->
        <section class="section-page" id="konfirmasi">

            <div class="panel">

                <h5 class="panel-title">
                    <i class="bi bi-cash-stack"></i>
                    KONFIRMASI REGISTRASI PESERTA
                </h5>

                <hr>

                <strong>Konfirmasi Pendaftaran:</strong>

                <div class="instruction mt-3">

                    <ol>
                        <li>
                            Bagi anggota yang ingin memproses lebih lanjut
                            untuk mendapatkan kartu anggota, dapat mengikuti
                            prosedur administrasi PROMAPI.
                        </li>

                        <li>
                            Melakukan pembayaran iuran anggota sesuai
                            ketentuan organisasi.
                        </li>

                        <li>
                            Mengirimkan bukti pembayaran melalui kontak
                            sekretariat PROMAPI.
                        </li>

                        <li>
                            Setelah data dinyatakan valid, administrator
                            akan melakukan verifikasi pendaftaran.
                        </li>

                        <li>
                            Kartu anggota dapat diproses setelah
                            seluruh persyaratan terpenuhi.
                        </li>
                    </ol>

                </div>

                <div class="email-banner">
                    <i class="bi bi-envelope-fill"></i>
                    sekretariat@promapi.or.id
                </div>

                <div class="warning-banner">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Catatan: Informasi biaya, rekening pembayaran, dan
                    prosedur administrasi akan disesuaikan dengan ketentuan
                    resmi PROMAPI.
                </div>

            </div>

        </section>


        <!-- LOGIN -->
        <section class="section-page" id="login">

            <div class="row justify-content-center">

                <div class="col-md-6 col-lg-5">

                    <div class="panel p-4">

                        <div class="text-center mb-4">
                            <img src="assets/logo-promapi.png"
                                style="width:90px;height:90px;object-fit:contain">

                            <h4 class="mt-2" style="color:var(--navy)">
                                Login Anggota PROMAPI
                            </h4>

                            <p class="text-muted">
                                Portal Keanggotaan PROMAPI
                            </p>
                        </div>

                        <form id="loginForm">

                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" required>
                            </div>

                            <button class="btn btn-navy w-100">
                                Login
                            </button>

                        </form>

                        <p class="text-center mt-3 text-muted">
                            Belum menjadi anggota?
                            <a href="#" onclick="showPage('pendaftaran')">
                                Daftar di sini
                            </a>
                        </p>

                    </div>

                </div>

            </div>

        </section>

    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

// SIDEBAR
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("show");
}

// NAVIGASI HALAMAN
function showPage(page) {

    document.querySelectorAll(".section-page").forEach(section => {
        section.classList.remove("active");
    });

    document.querySelectorAll(".menu-link[data-page]").forEach(link => {
        link.classList.remove("active");
    });

    document.getElementById(page).classList.add("active");

    document.querySelector(`[data-page="${page}"]`)
        ?.classList.add("active");

    document.getElementById("sidebar").classList.remove("show");

    window.scrollTo({top: 0, behavior: "smooth"});
}

document.querySelectorAll(".menu-link[data-page]").forEach(link => {
    link.addEventListener("click", function() {
        showPage(this.dataset.page);
    });
});

// FILTER ANGGOTA
function filterAnggota() {

    const nama = document.getElementById("filterNama").value.toLowerCase();
    const kerja = document.getElementById("filterKerja").value.toLowerCase();
    const provinsi = document.getElementById("filterProvinsi").value.toLowerCase();

    document.querySelectorAll("#tableAnggota tbody tr").forEach(row => {

        const text = row.innerText.toLowerCase();

        const cocokNama = text.includes(nama);
        const cocokKerja = text.includes(kerja);
        const cocokProvinsi = text.includes(provinsi);

        row.style.display =
            cocokNama && cocokKerja && cocokProvinsi ? "" : "none";

    });
}

function resetFilter() {

    document.getElementById("filterNama").value = "";
    document.getElementById("filterKerja").value = "";
    document.getElementById("filterProvinsi").value = "";

    filterAnggota();
}

// PREVIEW FOTO
document.getElementById("foto").addEventListener("change", function(e) {

    const file = e.target.files[0];

    if (!file) return;

    if (file.size > 500 * 1024) {
        alert("Ukuran foto maksimal 500 KB");
        e.target.value = "";
        return;
    }

    const reader = new FileReader();

    reader.onload = function(event) {
        document.getElementById("photoPreview").innerHTML =
            `<img src="${event.target.result}" alt="Foto anggota">`;
    };

    reader.readAsDataURL(file);

});


// LOGIN DEMO
document.getElementById("loginForm")
.addEventListener("submit", function(e) {
    e.preventDefault();
    alert("Fitur login belum terhubung dengan sistem autentikasi.");
});


async function muatDaftarAnggota() {
    const tbody = document.getElementById('daftarAnggota');

    try {
        const response = await fetch('api_daftar_anggota.php');

        if (!response.ok) {
            throw new Error('Gagal mengambil data anggota');
        }

        const data = await response.json();

        tbody.innerHTML = '';

        if (data.error) {
            throw new Error(data.error);
        }

        if (data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center">
                        Belum ada anggota aktif.
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach((anggota, index) => {
            const tr = document.createElement('tr');

            const values = [
                index + 1,
                anggota.nama_lengkap,
                anggota.intansi,
                anggota.kabupaten_kota,
                anggota.provinsi
            ];

            values.forEach(value => {
                const td = document.createElement('td');
                td.textContent = value || '-';
                tr.appendChild(td);
            });

            tbody.appendChild(tr);
        });

    } catch (error) {
        console.error(error);

        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-danger">
                    Data anggota gagal dimuat.
                </td>
            </tr>
        `;
    }
}

document.addEventListener('DOMContentLoaded', muatDaftarAnggota);


async function muatStatistikAnggota() {
    try {
        const response = await fetch('api_statistik_anggota.php');

        if (!response.ok) {
            throw new Error('Gagal mengambil statistik');
        }

        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        document.getElementById('totalAnggota').textContent =
            Number(data.total_anggota).toLocaleString('id-ID');

        document.getElementById('totalPendaftar').textContent =
            Number(data.total_pendaftar).toLocaleString('id-ID');

    } catch (error) {
        console.error('Statistik anggota:', error);
    }
}

document.addEventListener('DOMContentLoaded', muatStatistikAnggota);

</script>

</body>
</html>
