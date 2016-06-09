<?php
$set = []; #Массив с контактами для отправки на сервер
if(count($contacts_data) > $max) {

	for($i = 0; $i < count($contacts_data); $i++) {
		if ((($i + 1) % $max) == 0) {
			$set['request']['contacts']['add'][]= get_contact_by_index($contacts_data[$i], $deals_id[$i], $contacts_fields);

			#На каждой $max итерации делаем запрос на добавление сделок, чистим $set
            send_contacts_request($subdomain, $set);
			unset($set);
		} else {
            $set['request']['contacts']['add'][] = get_contact_by_index($contacts_data[$i], $deals_id[$i], $contacts_fields);
        }
	}

	echo "Сделки успешно добавлены за ".(microtime(TRUE) - $start_time)." сек.";

} else {
	for($i = 0; $i < count($contacts_data); $i++) {
		$set['request']['contacts']['add'][] = get_contact_by_index($contacts_data[$i], $deals_id[$i], $contacts_fields);
	}

	if(send_contacts_request($subdomain, $set)) {
        echo "Сделки успешно добавлены за ".(microtime(TRUE) - $start_time)." сек.";
    }
}

/**
 * Получает один контакт по итерации
 * @param $contact_data
 * @param $deal_id
 * @param $contacts_fields
 * @return array
 */
function get_contact_by_index($contact_data, $deal_id, $contacts_fields) {
    $custom_fields = []; #Кастомные поля
    foreach($contacts_fields as $contacts_field) {
        switch($contacts_field["code"]) {
            case 'POSITION':
                $custom_fields[] = [
                    'id' => $contacts_field['id'],
                    'values' => [
                        [
                            'value' => $contact_data['position'],
                        ]
                    ]
                ];
                break;
            case 'PHONE':
                $custom_fields[] = [
                    'id' => $contacts_field['id'],
                    'values' => [
                        [
                            'value' => $contact_data['phone'],
                            'enum' => 'MOB'
                        ]
                    ]
                ];
                break;
            case "EMAIL":
                $custom_fields[] = [
                    'id' => $contacts_field['id'],
                    'values'=> [
                        [
                            'value' => $contact_data['email'],
                            'enum' => 'WORK'
                        ]
                    ]
                ];
                break;
            case "IM":
                $custom_fields[] = [
                    'id' => $contacts_field['id'],
                    'values' => [
                        [
                            'value' => $contact_data['im'],
                            'enum' => 'JABBER'
                        ]
                    ]
                ];
                break;
        }
    }

    return [
		'name' => $contact_data['name'],
		'linked_leads_id' => [$deal_id['id']],
        'company_name' => $contact_data['company'],
		'custom_fields' => $custom_fields
    ];
}

function send_contacts_request($subdomain, $set) {
	#Формируем ссылку для запроса
	$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/set';

    $response = send_request($link, $set, 'CURLOPT_CUSTOMREQUEST');

    $response = $response['contacts']['add'];

	return isset($response[0]['id']);
}
