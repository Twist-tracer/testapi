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
		<header>
			<h1>Добавление сделок</h1>
		</header>
		<div id="contact_form">
			<form action="handler.php" method="post">
                <div class="field">
                    Добавить
                    <select name="number" id="number">
                        <option value="5">5</option>
                        <option value="100">50</option>
                        <option value="500">100</option>
                        <option value="1000">1000</option>
                    </select>
                    случайных сделок
                </div>
				<div>
					<button type="submit">Добавить</button>
				</div>
			</form>
		</div>
	</div>
</body>
</html>