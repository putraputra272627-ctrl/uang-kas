
<?php
require_once 'config.php';
wajibLogin();

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf();

    $anggota_id = (int)$_POST['anggota_id'];
    $tanggal = $_POST['tanggal'];
    $jumlah = (float)$_POST['jumlah'];
    $keterangan = trim($_POST['keterangan']);

    if ($anggota_id <= 0 || $jumlah <= 0) {
        $pesan = "Anggota dan jumlah pembayaran harus benar.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM anggota WHERE id=?");
        $stmt->bind_param("i", $anggota_id);
        $stmt->execute();

        if (!$stmt->get_result()->fetch_assoc()) {
            $pesan = "Anggota tidak ditemukan.";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO pembayaran
                (anggota_id,tanggal,jumlah,keterangan)
                VALUES (?,?,?,?)"
            );

            $stmt->bind_param(
                "isds",
                $anggota_id,
                $tanggal,
                $jumlah,
                $keterangan
            );

            $stmt->execute();
            header("Location: pembayaran.php");
            exit;
        }
    }
}

$anggota = $conn->query("SELECT * FROM anggota ORDER BY nama");

$data = $conn->query(
    "SELECT p.*,a.nama,a.kelas
     FROM pembayaran p
     JOIN anggota a ON p.anggota_id=a.id
     ORDER BY p.tanggal DESC,p.id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pembayaran Kas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">

<h2 class="fw-bold mb-4">Pembayaran Uang Kas</h2>

<?php if ($pesan): ?>
<div class="alert alert-warning"><?= e($pesan) ?></div>
<?php endif; ?>

<div class="content-card mb-4">
<h5 class="fw-bold mb-3">Input Pembayaran</h5>

<form method="POST">
<input type="hidden" name="csrf" value="<?= csrfToken() ?>">

<div class="row g-3">

<div class="col-md-6">
<label class="form-label">Nama Anggota</label>
<select name="anggota_id" class="form-select" required>
<option value="">Pilih Anggota</option>
<?php while ($a=$anggota->fetch_assoc()): ?>
<option value="<?= $a['id'] ?>">
<?= e($a['nama']) ?> - <?= e($a['kelas']) ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-6">
<label class="form-label">Tanggal</label>
<input type="date" name="tanggal"
class="form-control" value="<?= date('Y-m-d') ?>" required>
</div>

<div class="col-md-6">
<label class="form-label">Jumlah Pembayaran</label>
<input type="number" name="jumlah"
class="form-control" min="1" step="1" required>
</div>

<div class="col-md-6">
<label class="form-label">Keterangan</label>
<input type="text" name="keterangan"
class="form-control" placeholder="Contoh: Kas bulan November">
</div>

</div>

<button class="btn btn-success mt-3">
<i class="bi bi-save"></i> Simpan Pembayaran
</button>
</form>
</div>

<div class="content-card">
<h5 class="fw-bold mb-3">Riwayat Pembayaran</h5>

<div class="table-responsive">
<table class="table table-hover">
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

<?php $no=1; while ($row=$data->fetch_assoc()): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= e($row['tanggal']) ?></td>
<td><?= e($row['nama']) ?></td>
<td><?= e($row['kelas']) ?></td>
<td><?= e($row['keterangan']) ?></td>
<td class="text-success fw-bold"><?= rupiah($row['jumlah']) ?></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>
</div>

</div>
</body>
</html>
