<?php
$max = 200; #По сколько добавлять
$limit_rows = 500; #По сколько обновлять
$row_count = empty($_POST['row_count']) ? 500 : $_POST['row_count']; #Сколько сделок обновить

#Заполняем массивы сделок и контактов
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

function сheck_сurl_response($code) {
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

/**
 * Отправляет данные на сервер и возвращает ответ
 * @param $link
 * @param array $postData
 * @param bool|false $type string (CURLOPT_POST | CURLOPT_CUSTOMREQUEST)
 * @return mixed
 */
function send_request($link, $postData = array(), $type = FALSE) {
	$curl=curl_init(); #Сохраняем дескриптор сеанса cURL

	#Устанавливаем необходимые опции для сеанса cURL
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);
	curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
	curl_setopt($curl,CURLOPT_URL,$link);
	if($type == 'CURLOPT_POST') {
		curl_setopt($curl,CURLOPT_POST,TRUE);
		curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($postData));
	}elseif($type == 'CURLOPT_CUSTOMREQUEST') {
		curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
		curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($postData));
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
	}
	curl_setopt($curl,CURLOPT_HEADER,FALSE);
	curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
	curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

	$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
	$code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
	curl_close($curl); #Заверашем сеанс cURL
	сheck_сurl_response($code);

	$Response=json_decode($out,FALSE);
	return $Response['response'];
}
