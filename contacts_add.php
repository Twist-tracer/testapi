<?php
$set = []; #Массив с контактами для отправки на сервер
if(count($contacts_data) > $max) {

	for($i = 0; $i < count($contacts_data); $i++) {
		if ((($i + 1) % $max) == 0) {
			$set['request']['contacts']['add'][]= get_contact_by_index($contacts_data, $deals_id, $custom_fields, $i);

			#На каждой $max итерации делаем запрос на добавление сделок, чистим $set
            send_сontacts_request($subdomain, $set);
			unset($set);
            sleep(1); #Ждем секунду
		} else
			$set['request']['contacts']['add'][] = get_contact_by_index($contacts_data, $deals_id, $custom_fields, $i);
	}

	echo "Сделки успешно добавлены за ".(microtime(TRUE) - $start_time)." сек.";

} else {
	for($i = 0; $i < count($contacts_data); $i++) {
		$set['request']['contacts']['add'][] = get_contact_by_index($contacts_data, $deals_id, $custom_fields, $i);
	}

	if(send_сontacts_request($subdomain, $set));
		echo "Сделки успешно добавлены за ".(microtime(TRUE) - $start_time)." сек.";
}

function get_contact_by_index($contacts_data, $dealsID, $custom_fields, $iter) {
	$contact = [
		'name' => $contacts_data[$iter]['name'],
		'linked_leads_id' => [$dealsID[$iter]['id']],
		'custom_fields' => [
			[
				'id'=>$custom_fields['EMAIL'],
				'values'=> [
					[
						'value'=>$contacts_data[$iter]['email'],
						'enum'=>'WORK'
					]
				]
			]
		]
	];

	if(!empty($contacts_data[$iter]['company']))
		$contact += ['company_name'=>$contacts_data[$iter]['company']];
	if(!empty($contacts_data[$iter]['position'])) {
        $contact['custom_fields'][] = [
            'id'=>$custom_fields['POSITION'],
            'values' => [
                [
                    'value'=>$contacts_data[$iter]['position']
                ]
            ]
        ];
    }
	if(!empty($contact_data['phone'])) {
        $contact['custom_fields'][] = [
            'id'=>$custom_fields['PHONE'],
            'values'=>[
                [
                    'value'=>$contacts_data[$iter]['phone'],
                    'enum'=>'OTHER'
                ]
            ]
        ];
    }
	if(!empty($contact_data['im'])) {
        $contact['custom_fields'][] = [
            'id'=>$custom_fields['IM'],
            'values'=>[
                [
                    'value'=>$contacts_data[$iter]['im'],
                    'enum'=>'JABBER'
                ]
            ]
        ];
    }

	return $contact;
}

function send_сontacts_request($subdomain, $set) {
	#Формируем ссылку для запроса
	$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/set';

    $response = send_request($link, $set, 'CURLOPT_CUSTOMREQUEST');

    $response = $response['contacts']['add'];

	return isset($response[0]['id']) ? TRUE: FALSE;
}
