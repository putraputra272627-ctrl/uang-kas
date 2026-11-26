
<?php
require_once 'config.php';
wajibLogin();

$pemasukan = $conn->query(
    "SELECT COALESCE(SUM(jumlah),0) total FROM pembayaran"
)->fetch_assoc()['total'];

$pengeluaran = $conn->query(
    "SELECT COALESCE(SUM(jumlah),0) total FROM pengeluaran"
)->fetch_assoc()['total'];

$saldo = $pemasukan - $pengeluaran;

$pembayaran = $conn->query(
    "SELECT p.*,a.nama,a.kelas
     FROM pembayaran p
     JOIN anggota a ON p.anggota_id=a.id
     ORDER BY p.tanggal DESC"
);

$dataPengeluaran = $conn->query(
    "SELECT * FROM pengeluaran ORDER BY tanggal DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Laporan Uang Kas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="style.css">

<style>
@media print {
    .sidebar, .no-print { display: none !important; }
    .main-content { margin: 0 !important; padding: 0 !important; }
    body { background: white !important; }
    .content-card { box-shadow: none !important; }
}
</style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">

<div class="d-flex justify-content-between align-items-center mb-4">
<h2 class="fw-bold">Laporan Keuangan</h2>
<button onclick="window.print()" class="btn btn-primary no-print">
<i class="bi bi-printer"></i> Cetak Laporan
</button>
</div>

<h4 class="text-center mb-1">LAPORAN UANG KAS</h4>
<p class="text-center text-secondary">Laporan Keuangan Kas Kelas</p>

<div class="row g-3 mb-4">

<div class="col-md-4">
<div class="card-custom bg-hijau">
<p>Total Pemasukan</p>
<h4><?= rupiah($pemasukan) ?></h4>
</div>
</div>

<div class="col-md-4">
<div class="card-custom bg-merah">
<p>Total Pengeluaran</p>
<h4><?= rupiah($pengeluaran) ?></h4>
</div>
</div>

<div class="col-md-4">
<div class="card-custom bg-ungu">
<p>Saldo Akhir</p>
<h4><?= rupiah($saldo) ?></h4>
</div>
</div>

</div>

<div class="content-card mb-4">
<h5 class="fw-bold">Laporan Pemasukan</h5>

<div class="table-responsive">
<table class="table table-bordered">
<thead>
<tr>
<th>No</th>
<th>Tanggal</th>
<th>Nama</th>
<th>Kelas</th>
<th>Keterangan</th>
<th>Jumlah</th>
</tr>
</thead>
<tbody>

<?php $no=1; while ($row=$pembayaran->fetch_assoc()): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= e($row['tanggal']) ?></td>
<td><?= e($row['nama']) ?></td>
<td><?= e($row['kelas']) ?></td>
<td><?= e($row['keterangan']) ?></td>
<td><?= rupiah($row['jumlah']) ?></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>
</div>

<div class="content-card mb-4">
<h5 class="fw-bold">Laporan Pengeluaran</h5>

<div class="table-responsive">
<table class="table table-bordered">
<thead>
<tr>
<th>No</th>
<th>Tanggal</th>
<th>Keterangan</th>
<th>Jumlah</th>
</tr>
</thead>
<tbody>

<?php $no=1; while ($row=$dataPengeluaran->fetch_assoc()): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= e($row['tanggal']) ?></td>
<td><?= e($row['keterangan']) ?></td>
<td><?= rupiah($row['jumlah']) ?></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>
</div>

<div class="text-end">
<h4>Saldo Akhir: <?= rupiah($saldo) ?></h4>
<br><br>
<p>Bendahara</p>
<br><br>
<p>______________________</p>
</div>

</div>
</body>
</html>
