
<?php
require_once 'config.php';

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf();

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT * FROM users WHERE username = ?"
    );

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'nama' => $user['nama'],
            'role' => $user['role']
        ];

        header("Location: index.php");
        exit;
    }

    $error = "Username atau password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Uang Kas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body class="login-bg">

<div class="container">
<div class="row justify-content-center align-items-center min-vh-100">
<div class="col-md-5 col-lg-4">

<div class="card login-card border-0 shadow-lg">
<div class="card-body p-4">

<div class="text-center mb-4">
<div class="logo-login">💰</div>
<h3 class="fw-bold">UANG KAS</h3>
<p class="text-secondary">Sistem Informasi Keuangan Kelas</p>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<form method="POST">
<input type="hidden" name="csrf" value="<?= csrfToken() ?>">

<label class="form-label">Username</label>
<input type="text" name="username" class="form-control mb-3" required>

<label class="form-label">Password</label>
<input type="password" name="password" class="form-control mb-4" required>

<button type="submit" class="btn btn-primary w-100">
Masuk ke Aplikasi
</button>
</form>

</div>
</div>

</div>
</div>
</div>

</body>
</html>
