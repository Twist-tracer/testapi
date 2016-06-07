<?php
/**
 * Class MyExportWidget
 */
class MyExportWidget
{
	private $leadsData;
	private $configs;

	public function __construct($data, $configs) {
		$this->leadsData = json_decode($data, true);
		$this->configs = $configs;
	}

	public function generateCSVFile() {
		// 1) Собираем всю необходимую информацию
		$collection = $this->collectInformstion();

		// 2) Собираем строку и пишем ее в CSV файл. Возвращаем результат
		if(!file_put_contents($this->configs['EXPORT_FILE'], $this->generateCSVString($collection)))
			return false;
		else {
			return true;
		}
	}

	private function collectInformstion() {
		// 1) Авторизуемся, если авторизация прошла успешно идем дальше
		if($this->auth()) {
			// 2) Получим информацию о сделках по их ID, если сделки вернулись идем дальше
			$leads = $this->getLeadsOnID();
			/**
			 * $leads[]['name'] - название
			 * $leads[]['date_create'] - дата создания
			 * $leads[]['tags'] - теги
			 * $leads[]['custom_fields'] - информация из кастомных полей
			 * $leads[]['linked_company_id'] - ID связанной компании
			 * $cl_links[]['contact_id'] - ID связанного контакта
			 * $contacts[]['name] - Имя связанного контакта
			 * $companies[]['name] - Имя связанной компании
			 */
			if(!empty($leads)) {
				// 3) Получим данные информацию о связанных контактах по ID сделок
				$cl_links = $this->getContactsAndLeadsRelations();

				// 4) Получим все контакты по уже известным ID
				$contactsData = array();
				for($i = 0; $i < count($leads); $i++) {
					$contactsData[$i] = $cl_links[$i]['contact_id'];
				}
				$contacts = $this->getContactsOnID($contactsData);


				// 5) Получим все компании по уже известным ID
				$companiesData = array();
				for($i = 0; $i < count($leads); $i++) {
					$companiesData[$i] = $leads[$i]['linked_company_id'];
				}
				$companies = $this->getCompaniesOnID($companiesData);

				// 6) Получим данные информацию о кастомных полях сделок
				$fields = $this->getFields();

				return array(
					"leads" => $leads,
					"contacts" => $contacts,
					"companies" => $companies,
					"fields" => $fields
				);
			}
		}
	}


	private function generateCSVString($data) {
		# Собираем заглавную строчку
		$header = '"Название сделки";"Дата создания сделки";"Теги";"Имя связанного контакта";"Название связанной компании";';

		if(count($data["fields"]) != 0) {
			for($i = 0; $i < count($data["fields"]); $i++) {
				if($i < count($data["fields"]) - 1)
					$header .= '"'.$data["fields"][$i]['name'].'";';
				else $header .= '"'.$data["fields"][$i]['name'].'"';
			}
		}

		# А теперь и все остальные
		$row = "";
		for($i = 0; $i < count($data["leads"]); $i++) {
			$row .= '"'.$data["leads"][$i]['name'].'";';
			$row .= '"'.date("d:m:Y H:i:s", $data["leads"][$i]['date_create']).'";';
			if(count($data["leads"][$i]['tags']) == 0) {
				$row .= '"";';
			} else {
				for($j = 0; $j < count($data["leads"][$i]['tags']); $j++) {
					if($j < count($data["leads"][$i]['tags']) - 1) {
						$row .= '"'.$data["leads"][$i]['tags'].'",';
					} else {
						$row .= '"'.$data["leads"][$i]['tags'].'";';
					}
				}

			}
			$row .= '"'.$data["contacts"][$i]['name'].'";';
			$row .= '"'.$data["companies"][$i]['name'].'";';
			if(count($data["fields"]) != 0) {
				for($j = 0; $j < count($data["fields"]); $j++) {

					if($j < count($data["fields"]) - 1) {
						if($data["fields"][$j]['name'] == $data["leads"][$i]['custom_fields'][$j]['name']) {
							if(count($data["leads"][$i]['custom_fields'][$j]['values']) == 0) {
								$row .= '"";';
							} else {
								for($t = 0; $t < count($data["leads"][$i]['custom_fields'][$j]['values']); $t++) {
									if($t < count($data["leads"][$i]['custom_fields'][$j]['values']) - 1) {
										$row .= '"'.$data["leads"][$i]['custom_fields'][$j]['values'][$t]['value'].'",';
									} else {
										$row .= '"'.$data["leads"][$i]['custom_fields'][$j]['values'][$t]['value'].'"';
									}
								}
							}
						} else $row .= '"";';
						$row .= ';';
					} else {
						if($data["fields"][$j]['name'] == $data["leads"][$i]['custom_fields'][$j]['name']) {
							if(count($data["leads"][$i]['custom_fields'][$j]['values']) == 0) {
								$row .= '"";';
							} else {
								for($t = 0; $t < count($data["leads"][$i]['custom_fields'][$j]['values']); $t++) {
									if($t < count($data["leads"][$i]['custom_fields'][$j]['values']) - 1) {
										$row .= '"'.$data["leads"][$i]['custom_fields'][$j]['values'][$t]['value'].'",';
									} else {
										$row .= '"'.$data["leads"][$i]['custom_fields'][$j]['values'][$t]['value'].'"'."\r\n";
									}
								}
							}
						} else $row .= '""'."\r\n";
					}
				}
			}
		}
		return iconv('UTF-8','Windows-1251',$header."\r\n".$row);
	}

	/**
	 * @return bool
	 */
	private function auth() {
		#Формируем ссылку для запроса
		$link='https://'.$this->configs['SUB_DOMAIN'].'.amocrm.ru/private/api/auth.php?type=json';

		#Неоюходимые данные для авторизации
		$postData=array(
			'USER_LOGIN'=>$this->configs['USER_LOGIN'],
			'USER_HASH'=>$this->configs['API_KEY']
		);

		$Response = $this->cURLSession($link, $postData, 'CURLOPT_POST');

		return $Response['auth'];
	}

	/**
	 * @return mixed
	 */
	private function getLeadsOnID() {
		#Формируем ссылку для запроса
		$link='https://'.$this->configs['SUB_DOMAIN'].'.amocrm.ru/private/api/v2/json/leads/list?'.$this->parseDataToURL($this->leadsData, 'id');;

		$Response = $this->cURLSession($link);
		return $Response['leads'];
	}

	/**
	 * @return mixed
	 */
	private function getContactsAndLeadsRelations() {
		#Формируем ссылку для запроса
		$link='https://'.$this->configs['SUB_DOMAIN'].'.amocrm.ru/private/api/v2/json/contacts/links?'.$this->parseDataToURL($this->leadsData, 'deals_link');

		$Response = $this->cURLSession($link);
		return $Response['links'];
	}

	/**
	 * @return mixed
	 */
	private function getContactsOnID($data) {
		#Формируем ссылку для запроса
		$link='https://'.$this->configs['SUB_DOMAIN'].'.amocrm.ru/private/api/v2/json/contacts/list?'.$this->parseDataToURL($data, 'id');

		$Response = $this->cURLSession($link);
		return $Response['contacts'];
	}

	/**
	 * @return mixed
	 */
	private function getCompaniesOnID($data) {
		#Формируем ссылку для запроса
		$link='https://'.$this->configs['SUB_DOMAIN'].'.amocrm.ru/private/api/v2/json/company/list?'.$this->parseDataToURL($data, 'id');

		$Response = $this->cURLSession($link);
		return $Response['contacts'];
	}

	private function getFields() {
		$link='https://'.$this->configs['SUB_DOMAIN'].'.amocrm.ru/private/api/v2/json/accounts/current';

		$Response = $this->cURLSession($link);
		return $Response['account']['custom_fields']['leads'];
	}

	private function parseDataToURL($data, $keyword) {
		$urlSrting = ''; #id[]=value1&id[]=value2&id[]=value3
		for($i = 0; $i < count($data); $i++) {
			if($i < count($data) - 1) {
				$urlSrting .= $keyword.'[]='.$data[$i]."&";
			} else $urlSrting .= $keyword.'[]='.$data[$i];
		}

		return $urlSrting;
	}

	/**
	 * @param $link
	 * @param array() $postData
	 * @param bool|false $flag string (CURLOPT_POST | CURLOPT_CUSTOMREQUEST)
	 * @return mixed
	 */
	private function cURLSession($link, $postData = array(), $flag = false) {
		$curl=curl_init(); #Сохраняем дескриптор сеанса cURL

		#Устанавливаем необходимые опции для сеанса cURL
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		if($flag == 'CURLOPT_POST') {
			curl_setopt($curl,CURLOPT_POST,true);
			curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($postData));
		}elseif($flag == 'CURLOPT_CUSTOMREQUEST') {
			curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
			curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($postData));
			curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
		}
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

		$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
		curl_close($curl); #Заверашем сеанс cURL
		$this->CheckCurlResponse($code);

		$Response=json_decode($out,true);
		return $Response=$Response['response'];
	}

	private function CheckCurlResponse($code) {
		$code=(int)$code;
		$errors=array(
			301=>'Moved permanently',
			400=>'Bad request',
			401=>'Unauthorized',
			403=>'Forbidden',
			404=>'Not found',
			500=>'Internal server error',
			502=>'Bad gateway',
			503=>'Service unavailable'
		);
		try
		{
			#Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
			if($code!=200 && $code!=204)
				throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		}
		catch(Exception $E)
		{
			die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
		}
	}

	public function file_force_download($file) {
		if (file_exists($file)) {
			// сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
			// если этого не сделать файл будет читаться в память полностью!
			if (ob_get_level()) {
				ob_end_clean();
			}
			// заставляем браузер показать окно сохранения файла
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			// читаем файл и отправляем его пользователю
			readfile($file);
			exit;
		}
	}
}
