<?php
error_reporting(-1);
header('Content-Type: text/html; charset=utf-8');
$root=__DIR__.DIRECTORY_SEPARATOR;
require $root.'prepare.php'; #«десь будут производитьс€ подготовительные действи€, объ€влени€ функций и т.д.
require $root.'auth.php'; #«десь будет происходить авторизаци€ пользовател€
require $root.'field_add.php'; #«десь будет происходить добавление кастомного пол€
?>