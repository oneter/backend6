<?php
session_start();
 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
 
$servername = "localhost";
$username = "u52988";
$password = "4622873";
$dbname = "u52988";
 
try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
 
$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;
 
// Îáðàáîòêà äàííûõ ôîðìû è îáíîâëåíèå èíôîðìàöèè î ïîëüçîâàòåëå â áàçå äàííûõ
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $year = $_POST['year'];
    $sex = $_POST['sex'];
    $legs = $_POST['legs'];
    $bio = $_POST['bio'];
    $agree = isset($_POST['agree']) ? 1 : 0;
 
    // Âàëèäàöèÿ äàííûõ
    if (!preg_match('/^[\p{L}\s]+$/u', $name)) {
    $errors[] = "Поле Имя содержит недопустимые символы. Используйте только буквы русского и английского алфавитов";
}
 
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@.*\.ru$/', $email)) {
        $errors[] = "Неверный формат e-mail";
    }
 
 
    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, year = ?, sex = ?, legs = ?, bio = ?, agree = ? WHERE id = ?");
        $stmt->execute([$name, $email, $year, $sex, $legs, $bio, $agree, $user_id]);
        $success = true;
    }
}
 
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Info</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="container">
 
       <?php if (!empty($errors)) {
    echo '<div class="error-container">';
    foreach ($errors as $error) {
        echo '<p class="error"> ' . $error . '</p>';
    }
    echo '</div>';
} ?>

 
        <?php if (empty($errors)) {
    echo '<div class="success-container">';
    echo '<p class="success"> Изменения сохранены </p>';
    echo '</div>';
} ?>
 
        <form action="userinfo.php" method="POST">
            <label for="name">Имя:</label>
            <input type="text" name="name" id="name" value="<?= $user['name'] ?>" required> <br/>
 
            <label for="email">Почта:</label>
            <input type="email" name="email" id="email" value="<?= $user['email'] ?>" required> <br/>
 
            <label for="birth_year">Год рождения:</label>
            <input type="number" name="year" id="year" value="<?= $user['year'] ?>" min="1923" max="2023" required> <br/>
 
            <label>Пол:</label>
            <label><input type="radio" name="sex" value="Мужской" <?= $user['sex'] == 'Мужской' ? 'checked' : '' ?> required> Мужской</label>
            <label><input type="radio" name="sex" value="Женский" <?= $user['sex'] == 'Женский' ? 'checked' : '' ?> required> Женский</label> <br/>
 
            <label>Кол-во конечностей:</label>
            <label><input type="radio" checked="checked" name="legs" id="legs" value="2" <?= $user['legs'] == '1' ? 'checked' : '' ?> required>1</label>
            <label><input type="radio" name="legs" id="legs" value="2" <?= $user['legs'] == '2' ? 'checked' : '' ?> required>2</label>
            <label><input type="radio" name="legs" id="legs" value="3" <?= $user['legs'] == '3' ? 'checked' : '' ?> required>3</label>
          <label><input type="radio" name="legs" id="legs" value="4" <?= $user['legs'] == '4' ? 'checked' : '' ?> required>4</label> 
          <label><input type="radio" name="legs" id="legs" value="5" <?= $user['legs'] == '5' ? 'checked' : '' ?> required>5</label> <br/>
 
            <label for="bio">Биография:</label>
            <textarea name="bio" id="bio" required><?= $user['bio'] ?></textarea> <br/>
            <label>
                С контрактом ознакомлен <input type="checkbox" name="agree" value="yes" <?= $user['agree'] == 'yes' ? 'checked' : '' ?> required ></label><br/>

 
            <input type="submit" value="Сохранить">
        </form>
        <p><a href="quitlog.php">Выход</a></p>
    </div>
</body>
</html>
