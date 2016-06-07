<?php
if(count($contacts_data) > $max) {

	for($i = 0; $i < count($contacts_data); $i++) {
		if ((($i + 1) % $max) == 0) {
			$set['request']['contacts']['add'][]= getOneContactByIndex($contacts_data, $dealsID, $custom_fields, $i);

			# На каждой $max итерации делаем запрос на добавление сделок, чистим $set
			sendSetContactsRequest($subdomain, $set);
			unset($set);
		} else
			$set['request']['contacts']['add'][] = getOneContactByIndex($contacts_data, $dealsID, $custom_fields, $i);
	}

	echo "Сделки успешно добавлены за ".(microtime(true) - $start_time)." сек.";

} else {
	for($i = 0; $i < count($contacts_data); $i++) {
		$set['request']['contacts']['add'][] = getOneContactByIndex($contacts_data, $dealsID, $custom_fields, $i);
	}

	if(sendSetContactsRequest($subdomain, $set));
		echo "Сделки успешно добавлены за ".(microtime(true) - $start_time)." сек.";
}

function getOneContactByIndex($contacts_data, $dealsID, $custom_fields, $iter) {
	$contact=array(
		'name'=>$contacts_data[$iter]['name'],
		'linked_leads_id'=>array($dealsID[$iter]['id']),
		'custom_fields'=>array(
			array(
				'id'=>$custom_fields['EMAIL'],
				'values'=>array(
					array(
						'value'=>$contacts_data[$iter]['email'],
						'enum'=>'WORK'
					)
				)
			)
		)
	);

	if(!empty($contacts_data[$iter]['company']))
		$contact += array('company_name'=>$contacts_data[$iter]['company']);
	if(!empty($contacts_data[$iter]['position']))
		$contact['custom_fields'][]=array(
			'id'=>$custom_fields['POSITION'],
			'values'=>array(
				array(
					'value'=>$contacts_data[$iter]['position']
				)
			)
		);
	if(!empty($contact_data['phone']))
		$contact['custom_fields'][]=array(
			'id'=>$custom_fields['PHONE'],
			'values'=>array(
				array(
					'value'=>$contacts_data[$iter]['phone'],
					'enum'=>'OTHER'
				)
			)
		);
	if(!empty($contact_data['im']))
		$contact['custom_fields'][]=array(
			'id'=>$custom_fields['IM'],
			'values'=>array(
				array(
					'value'=>$contacts_data[$iter]['im'],
					'enum'=>'JABBER'
				)
			)
		);

	return $contact;
}

function sendSetContactsRequest($subdomain, $set) {
	#Формируем ссылку для запроса
	$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/set';
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
	$Response=$Response['response']['contacts']['add'];

	if(isset($Response[0]['id'])) return true;
	else return false;
}
