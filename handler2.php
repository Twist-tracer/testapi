<?php
error_reporting(-1);
header('Content-Type: text/html; charset=utf-8');
$root=__DIR__.DIRECTORY_SEPARATOR;
require $root.'prepare.php'; #����� ����� ������������� ���������������� ��������, ���������� ������� � �.�.
require $root.'auth.php'; #����� ����� ����������� ����������� ������������
require $root.'field_add.php'; #����� ����� ����������� ���������� ���������� ����
?>