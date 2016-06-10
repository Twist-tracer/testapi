<?php
#Массив с параметрами, которые нужно передать методом POST к API системы
$user = [
	'USER_LOGIN'=>'twist.tracer@gmail.com', #Ваш логин (электронная почта)
	'USER_HASH'=>'3152adcab5a33956a198f997412f4310' #Хэш для доступа к API (смотрите в профиле пользователя)
];
$subdomain='bogdanov'; #Наш аккаунт - поддомен

if(!auth($user, $subdomain)) {
    die('Авторизация не удалась');
}

/**
 * Возвращает TRUE в случае успешной авторизации, в противном случае FALSE
 * @param $user
 * @param $subdomain
 * @return bool
 */
function auth($user, $subdomain) {
	#Формируем ссылку для запроса
	$link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';

	$response = send_request($link, $user, 'CURLOPT_POST');

	return isset($response['auth']); #Флаг авторизации доступен в свойстве "auth"
}
