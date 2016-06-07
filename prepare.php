<?php
$max = 200; #По сколько добавлять
$limit_rows = 500; #По сколько по сколько обновлять
$row_count = empty($_POST['row_count']) ? 500 : $_POST['row_count']; #Сколько сделок обновить

function CheckCurlResponse($code) {
	$code=(int)$code;
	$errors=array(
		301=>'Moved permanently',
		400=>'Bad request',
		401=>'Unauthorized',
		403=>'Forbidden',
		404=>'Not found',
		500=>'Internal server error',
		502=>'Bad gateway',
		503=>'Service unavailable'
	);
	try
	{
		#Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
		if($code!=200 && $code!=204)
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
	}
	catch(Exception $E)
	{
		die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
	}
}

if(isset($_POST['number'])) {

	$deals_data = array(); // Массив со сделками
	// Заполняем
	for ($i = 0; $i < $_POST['number']; $i++) {
		$deals_data[$i]['name'] = "Сделка № " . ($i + 1);
	}

	$contacts_data = array(); // Массив контактов
	$positions = array('Ген директор', 'Менеджер', 'Продавец', 'Управляющий', 'Оператор'); // Массив должностей
	// Заполняем
	for ($i = 0; $i < $_POST['number']; $i++) {
		$contacts_data[$i]['name'] = "Контакт № " . ($i + 1);
		$contacts_data[$i]['position'] = $positions[mt_rand(0, 4)];
		$contacts_data[$i]['phone'] = mt_rand(8905, 8999) . mt_rand(100, 999) . mt_rand(10, 99) . mt_rand(10, 99);
		$contacts_data[$i]['email'] = "contact" . ($i + 1) . "@mail.ru";
		$contacts_data[$i]['im'] = "contact" . ($i + 1) . "@jabber.ru";
		$contacts_data[$i]['company'] = "Компания № " . ($i + 1);
	}
}
