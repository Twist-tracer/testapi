<?php
$link = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/accounts/current'; #Cсылка для запроса

$Response = send_request($link);
$Response = $Response['account'];

// Кастомные поля у сделок
if(isset($Response['custom_fields']['leads'])) $dealsFields = $Response['custom_fields']['leads'];
