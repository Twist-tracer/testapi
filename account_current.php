<?php
$link = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/accounts/current'; #Cсылка для запроса

$response = send_request($link);
$response = $response['account'];

if(isset($response['custom_fields']['leads'])) $deals_fields = $response['custom_fields']['leads']; #Кастомные поля у сделок
