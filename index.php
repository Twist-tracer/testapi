<?php header('Content-type: text/html; charset="utf-8"'); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Добавление сделок</title>
	<link rel="stylesheet" type="text/css" href="css/main.css" media="all">
</head>
<body>
	<div id="wrapper">
		<div id="contact_form">
			<h2>Добавление сделок</h2>
			<form action="handler.php" method="post" onSubmit="return checkForm(this)">
                <div class="field">
                    Добавить
                    <select name="number" id="number">
						<option value="1">1</option>
						<option value="5">5</option>
                        <option value="100">100</option>
                        <option value="250">250</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                    </select>
                    случайных сделок
                </div>
				<div>
					<button type="submit">Добавить</button>
				</div>
			</form>
			<hr>
			<h2>Добавление полей</h2>
			<form action="handler2.php" method="post" onSubmit="return checkForm(this)">
				<div class="field">
					<label for="field_name">Название поля</label>
					<input type="text" name="field_name" id="field_name">
				</div>
				<div class="field">
					<label for="element_type">Добавить поле в </label>
					<select name="element_type" id="element_type">
						<option value="1">Контакт</option>
						<option value="2">Сделка</option>
						<option value="3">Компания</option>
					</select>
				</div>
				<div class="field">
					<label for="field_type">Тип поля</label>
					<select name="field_type" id="field_type">
						<option value="1">Текст</option>
						<option value="2">Числовое</option>
						<option value="3">Чекбокс</option>
						<option value="4">Селект</option>
						<option value="5">Мультисписок</option>
					</select>
				</div>
				<div>
					<button type="submit">Добавить</button>
				</div>
			</form>
		</div>
	</div>
</body>
</html>