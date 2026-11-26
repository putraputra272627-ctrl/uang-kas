
<div class="sidebar">
    <h3><i class="bi bi-wallet2"></i> KAS KELAS</h3>

    <div class="user-info">
        <i class="bi bi-person-circle"></i>
        <?= e($_SESSION['user']['nama']) ?>
        <small><?= e($_SESSION['user']['role']) ?></small>
    </div>

    <a href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="anggota.php"><i class="bi bi-people"></i> Data Anggota</a>
    <a href="pembayaran.php"><i class="bi bi-cash-stack"></i> Pembayaran</a>
    <a href="pengeluaran.php"><i class="bi bi-receipt"></i> Pengeluaran</a>
    <a href="laporan.php"><i class="bi bi-file-earmark-text"></i> Laporan</a>

    <hr>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>
