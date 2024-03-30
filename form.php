<!DOCTYPE html>
<html>
<head>
    <title>Валидация</title>
</head>
<body>
    <form name="myForm" action="validate.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
        Адрес почты: <input type="email" name="email" required><br>
        Пароль: <input type="password" name="password" required><br>
        Номер телефона: <input type="tel" id="phone" name="phone"><br>
        Дата рождения: <input type="date" name="birth_date" required><br>
        Пол: <input type="radio" name="gender" value="М" required>М
             <input type="radio" name="gender" value="Ж" required>Ж<br>
        Имя пользователя: <input type="text" name="username" required><br>
        Изображение: <input type="file" name="picture" required><br>
        <input type="submit" value="Отправить">
    </form>
</body>
</html>