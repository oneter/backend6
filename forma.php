<?php
session_start();

function showError($field)
{
    if (isset($_SESSION['errors'][$field])) {
        return '<span class="error">' . $_SESSION['errors'][$field] . '</span>';
    }
    return '';
}

function getSelected($fieldName, $value)
{
    if (isset($_COOKIE[$fieldName]) && in_array($value, explode(',', $_COOKIE[$fieldName]))) {
        return 'selected';
    }
    return '';
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Получаем выбранные значения и записываем их в куки-файл
  if (isset($_POST["powers"])) {
    $selectedA = implode(',', $_POST["powers"]);
    setcookie('powers', $selectedA, time() + 3600, '/');
  }
}
if(isset($_POST['year'])) {
  $selectedYear = $_POST['year'];
  setcookie('year', $selectedYear, time() + (86400 * 30), "/");
}

function getChecked($fieldName, $value)
{
    if (isset($_COOKIE[$fieldName]) && $_COOKIE[$fieldName] == $value) {
        return 'checked';
    }
    return '';
}

  if (isset($_POST["sex"])) {
    $value = $_POST["sex"];
    setcookie('sex', $value, time() + 3600, '/');
  }
if (isset($_POST["legs"])) {
    $value = $_POST["legs"];
    setcookie('legs', $value, time() + 3600, '/');
  }

function getFieldValue($fieldName)
{
    if (isset($_SESSION['errors']) && !empty($_SESSION['errors']) && isset($_SESSION['data'][$fieldName])) {
        return htmlspecialchars($_SESSION['data'][$fieldName]);
    } elseif (isset($_COOKIE[$fieldName])) {
        return htmlspecialchars($_COOKIE[$fieldName]);
    }
    return '';
}

// Настройки подключения к базе данных
$servername = "localhost";
$username = "u52988";
$password = "4622873";
$dbname = "u52988";

// Создание подключения
try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получение данных из формы
    $name = $_POST["name"];
    $email = $_POST["email"];
    $year = $_POST["year"];
    $sex = $_POST["sex"];
    $legs = $_POST["legs"];
    $powers = $_POST["powers"];
    $bio = $_POST["bio"];
    $agree = $_POST["agree"] == "yes";

    // Валидация данных
    $errors = [];
    $_SESSION['errors'] = [];

    // Валидация полей (проверка на пустоту и корректность)
    if (empty($name)) {
    $errors[] = "Пожалуйста заполните поле Имя";
'<style>
    input[id="name"]{
    border:10px solid red;
    }
    </style>';
}

if (empty($email)) {
    $errors[] = "Пожалуйста заполните поле E-mail";
}

if (empty($year)) {
    $errors[] = "Пожалуйста заполните поле Год рождения";
}

if (!empty($name) && !preg_match("/^[a-zA-Zа-яА-ЯёЁ\s]+$/u", $name)) {
    $errors[] = "Поле Имя содержит недопустимые символы. Используйте только буквы русского и английского алфавитов";
}
 
if (!empty($email) && (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("/.*@.*\.ru$/", $email))) {
    $errors[] = "Неверный формат e-mail";
}

if (!empty($year) && (!preg_match("/^(19|20)\d{2}$/", $year) || intval($year) > 2023)) {
    $errors[] = "Год рождения должен быть в диапазоне от 1900 до 2023";
}
if(!$agree){
  $errors[] = "Пожалуйста подтвердите ознакомление с соглашением";
}

$_SESSION['data'] = [
    'name' => $name,
    'email' => $email,
    'year' => $year,
    'sex' => $sex,
    'legs' => $legs,
    'powers' => $powers,
    'bio' => $bio,
    'agree'=>$agree
];
    // Сохранение данных, если нет ошибок
    if (empty($errors)) {
        unset($_SESSION['errors']);
 try {
            $stmt = $db->prepare("INSERT INTO users (name, email, year, sex, legs, bio, agree) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $year, $sex, $legs, $bio, $agree]);
 
            $user_id = $db->lastInsertId();
            
            $login = 's' . sprintf('%07d', mt_rand(0, 9999999));
            $password = bin2hex(random_bytes(8));
            $hased_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $db->prepare("INSERT INTO user_auth (user_id, login, password) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $login, $hased_password]);
 
            $stmt = $db->prepare("SELECT id FROM powers WHERE power_name = ?");
            foreach ($powers as $power) {
            $stmt->execute([$power]);
            $power_id = $stmt->fetchColumn();
 
            $stmt2 = $db->prepare("INSERT INTO user_powers (user_id, power_id) VALUES (?, ?)");
            $stmt2->execute([$user_id, $power_id]);
            }
            
            $_SESSION['login'] = $login;
            $_SESSION['password'] = $password;
            $_SESSION['user_id'] = $user_id;

            $cookie_expires = time() + 60 * 60 * 24 * 365;
            setcookie('name', $name, $cookie_expires);
            setcookie('email', $email, $cookie_expires);
            setcookie('year', $year, $cookie_expires);
            setcookie('sex', $sex, $cookie_expires);
            setcookie('legs', $legs, $cookie_expires);
            setcookie('powers', implode(',', $powers), $cookie_expires);
            setcookie('bio', $bio, $cookie_expires);
            setcookie('agree', $agree, $cookie_expires);
            unset($_SESSION['data']);

            header("Location: userdata.php");
            exit();
        } catch (PDOException $e) {
            print('Error : ' . $e->getMessage());
            exit();
        }
    } else {
        foreach ($errors as $field => $error) {
            $_SESSION['errors'][$field] = $error;
        }
        header("Location: index.php");
        exit();
    }
}
