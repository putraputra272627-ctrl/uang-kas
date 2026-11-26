
<?php
require_once 'config.php';
wajibLogin();

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf();

    $tanggal = $_POST['tanggal'];
    $keterangan = trim($_POST['keterangan']);
    $jumlah = (float)$_POST['jumlah'];

    if ($keterangan === '' || $jumlah <= 0) {
        $pesan = "Keterangan dan jumlah pengeluaran wajib diisi.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO pengeluaran
            (tanggal,keterangan,jumlah)
            VALUES (?,?,?)"
        );

        $stmt->bind_param("ssd", $tanggal, $keterangan, $jumlah);
        $stmt->execute();

        header("Location: pengeluaran.php");
        exit;
    }
}

$data = $conn->query(
    "SELECT * FROM pengeluaran ORDER BY tanggal DESC,id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pengeluaran Kas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">

<h2 class="fw-bold mb-4">Pengeluaran Uang Kas</h2>

<?php if ($pesan): ?>
<div class="alert alert-warning"><?= e($pesan) ?></div>
<?php endif; ?>

<div class="content-card mb-4">
<h5 class="fw-bold mb-3">Tambah Pengeluaran</h5>

<form method="POST">
<input type="hidden" name="csrf" value="<?= csrfToken() ?>">

<label class="form-label">Tanggal</label>
<input type="date" name="tanggal"
class="form-control mb-3"
value="<?= date('Y-m-d') ?>" required>

<label class="form-label">Keterangan</label>
<input type="text" name="keterangan"
class="form-control mb-3"
placeholder="Contoh: Pembelian alat tulis" required>

<label class="form-label">Jumlah Pengeluaran</label>
<input type="number" name="jumlah"
class="form-control mb-3" min="1" step="1" required>

<button class="btn btn-danger">
<i class="bi bi-save"></i> Simpan Pengeluaran
</button>
</form>
</div>

<div class="content-card">
<h5 class="fw-bold mb-3">Riwayat Pengeluaran</h5>

<div class="table-responsive">
<table class="table table-hover">
<thead>
<tr>
<th>No</th>
<th>Tanggal</th>
<th>Keterangan</th>
<th>Jumlah</th>
</tr>
</thead>
<tbody>

<?php $no=1; while ($row=$data->fetch_assoc()): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= e($row['tanggal']) ?></td>
<td><?= e($row['keterangan']) ?></td>
<td class="text-danger fw-bold"><?= rupiah($row['jumlah']) ?></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>
</div>

</div>
</body>
</html>
