<?php
$max = 200; #По сколько добавлять
$limit_rows = 500; #По сколько получать

#Заполняем массивы сделок и контактов
if(isset($_POST['number'])) {
	$deals_data = []; #Массив со сделками

	for ($i = 0; $i < $_POST['number']; $i++) {
		$deals_data[$i]['name'] = "Сделка № " . ($i + 1);
	}

	$contacts_data = []; #Массив контактов
	$positions = ['Ген директор', 'Менеджер', 'Продавец', 'Управляющий', 'Оператор']; #Массив должностей

	for ($i = 0; $i < $_POST['number']; $i++) {
		$contacts_data[] = [
            'name' => "Контакт № " . ($i + 1),
            'position' =>  $positions[mt_rand(0, 4)],
            'phone' => mt_rand(8905, 8999) . mt_rand(100, 999) . mt_rand(10, 99) . mt_rand(10, 99),
            'email' => "contact" . ($i + 1) . "@mail.ru",
            'im' => "contact" . ($i + 1) . "@jabber.ru",
            'company' => "Компания № " . ($i + 1)
        ];
	}
}

function сheck_сurl_response($code) {
	$code = (int)$code;
	$errors = [
		301=>'Moved permanently',
		400=>'Bad request',
		401=>'Unauthorized',
		403=>'Forbidden',
		404=>'Not found',
		500=>'Internal server error',
		502=>'Bad gateway',
		503=>'Service unavailable'
	];
	try	{
		#Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
		if($code!=200 && $code!=204)
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
	} catch(Exception $E) {
		die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
	}
}

/**
 * Отправляет данные на сервер и возвращает ответ
 * @param $link
 * @param array $post_data
 * @param bool|false $type string (CURLOPT_POST | CURLOPT_CUSTOMREQUEST)
 * CURLOPT_CUSTOMREQUEST - POST
 * CURLOPT_POST - GET
 * @return mixed
 */
function send_request($link, $post_data = [], $type = FALSE) {
	$curl = curl_init(); #Сохраняем дескриптор сеанса cURL

	#Устанавливаем необходимые опции для сеанса cURL
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
	curl_setopt($curl, CURLOPT_URL,$link);
	if($type == 'CURLOPT_POST') {
		curl_setopt($curl, CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
	} elseif($type == 'CURLOPT_CUSTOMREQUEST') {
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	}
	curl_setopt($curl, CURLOPT_HEADER, FALSE);
	curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
	curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

	$out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
	curl_close($curl); #Заверашем сеанс cURL
	сheck_сurl_response($code); #Проверяем на ошибки

    sleep(1); #Ждем секунду

	$response = json_decode($out, TRUE);

	return $response['response'];
}
