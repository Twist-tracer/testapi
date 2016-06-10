<?php
$start_time = microtime(TRUE);

set_time_limit(0);
error_reporting(-1);
header('Content-Type: text/html; charset=utf-8');
$root=__DIR__.DIRECTORY_SEPARATOR;
require $root.'prepare.php'; #Здесь будут производиться подготовительные действия, объявления функций и т.д.
require $root.'auth.php'; #Здесь будет происходить авторизация пользователя
require $root.'deals_add.php'; #Здесь будет происходить добавление сделок и получение их идентификаторов
require $root.'account_current.php'; #Здесь мы будем получать информацию об аккаунте
require $root.'contacts_add.php'; #Здесь будет происходить добавление контакта
