<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'forma.php';

if (!isset($_SESSION['login']) || !isset($_SESSION['password'])) {
    header("Location: index.php");
    exit();
}

$login = $_SESSION['login'];
$password = $_SESSION['password'];
$user_id = $_SESSION['user_id'];

try {

    unset($_SESSION['login']);
    unset($_SESSION['password']);
} catch (PDOException $e) {
    print('Error : ' . $e->getMessage());
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="container">
        <h1>Добро пожаловать</h1>
        <p>Login: <?php echo htmlspecialchars($login); ?></p>
        <p>Password: <?php echo htmlspecialchars($password); ?></p>
        <p><a href="index.php"><=  Назад</a></p>
    </div>
</body>
</html>
