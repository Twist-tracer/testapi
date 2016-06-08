<?php
$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/set'; #Cсылка для запроса
$set = []; #Массив со сделками для отправки на сервер
$deals_id = []; #Массив с ID добавленных сделок

if(count($deals_data) > $max) {
    for($i = 0; $i < count($deals_data); $i++) {
        if((($i + 1) % $max) == 0) {
            $set['request']['leads']['add'][] = $deals_data[$i];

            # На каждой $max итерации делаем запрос на добавление сделок, чистим $set
            $deals_id = array_merge($deals_id, sendSetListRequest($subdomain, $set));
            unset($set);
        }
        else $set['request']['leads']['add'][] = $deals_data[$i];
    }
} else {
    foreach($deals_data as $deal_data) {
        $set['request']['leads']['add'][] = $deal_data;
    }
    $deals_id = sendSetListRequest($subdomain, $set);
}

/**
 * Возвращает массив с ID добавленных сделок
 * @param $link
 * @param $set
 * @return mixed
 */
function sendSetListRequest($link, $set) {
    $response = send_request($link, $set, 'CURLOPT_CUSTOMREQUEST');

    return $response['leads']['add'];
}
