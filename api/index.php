<?php header('Content-type: text/html; charset="utf-8"'); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Работа с API</title>
	<link rel="stylesheet" type="text/css" href="css/main.css" media="all">
	<script type="text/javascript">
		function checkType(selectType) {
			if(Number(selectType.value) == 5) {
				var select_type_block = document.getElementById('select_type');
				var div = document.createElement('div');
				div.className = 'options';
				var input = '<input type="text" name="options[]">';
				div.innerHTML = "<label>Варианты:</label>" + input + '<br>' + input + '<br>' + input;
				select_type_block.appendChild(div);
			}
			else {
				var options = document.getElementsByClassName('options')[0];
				if(options != undefined)options.parentNode.removeChild(options);
			}
		}
	</script>
</head>
<body>
	<div id="wrapper">
		<div id="contact_form">
			<h2>Добавление сделок</h2>
			<form action="handler.php" method="post">
                <div class="field">
                    Добавить
                    <select name="number" id="number">
						<option value="1">1</option>
						<option value="5">5</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
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
			<form action="handler2.php" method="post">
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
				<div class="field" id="select_type">
					<label for="field_type">Тип поля</label>
					<select name="field_type" id="field_type" onchange="checkType(this)">
						<option value="1">Текст</option>
						<option value="2">Числовое</option>
						<option value="3">Чекбокс</option>
						<option value="4">Селект</option>
						<option value="5">Мультисписок</option>
					</select>
				</div>
				<div>
					<button type="submit" name="add_field">Добавить</button>
				</div>
			</form>
			<hr>
			<h2>Обновить значение полей у сделок</h2>
			<form action="handler3.php" method="post">
				<div>
					<button type="submit" name="refresh_fields">Обновить все</button>
				</div>
			</form>
		</div>
	</div>
</body>
</html>
