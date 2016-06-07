<?php
$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/set'; #Cсылка для запроса
$set = array(); #Массив со сделками для отправки на сервер
$dealsID = array(); #Массив с ID добавленных сделок

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

/**
 * Возвращает массив с ID добавленных сделок
 * @param $link
 * @param $set
 * @return mixed
 */
function sendSetListRequest($link, $set) {
    $Response = send_request($link, $set, 'CURLOPT_CUSTOMREQUEST');

    return $Response['leads']['add'];
}
