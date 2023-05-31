<?php


if (empty($_SERVER['PHP_AUTH_USER']) ||
    empty($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != 'admin' ||
    md5($_SERVER['PHP_AUTH_PW']) != md5('123')) {
  header('HTTP/1.1 401 Unanthorized');
  header('WWW-Authenticate: Basic realm="My site"');
  print('<h1>401 Òðåáóåòñÿ àâòîðèçàöèÿ</h1>');
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

if (isset($_POST['delete_btn'])) {
    $userIdToDelete = $_POST['delete_id'];
 
    try {
        $stmt = $db->prepare("DELETE FROM user_powers WHERE user_id = ?");
        $stmt->execute([$userIdToDelete]);

        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userIdToDelete]);

        header("Location: admin.php");
        exit;
    } catch (PDOException $e) {
        echo "Îøèáêà ïðè óäàëåíèè ïîëüçîâàòåëÿ: " . $e->getMessage();
    }
}
$sql = "SELECT * FROM users";
$result = $db->query($sql);
$users = $result->fetchAll(PDO::FETCH_ASSOC);

$powers_sql = "SELECT * FROM powers";
$powers_result = $db->query($powers_sql);
$powers = $powers_result->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    foreach ($users as $user) {
        $id = $user['id'];
        $name = $_POST['name'][$id];
        $email = $_POST['email'][$id];
        $year = $_POST['year'][$id];
        $sex = $_POST['sex'][$id];
        $legs = $_POST['legs'][$id];
        $bio = $_POST['bio'][$id];
        $agree = 1;

        $update_sql = "UPDATE users SET name = ?, email = ?, year = ?, sex = ?, legs = ?, bio = ?, agree = ? WHERE id = ?";
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->execute([$name, $email, $year, $sex, $legs, $bio, $agree, $id]);

        $delete_powers_sql = "DELETE FROM user_powers WHERE user_id = ?";
        $delete_powers_stmt = $db->prepare($delete_powers_sql);
        $delete_powers_stmt->execute([$id]);

        foreach ($powers as $power) {
            if (isset($_POST['powers'][$id]) && in_array($power['id'], $_POST['powers'][$id])) {
                $insert_powers_sql = "INSERT INTO user_powers (user_id, power_id) VALUES (?, ?)";
                $insert_powers_stmt = $db->prepare($insert_powers_sql);
                $insert_powers_stmt->execute([$id, $power['id']]);
            }
        }
    }
    header("Location: admin.php");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Административная страница</title>
<style>
table {
border-collapse: collapse;
width: 100%;
}
th, td {
border: 1px solid black;
padding: 8px;
text-align: left;
}
th {
background-color: #f2f2f2;
}
input[type="checkbox"] {
transform: scale(1.5);
}
.delete-button {
        background-color: red;
        color: white;
        border: none;
        padding: 5px 10px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
        cursor: pointer;
    }
</style>
</head>
<body>
    <h1>Администратор</h1>
<form action="admin.php" method="post">
    <table>
        <tr>
            <th>Имя</th>
            <th>Email</th>
            <th>Год рождения</th>
            <th>Пол</th>
            <th>Кол-во конечностей</th>
            <th>Биография</th>
            <th>Суперспособности</th>
            <th>Отчистить</th>
        </tr>
        <?php foreach ($users as $user) : ?>
            <?php
            $user_id = $user['id'];
            $user_powers_sql = "SELECT power_id FROM user_powers WHERE user_id = ?";
            $user_powers_stmt = $db->prepare($user_powers_sql);
            $user_powers_stmt->execute([$user_id]);
            $user_powers = $user_powers_stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            ?>
            <tr>
                <td><input type="text" name="name[<?= $user_id ?>]" value="<?= htmlspecialchars($user['name']) ?>"></td>
                <td><input type="text" name="email[<?= $user_id ?>]" value="<?= htmlspecialchars($user['email']) ?>"></td>
                <td><input type="number" name="year[<?= $user_id ?>]" value="<?= $user['year'] ?>" min="1900" max="2023"></td>
                <td>
                    <select name="sex[<?= $user_id ?>]">
                        <option value="Мужской" <?= $user['sex'] == 'Мужской' ? 'selected' : '' ?>>Мужской</option>
                        <option value="Женский" <?= $user['sex'] == 'Женский' ? 'selected' : '' ?>>Женский</option>
                    </select>
                </td>
                <td><input type="number" name="legss[<?= $user_id ?>]" value="<?= $user['legs'] ?>" min="1" max="5"></td>
                <td><textarea name="bio[<?= $user_id ?>]"><?= htmlspecialchars($user['bio']) ?></textarea></td>
            
                <td>
                    <?php foreach ($powers as $power) : ?>
                        <div>
                            <input type="checkbox" name="powers[<?= $user_id ?>][]" value="<?= $power['id'] ?>" <?= in_array($power['id'], $user_powers) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($power['power_name']) ?>
                        </div>
                    <?php endforeach; ?>
                </td>
                <td>
                <form method="POST">
                  <input type="hidden" name="delete_id" value="<?= $user['id'] ?>">
                  <button type="submit" name="delete_btn" class="delete-button">Отчистить</button>
                </td>
                </form>
            </tr>
        <?php endforeach; ?>
    </table>
    <button type="submit">Сохранить</button>
</form>

<h2>Статистика</h2>
<table border="1">
    <tr>
        <th>Суперспособности</th>
        <th>Номер пользователя</th>
    </tr>
    <?php
    $sql = "SELECT a.power_name, COUNT(ua.user_id) AS user_count
            FROM powers a
            JOIN user_powers ua ON a.id = ua.power_id
            GROUP BY a.id";
    $stmt = $db->query($sql);
    $powers_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
    foreach ($powers_stats as $power_stat) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($power_stat['power_name']) . "</td>";
        echo "<td>" . htmlspecialchars($power_stat['user_count']) . "</td>";
        echo "</tr>";
    }
    ?>
</table>

</body>
</html>
