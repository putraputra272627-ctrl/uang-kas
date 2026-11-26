
<?php
require_once __DIR__ . '/config.php';

wajibLogin();

$pemasukan = $conn->query(
    "SELECT COALESCE(SUM(jumlah),0) AS total FROM pembayaran"
)->fetch_assoc()['total'];

$pengeluaran = $conn->query(
    "SELECT COALESCE(SUM(jumlah),0) AS total FROM pengeluaran"
)->fetch_assoc()['total'];

$anggota = $conn->query(
    "SELECT COUNT(*) AS total FROM anggota"
)->fetch_assoc()['total'];

$saldo = $pemasukan - $pengeluaran;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard Uang Kas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>

<body>

<?php include __DIR__ . '/sidebar.php'; ?>

<div class="main-content">

<h2 class="fw-bold mb-4">
Selamat Datang, <?= e($_SESSION['user']['nama']) ?>!
</h2>

<div class="row g-3">

<div class="col-md-3">
<div class="card-custom bg-biru">
<p>Total Anggota</p>
<h3><?= $anggota ?></h3>
</div>
</div>

<div class="col-md-3">
<div class="card-custom bg-hijau">
<p>Total Pemasukan</p>
<h4><?= rupiah($pemasukan) ?></h4>
</div>
</div>

<div class="col-md-3">
<div class="card-custom bg-merah">
<p>Total Pengeluaran</p>
<h4><?= rupiah($pengeluaran) ?></h4>
</div>
</div>

<div class="col-md-3">
<div class="card-custom bg-ungu">
<p>Saldo Kas</p>
<h4><?= rupiah($saldo) ?></h4>
</div>
</div>

</div>

<div class="content-card mt-4">
<h5>Menu Aplikasi</h5>

<a href="anggota.php" class="btn btn-primary">Data Anggota</a>
<a href="pembayaran.php" class="btn btn-success">Pembayaran</a>
<a href="pengeluaran.php" class="btn btn-danger">Pengeluaran</a>
<a href="laporan.php" class="btn btn-dark">Laporan</a>
</div>

</div>

</body>
</html>
