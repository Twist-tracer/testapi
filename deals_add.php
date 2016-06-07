<?php
$dealsID = array();
if(count($deals_data) > $max) {
    for($i = 0; $i < count($deals_data); $i++) {
        if((($i + 1) % $max) == 0) {
            $set['request']['leads']['add'][] = $deals_data[$i];

            # На каждой $max итерации делаем запрос на добавление сделок, чистим $set
            $dealsID = array_merge($dealsID, sendSetListRequest($subdomain, $set));
            unset($set);
        }
        else $set['request']['leads']['add'][] = $deals_data[$i];
    }
} else {
    foreach($deals_data as $deal_data) {
        $set['request']['leads']['add'][] = $deal_data;
    }
    $dealsID = sendSetListRequest($subdomain, $set);
}

function sendSetListRequest($subdomain, $set) {
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/set';
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($set));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    CheckCurlResponse($code);

    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */
    $Response=json_decode($out,true);
    // Массив с id добавленных сделок
    $dealsID=$Response['response']['leads']['add'];

    return $dealsID;
}
