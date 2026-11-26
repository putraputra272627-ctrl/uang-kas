<?php 

    require_once "config.php";

    if(isset($_POST["simpan"])) {
        register($conn);
    }

    function register($conn) {
        $name = $_POST["name"];
        $username = $_POST["username"];
        $password = $_POST["password"];

        $hasPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, username, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $username, $hasPassword]);
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="post">
        <input type="text" name="name">
        <br>
        <input type="text" name="username">
        <br>
        <input type="password" name="password">
        <br>
        <button type="submit" name="simpan">simpan</button>
    </form>
</body>
</html>