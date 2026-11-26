
<?php
require_once 'config.php';
wajibLogin();

$pesan = '';

/* PROSES TAMBAH, EDIT DAN HAPUS */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf();

    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'tambah' || $aksi === 'edit') {
        $nama = trim($_POST['nama'] ?? '');
        $kelas = trim($_POST['kelas'] ?? '');
        $hp = trim($_POST['no_hp'] ?? '');

        if ($nama === '' || $kelas === '') {
            $pesan = "Nama dan kelas wajib diisi!";
        } elseif ($aksi === 'tambah') {
            $stmt = $conn->prepare(
                "INSERT INTO anggota (nama, kelas, no_hp)
                 VALUES (?, ?, ?)"
            );

            $stmt->bind_param("sss", $nama, $kelas, $hp);

            if ($stmt->execute()) {
                header("Location: anggota.php?status=tambah");
                exit;
            }

            $pesan = "Gagal menambahkan anggota.";
        } else {
            $id = (int)($_POST['id'] ?? 0);

            $stmt = $conn->prepare(
                "UPDATE anggota
                 SET nama=?, kelas=?, no_hp=?
                 WHERE id=?"
            );

            $stmt->bind_param("sssi", $nama, $kelas, $hp, $id);

            if ($stmt->execute()) {
                header("Location: anggota.php?status=edit");
                exit;
            }

            $pesan = "Gagal mengedit anggota.";
        }
    }

    if ($aksi === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);

        $stmt = $conn->prepare(
            "DELETE FROM anggota WHERE id=?"
        );

        $stmt->bind_param("i", $id);

        try {
            if ($stmt->execute()) {
                header("Location: anggota.php?status=hapus");
                exit;
            }
        } catch (mysqli_sql_exception $ex) {
            $pesan = "Anggota memiliki riwayat pembayaran sehingga tidak dapat dihapus.";
        }
    }
}

/* AMBIL DATA UNTUK EDIT */
$editData = null;

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];

    $stmt = $conn->prepare(
        "SELECT * FROM anggota WHERE id=?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $editData = $stmt->get_result()->fetch_assoc();
}

/* PENCARIAN */
$cari = trim($_GET['cari'] ?? '');

$stmt = $conn->prepare(
    "SELECT * FROM anggota
     WHERE nama LIKE ? OR kelas LIKE ?
     ORDER BY id DESC"
);

$keyword = "%".$cari."%";
$stmt->bind_param("ss", $keyword, $keyword);
$stmt->execute();

$data = $stmt->get_result();

/* TOTAL ANGGOTA */
$total = $conn->query(
    "SELECT COUNT(*) AS total FROM anggota"
)->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Data Anggota - Uang Kas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link rel="stylesheet" href="style.css">

</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">

<!-- JUDUL -->
<div class="d-flex justify-content-between align-items-center mb-4">

<div>
<h2 class="fw-bold">Data Anggota</h2>
<p class="text-secondary">
Kelola data anggota kelas
</p>
</div>

<div class="badge bg-primary p-3">
<i class="bi bi-people"></i>
Total: <?= $total ?> Anggota
</div>

</div>

<!-- NOTIFIKASI -->
<?php if ($pesan): ?>
<div class="alert alert-warning">
<?= e($pesan) ?>
</div>
<?php endif; ?>

<?php if (isset($_GET['status'])): ?>
<div class="alert alert-success alert-dismissible fade show">
<?php
$status = $_GET['status'];

if ($status === 'tambah') {
    echo "Anggota berhasil ditambahkan!";
} elseif ($status === 'edit') {
    echo "Data anggota berhasil diperbarui!";
} elseif ($status === 'hapus') {
    echo "Anggota berhasil dihapus!";
}
?>
<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- FORM TAMBAH / EDIT -->
<div class="content-card mb-4">

<h5 class="fw-bold mb-3">

<i class="bi bi-person-plus text-primary"></i>

<?= $editData ? 'Edit Data Anggota' : 'Tambah Anggota Baru' ?>

</h5>

<form method="POST">

<input type="hidden" name="csrf"
value="<?= e(csrfToken()) ?>">

<input type="hidden" name="aksi"
value="<?= $editData ? 'edit' : 'tambah' ?>">

<?php if ($editData): ?>

<input type="hidden" name="id"
value="<?= (int)$editData['id'] ?>">

<?php endif; ?>

<div class="row g-3">

<div class="col-md-4">

<label class="form-label">Nama Anggota</label>

<input
type="text"
name="nama"
class="form-control"
placeholder="Masukkan nama anggota"
value="<?= e($editData['nama'] ?? '') ?>"
required>

</div>

<div class="col-md-4">

<label class="form-label">Kelas</label>

<input
type="text"
name="kelas"
class="form-control"
placeholder="Contoh: XII RPL 1"
value="<?= e($editData['kelas'] ?? '') ?>"
required>

</div>

<div class="col-md-4">

<label class="form-label">Nomor HP</label>

<input
type="text"
name="no_hp"
class="form-control"
placeholder="Masukkan nomor HP"
value="<?= e($editData['no_hp'] ?? '') ?>">

</div>

</div>

<div class="mt-3">

<button type="submit" class="btn btn-primary">

<i class="bi bi-save"></i>

<?= $editData ? 'Update Data' : 'Simpan Anggota' ?>

</button>

<?php if ($editData): ?>

<a href="anggota.php" class="btn btn-secondary">
Batal
</a>

<?php endif; ?>

</div>

</form>

</div>

<!-- TABEL ANGGOTA -->
<div class="content-card">

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">

<h5 class="fw-bold mb-0">
<i class="bi bi-table text-primary"></i>
Daftar Anggota
</h5>

<form method="GET" class="d-flex gap-2">

<input
type="text"
name="cari"
class="form-control"
placeholder="Cari nama atau kelas..."
value="<?= e($cari) ?>">

<button type="submit" class="btn btn-primary">
<i class="bi bi-search"></i> Cari
</button>

<a href="anggota.php" class="btn btn-secondary">
Reset
</a>

</form>

</div>

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead>

<tr>
<th>No</th>
<th>Nama Anggota</th>
<th>Kelas</th>
<th>Nomor HP</th>
<th>Aksi</th>
</tr>

</thead>

<tbody>

<?php if ($data->num_rows > 0): ?>

<?php $no = 1; ?>

<?php while ($row = $data->fetch_assoc()): ?>

<tr>

<td><?= $no++ ?></td>

<td>
<i class="bi bi-person-circle text-primary"></i>
<?= e($row['nama']) ?>
</td>

<td>
<span class="badge bg-info text-dark">
<?= e($row['kelas']) ?>
</span>
</td>

<td><?= e($row['no_hp']) ?></td>

<td>

<a
href="anggota.php?edit=<?= (int)$row['id'] ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil-square"></i>
Edit

</a>

<form
method="POST"
class="d-inline"
onsubmit="return confirm('Yakin ingin menghapus anggota ini?')">

<input
type="hidden"
name="csrf"
value="<?= e(csrfToken()) ?>">

<input
type="hidden"
name="aksi"
value="hapus">

<input
type="hidden"
name="id"
value="<?= (int)$row['id'] ?>">

<button type="submit" class="btn btn-danger btn-sm">

<i class="bi bi-trash"></i>
Hapus

</button>

</form>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="5" class="text-center text-secondary py-4">
<i class="bi bi-inbox fs-2 d-block"></i>
Data anggota belum tersedia.
</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
