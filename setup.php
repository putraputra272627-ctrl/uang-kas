
<?php
require_once __DIR__ . '/config.php';

// Cek apakah admin sudah ada
$cek = $conn->query(
    "SELECT id FROM users WHERE username='ayam'"
);

if ($cek->num_rows > 0) {
    die("Akun admin sudah tersedia!");
}

// Buat akun admin
$nama = "Administrator";
$username = "admin";
$password = password_hash("dida1234", PASSWORD_DEFAULT);
$role = "admin";

$stmt = $conn->prepare(
    "INSERT INTO users (nama, username, password, role)
     VALUES (?, ?, ?, ?)"
);

$stmt->bind_param(
    "ssss",
    $nama,
    $username,
    $password,
    $role
);

if ($stmt->execute()) {
    echo "Akun admin berhasil dibuat!";
    echo "<br>Username: ayam";
    echo "<br>Password: dida1234";
} else {
    echo "Gagal membuat akun admin: " . $conn->error;
}
?>
