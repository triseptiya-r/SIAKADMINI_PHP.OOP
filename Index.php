<?php
require_once 'app/Mahasiswa.php';
require_once 'app/Dosen.php';

session_start();

$isLoggedIn = false;
$userAktif = null;

// 1. Logika Login
if (isset($_POST['login'])) {
    $role = $_POST['role'];
    $_SESSION['role'] = $role;
    $_SESSION['nama_user'] = $_POST['nama'];
    $_SESSION['id_user'] = $_POST['id_nim'];
    
    header("Location: index.php"); 
    exit();
}

if (isset($_SESSION['nama_user'])) {
    $isLoggedIn = true;
    $role = $_SESSION['role'];
    if ($role == "Dosen") {
        $userAktif = new Dosen($_SESSION['nama_user'], $_SESSION['id_user']);
    } else {
        $userAktif = new Mahasiswa($_SESSION['nama_user'], $_SESSION['id_user']);
    }
}

// 2. Logika Dosen Input Nilai ke List Preview
if (isset($_POST['simpan_nilai_dosen'])) {
    $_SESSION['khs_temp'][$_POST['matkul']] = $_POST['nilai'];
    $_SESSION['mhs_terpilih'] = ['nama' => $_POST['m_nama'], 'nim' => $_POST['m_nim']];
}

// 3. Tombol Kunci & Kirim KHS (Data masuk ke khs_resmi)
if (isset($_POST['kunci_khs'])) {
    if (isset($_SESSION['khs_temp'])) {
        $_SESSION['khs_resmi'] = $_SESSION['khs_temp'];
        $_SESSION['mhs_resmi'] = $_SESSION['mhs_terpilih'];
        $_SESSION['dosen_pengampu'] = $_SESSION['nama_user']; 
        
        unset($_SESSION['khs_temp']); 
        echo "<script>alert('KHS Mahasiswa Berhasil Disimpan & Terbit!');</script>";
    }
}

// Logika Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['role']);
    unset($_SESSION['nama_user']);
    unset($_SESSION['id_user']);
// Jangan Hapus $_SESSION['khs_resmi'] supaya nilai tetap ada
    header("Location: index.php");
    exit();
}

// Daftar 8 Mata Kuliah Tetap
$daftar_matkul = [
    "Pemograman Berorientasi Objek", "Visualisasi Keputusan Bisnis",
    "Desain Grafis dan Multimedia", "Manajemen Pemasaran Digital",
    "Kewarganegaraan", "Bahasa Indonesia", 
    "Interpersonal Skill", "Intermediate English"
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SISTEM AKADEMIK</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/Style.css">
</head>
<body style="background-color: #fefae0;">

<?php if (!$isLoggedIn): ?>
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card card-siakad shadow-lg text-center" style="max-width: 400px; width: 100%;">
        <img src="img/Logo_Polije_Blu.png" alt="Logo" style="height: 100px; margin-bottom: 20px;">
        <h4 class="fw-bold mb-4">SIAKAD LOGIN</h4>
        <form method="POST">
            <div class="mb-3 text-start">
                <label class="form-label">Masuk Sebagai</label>
                <select name="role" class="form-select">
                    <option value="Mahasiswa">Mahasiswa</option>
                    <option value="Dosen">Dosen</option>
                </select>
            </div>
            <div class="mb-3 text-start"><label class="form-label">Nama Lengkap</label><input type="text" name="nama" class="form-control" required></div>
            <div class="mb-4 text-start"><label class="form-label">NIM / NIDN</label><input type="text" name="id_nim" class="form-control" required></div>
            <button type="submit" name="login" class="btn-proses w-100">MASUK</button>
        </form>
    </div>
</div>

<?php else: ?>
<div class="navbar-custom no-print shadow-sm">
    <img src="img/POLIJE_LOGO.png" alt="Logo" class="logo-header">
    <div class="text-start">
        <h2 class="fw-bold m-0" style="color: #3e2723;">SIAKAD DASHBOARD</h2>
        <p class="m-0 text-muted">Selamat Datang, <strong><?= $_SESSION['nama_user'] ?></strong></p>
    </div>
    <div class="ms-auto pe-4 no-print"><a href="?logout=true" class="btn btn-sm btn-danger rounded-pill px-3">Logout</a></div>
</div>

<div class="container mt-5 mb-5">
    <?php if ($_SESSION['role'] == "Dosen"): ?>
        <div class="row">
            <div class="col-md-5 no-print">
                <div class="card card-siakad shadow mb-4">
                    <h5 class="mb-4">Input Nilai Mahasiswa</h5>
                    <form method="POST">
                        <div class="mb-3 text-start"><label class="form-label">Nama Mahasiswa</label><input type="text" name="m_nama" class="form-control" value="<?= $_SESSION['mhs_terpilih']['nama'] ?? '' ?>" required></div>
                        <div class="mb-3 text-start"><label class="form-label">NIM Mahasiswa</label><input type="text" name="m_nim" class="form-control" value="<?= $_SESSION['mhs_terpilih']['nim'] ?? '' ?>" required></div>
                        <div class="mb-3">
                            <label class="form-label">Mata Kuliah</label>
                            <select name="matkul" class="form-select">
                                <?php foreach($daftar_matkul as $m) echo "<option value='$m'>$m</option>"; ?>
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Nilai</label><input type="number" name="nilai" class="form-control" required></div>
                        <button type="submit" name="simpan_nilai_dosen" class="btn-proses w-100">INPUT NILAI</button>
                    </form>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card card-siakad shadow h-100 p-4">
                    <h5>Preview & Kirim KHS</h5>
                    <?php if(isset($_SESSION['khs_temp'])): ?>
                        <div class="p-3 mt-3 rounded shadow-sm" style="background: #ffffff; border: 1px solid #d4a373;">
                            <p class="mb-1"><strong>Target:</strong> <?= $_SESSION['mhs_terpilih']['nama'] ?> (<?= $_SESSION['mhs_terpilih']['nim'] ?>)</p>
                            <table class="table table-sm mt-2">
                                <thead class="table-light"><tr><th>Mata Kuliah</th><th>Nilai</th></tr></thead>
                                <tbody>
                                    <?php foreach($_SESSION['khs_temp'] as $mk => $nl): ?>
                                        <tr><td><?= $mk ?></td><td><?= $nl ?></td></tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <form method="POST" class="mt-3">
                                <button type="submit" name="kunci_khs" class="btn-proses w-100">SIMPAN & TERBITKAN KHS</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mt-5 text-center">Belum ada antrean nilai yang siap diterbitkan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card card-siakad shadow p-4">
                    <?php if(isset($_SESSION['khs_resmi'])): ?>
                        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                            <h5 class="m-0 fw-bold text-success">✓ KHS Anda Telah Terbit</h5>
                            <button onclick="window.print()" class="btn-proses" style="width: auto;">🖨️ Unduh / Cetak KHS</button>
                        </div>

                        <div class="p-5 border border-dark bg-white">
                            <div class="text-center mb-4">
                                <h4 class="fw-bold m-0">KARTU HASIL STUDI (KHS)</h4>
                                <p class="m-0">D4 Bisnis Digital - Politeknik Negeri Jember</p>
                                <hr class="border-dark opacity-100">
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    Nama: <strong><?= $_SESSION['nama_user'] ?></strong><br>
                                    NIM: <?= $_SESSION['id_user'] ?>
                                </div>
                                <div class="col-6 text-end">
                                    Dosen Wali: <?= $_SESSION['dosen_pengampu'] ?? '-' ?><br>
                                    TA: 2025/2026
                                </div>
                            </div>
                            <table class="table table-bordered border-dark text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th class="text-start">Mata Kuliah</th>
                                        <th>Nilai</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total = 0; $cnt = 0;
                                    foreach($daftar_matkul as $i => $mk): 
                                        $val = $_SESSION['khs_resmi'][$mk] ?? 0;
                                        $total += $val; $cnt++;
                                    ?>
                                    <tr>
                                        <td><?= $i+1 ?></td>
                                        <td class="text-start"><?= $mk ?></td>
                                        <td><?= $val ?></td>
                                        <td class="fw-bold <?= $val >= 60 ? 'text-success' : 'text-danger' ?>">
                                            <?= $val >= 60 ? 'LULUS' : 'REMIDI' ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tr class="fw-bold table-light">
                                    <td colspan="2">INDEKS PRESTASI KUMULATIF (IPK)</td>
                                    <td colspan="2">
                                        <?= number_format(($total / $cnt / 100) * 4, 2) ?>
                                    </td>
                                </tr>
                            </table>

                            <div class="mt-5 d-flex justify-content-between text-center">
                                <div style="width: 180px;"><p class="mb-5">Mahasiswa,</p><br><strong>( <?= $_SESSION['nama_user'] ?> )</strong></div>
                                <div style="width: 180px;"><p class="mb-5">Dosen Wali,</p><br><strong>( <?= $_SESSION['dosen_pengampu'] ?> )</strong></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <h4 class="text-muted fw-bold">KHS BELUM TERBIT</h4>
                            <p class="text-secondary">Mohon tunggu dosen pengampu mengunci nilai akhir Anda untuk semester ini.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

</body>
</html>