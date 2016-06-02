<?php header('Content-type: text/html; charset="utf-8"'); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Добавление контакта</title>
	<link rel="stylesheet" type="text/css" href="css/main.css" media="all">
</head>
<body>
	<div id="wrapper">
		<header>
			<h1>Создание контакта</h1>
		</header>
		<div id="contact_form">
			<form action="handler.php" method="post">
				<div class="field">
					<label for="contact_name">Имя</label><input id="contact_name" type="text" name="name">
				</div>
				<div class="field">
					<label for="contact_company">Компания</label><input id="contact_company" type="text" name="company">
				</div>
				<div class="field">
					<label for="contact_position">Должность</label><input id="contact_position" type="text" name="position">
				</div>
				<div class="field">
					<label for="contact_phone">Телефон</label><input id="contact_phone" type="tel" name="phone">
				</div>
				<div class="field">
					<label for="contact_email">E-mail</label><input id="contact_email" type="email" name="email">
				</div>
				<div class="field">
					<label for="contact_jabber">Jabber</label><input id="contact_jabber" type="text" name="jabber">
				</div>
				<div>
					<button type="submit">Создать контакт</button>
					<button type="reset">Очистить форму</button>
				</div>
			</form>
		</div>
	</div>
</body>
</html>