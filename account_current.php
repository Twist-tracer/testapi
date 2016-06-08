<?php
$link = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/accounts/current'; #Cсылка для запроса
$contacts_fields = []; #Кастомные поля у контактов
$deals_fields = []; #Кастомные поля у сделок

$response = send_request($link);
$response = $response['account'];

if(isset($response['custom_fields']['contacts'])) $contacts_fields = $response['custom_fields']['contacts']; #Кастомные поля у контактов
if(isset($response['custom_fields']['leads'])) $deals_fields = $response['custom_fields']['leads']; #Кастомные поля у сделок
