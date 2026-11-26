
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "db_uang_kas"
);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

function e($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function rupiah($angka) {
    return "Rp " . number_format(
        (float)$angka,
        0,
        ',',
        '.'
    );
}

function wajibLogin() {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }
}

function wajibAdmin() {
    wajibLogin();

    if ($_SESSION['user']['role'] !== 'admin') {
        exit("Akses ditolak!");
    }
}

function csrfToken() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function cekCsrf() {
    if (
        !isset($_POST['csrf']) ||
        !hash_equals(csrfToken(), $_POST['csrf'])
    ) {
        exit("Token keamanan tidak valid!");
    }
}
