<?php
if($row_count > $limit_rows) {
    $count = $row_count / $limit_rows;

    for($i = 0; $i < ceil($count); $i++) {
        $dealsList = getDealsList($subdomain, $limit_rows);

        foreach($dealsList as $deal) {
            $set['request']['leads']['update'][] = array(
                'id' => $deal['id'],
                'last_modified' => time(),
                'custom_fields'=> getCustomFields($dealsFields)
            );
        }

        sendSetFieldsRequest($subdomain, $set);
    }

    echo "Сделки успешно обновлены за ".(microtime(true) - $start_time)." сек.";

} else {
    $dealsList = getDealsList($subdomain, $limit_rows);

    foreach($dealsList as $deal) {
        $set['request']['leads']['update'][] = array(
            'id' => $deal['id'],
            'last_modified' => time(),
            'custom_fields'=> getCustomFields($dealsFields)
        );
    }

    if(sendSetFieldsRequest($subdomain, $set))
        echo "Сделки(а) успешно обновлены за ".(microtime(true) - $start_time)." сек.";


}

function getCustomFields($deals_fields) {
    foreach($deals_fields as $deal_field) {
        $custom_fields[] = array(
            'id' => $deal_field['id'],
            'values' => array_rand($deal_field['enums'], mt_rand(1, 3))
        );
    }
    return $custom_fields;
}

function getDealsList($subdomain, $limit_rows, $limit_offset = 0) {
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/list?limit_rows='.$limit_rows.'&limit_offset='.$limit_offset;
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    curl_close($curl);

    CheckCurlResponse($code);

    $Response=json_decode($out,true);
    $Response=$Response['response'];

    return $Response['leads'];
}

function sendSetFieldsRequest($subdomain, $set) {
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
    $Response=$Response['response']['leads'];

    if(isset($Response['update'])) return true;
    else return false;

}
