<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "practice_7";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = $_POST['password'];
$phone = $_POST['phone'];
$birth_date = $_POST['birth_date'];
$gender = $_POST['gender'];
$username = $_POST['username'];
$picture = $_FILES['picture']['name'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Неверный формат электронной почты!";
    exit();
}

if (strlen($email) >= 30) {
    echo "Слишком много символов у почты";
    exit();
}

if (strlen($email) <= 6) {
    echo "Слишком мало символов у почты";
    exit();
}

$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "Этот адрес электронной почты уже занят!";
    exit();
}

if (!preg_match("/.{8,}/", $password)) {
    echo "Пароль должен содержать не менее 8 символов!";
    exit();
}

if (preg_match("/(.)\\1{2,}/", $password)) {
    echo "Пароль не должен иметь повторяющихся символов!";
    exit();
}

$length = strlen($password);
for ($i = 0; $i < $length; $i++) {
    for ($j = 2; $j <= ($length - $i) / 2; $j++) {
        if (substr($password, $i, $j) == substr($password, $i + $j, $j)) {
            echo "Пароль не должен иметь повторяющихся комбинаций!";
            exit();
        }
    }
}

if (!preg_match("/" . strtolower(date('F')) . "/", strtolower($password))) {
    echo "Пароль должен содержать текущий месяц на английском языке!";
    exit();
}

if (!preg_match("/.*[!@#$%^&*()\-_=+{};:,<.>].*[!@#$%^&*()\-_=+{};:,<.>].*/", $password)) {
    echo "Пароль должен содержать минимум 2 специальных символа!";
    exit();
}

if (preg_match("/([!@#$%^&*()\-_=+{};:,<.>]){2,}/", $password)) {
   echo "Пароль не должен содержать два специальных символа, идущих подряд!";
   exit();
}

if (count(array_unique(str_split($password))) < strlen($password)) {
    echo "Пароль не должен содержать повторяющихся символов!";
    exit();
}

if (!preg_match("/[А-Яа-яЁё]/", $password) || !preg_match("/[A-Za-z]/", $password)) {
    echo "Пароль должен содержать символы как русского, так и английского алфавита!";
    exit();
}

if (!preg_match("/^\+[0-9]{1,3}[0-9]{4,14}(?:x.+)?$/", $phone)) {
    echo "Неправильный номер телефона!";
    exit();
}

if (strlen($phone) >= 13) {
    echo "Слишком много символов у телефона";
    exit();
}

if (strlen($phone) <= 11) {
    echo "Слишком мало символов у телефона";
    exit();
}

$sql = "SELECT * FROM users WHERE phone = '$phone'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "Этот номер телефона уже занят!";
    exit();
}

$birth_date = date('Y-m-d', strtotime($birth_date));
if ($birth_date > date('Y-m-d') || $birth_date < date('Y-m-d', strtotime('-111 years'))) {
    echo "Неверная дата рождения!";
    exit();
}

if (!in_array($gender, ['М', 'Ж'])) {
    echo "Недействительный пол!";
    exit();
}

$age = date_diff(date_create($birth_date), date_create('today'))->y;
if ($age < 18) {
    $gender = $gender == 'М' ? 'Мальчик' : 'Девочка';
} else {
    $gender = $gender == 'М' ? 'Мужчина' : 'Женщина';
}

if (empty($username) || preg_match("/\d/", $username)) {
    echo "Имя пользователя не должно быть пустым и не должно содержать чисел!";
    exit();
}

$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "Имя пользователя уже занято!";
    exit();
}

$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
$extension = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
if (!in_array($extension, $allowed_extensions)) {
    echo "Неверное расширение файла!";
    exit();
}

$file_size = $_FILES['picture']['size'];
if ($file_size > 5000000 || $file_size < 50000) {
    echo "Неверный размер файла!";
    exit();
}

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["picture"]["name"]);

$check = getimagesize($_FILES["picture"]["tmp_name"]);
if($check === false) {
    echo "Файл не является изображением!";
    exit();
}

if (!move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file)) {
    echo "Ошибка загрузки файла на сервер!";
    exit();
}

$picture = $target_file; 

$sql = "INSERT INTO users (email, password, phone, birth_date, gender, username, picture)
VALUES ('$email', '$password', '$phone', '$birth_date', '$gender', '$username', '$picture')";

if ($conn->query($sql) === TRUE) {
    echo "Новая запись успешно создана!";
} else {
    echo "Ошибка: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
