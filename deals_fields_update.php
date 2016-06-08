<?php
$set = []; #Массив с контактами для отправки на сервер
$limit_offset = 0; #C какой сделки запрашивать очередные

$i = 0;
while(TRUE) {
    $deals_list = get_deals_list($subdomain, $limit_rows);

    foreach($deals_list as $deal) {
        $set['request']['leads']['update'][] = [
            'id' => $deal['id'],
            'last_modified' => time(),
            'custom_fields' => get_сustom_fields($deals_fields)
        ];
    }

    if($i == 0) { #Запоминаем первую партию сделок которые будем обновлять
        $rem = $deals_list[0]['id'];
        $i++;
    } else { #Если вернулись те же сделки прерываем цикл
        $result = array_filter($deals_list,function($a){
            return $a["id"] == $GLOBALS['rem'];
        });

        if(!empty($result)) break;
    }

    send_fields_request($subdomain, $set);
    sleep(1); #Ждем секунду
}

echo "Сделки успешно обновлены за ".(microtime(TRUE) - $start_time)." сек.";

function get_сustom_fields($deals_fields) {
    $custom_fields = []; # Массив с кастомными полями

    foreach($deals_fields as $deal_field) {
        $custom_fields[] = array(
            'id' => $deal_field['id'],
            'values' => array_rand($deal_field['enums'], mt_rand(1, 3))
        );
    }
    return $custom_fields;
}

/**
 * Позвращает список сделок
 * @param $subdomain
 * @param $limit_rows
 * @param int $limit_offset
 * @return mixed
 */
function get_deals_list($subdomain, $limit_rows, $limit_offset = 0) {
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/list?limit_rows='.$limit_rows;

    $response = send_request($link);

    return $response['leads'];
}

/**
 * Отправляет запрос на добовление полей
 * @param $subdomain
 * @param $set
 * @return bool
 */
function send_fields_request($subdomain, $set) {
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/set';

    $response = send_request($link, $set, 'CURLOPT_CUSTOMREQUEST');

    $response = $response['leads'];

    return isset($response['update']) ? TRUE: FALSE;
}
