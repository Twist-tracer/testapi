<?php
require $root.'prepare.php'; #Здесь будут производиться подготовительные действия, объявления функций и т.д.
require $root.'auth.php'; #Здесь будет происходить авторизация пользователя
require $root.'account_current.php'; #Здесь мы будем получать информацию об аккаунте

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Получение списка контактов</title>

    <link rel="stylesheet" type="text/css" href="css/main.css" media="all">
</head>
<body>
<div id="wrapper">
    <form action="" method="post">
        <input type="submit" value="Получтиь список сделок">
    </form>
</div>
</body>
</html>