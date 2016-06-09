<?php
$field = [
    'name' => $_POST['field_name'],
    'type' => $_POST['field_type'],
    'element_type' => $_POST['element_type'],
    'origin' => md5($_POST['field_name'].$_POST['field_type'].$_POST['element_type']).'_'.str_replace(" ", "_", $_POST['field_name']),
    'enums' => $_POST['options'],
    'disabled' => 1
];

$set['request']['fields']['add'][]=$field;

#Формируем ссылку для запроса
$link = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/fields/set';

$response = send_request($link, $set, 'CURLOPT_CUSTOMREQUEST');

$response = $response['fields']['add'];

if(isset($response[0]['id'])) {
    print('Поле успешно добавленно');
} else {
    print($response);
}
