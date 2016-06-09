<?php
/**
 * Class MyExportWidget
 */
class MyExportWidget {
	private $_leads_data;
	private $_configs;

	public function __construct($data, $configs) {
		$this->_leads_data = json_decode($data, TRUE);
		$this->_configs = $configs;
	}

    /**
     * Собираем необходимую информацию.
     * Собираем строку и пишем ее в CSV файл.
     * Возвращаем результат.
     * @return bool
     */
	public function generate_csv_file() {
		$collection = $this->collect_information();

        return !file_put_contents($this->_configs['EXPORT_FILE'], $this->generate_csv_string($collection)) ? FALSE : TRUE;
	}

    /**
     * Возвращает отформатированную строку для записи в cvs
     *
     * Авторизовываемся
     * Получаем информацию о сделках по их ID
     * Получаем данные информацию о связанных контактах по ID сделок
     * Получим все контакты по уже известным ID
     * Получим все компании по уже известным ID
     * Получим данные информацию о кастомных полях сделок
     *
     * @return array
     * data['leads']- Массив со сделками
     * data['contacts'] - Массив с контактами
     * data['companies'] - Массив с компаниями
     * data['fields'] - Массив с полями
     *
     */
	private function collect_information() {
		if($this->auth()) {
			$leads = $this->get_data_on_id($this->_leads_data, 'leads');
			if(!empty($leads)) {
				$cl_links = $this->get_contacts_and_leads_relations(); #$cl_links[]['contact_id'] - ID связанного контакта

				$contacts_data = []; # Массив с id контактов
                $companies_data = []; # Массив с id компаний

                foreach($cl_links as $cl_link) {
                    $contacts_data[] = $cl_link['contact_id'];
                }

                foreach($leads as $lead) {
                    $companies_data[] = $lead['linked_company_id'];
                }

                $contacts = $this->get_data_on_id($contacts_data, 'contacts');
                $companies = $this->get_data_on_id($companies_data, 'companies');

				return [
					"leads" => $leads,
					"contacts" => $contacts,
					"companies" => $companies,
				];
			}
		} else die('Авторизация не удалась');
	}

    /**
     * @param $data - Массив с данными
     * data['leads'][]['name'] - название
     * data['leads'][]['date_create'] - дата создания
     * data['leads']['tags'] - теги
     * data['leads']['custom_fields'] - информация из кастомных полей
     * data['leads']['linked_company_id'] - ID связанной компании
     * data['contacts'][]['name] - Имя связанного контакта
     * data['companies'][]['name] - Имя связанной компании
     *
     * @return string Отформатированнная строка для cvs
     */
	private function generate_csv_string($data) {
		$header = '"Название сделки";"Дата создания сделки";"Теги";"Имя связанного контакта";"Название связанной компании";';
		$row = "";

        #Собераем остальные строчки
		for($i = 0; $i < count($data["leads"]); $i++) {
            $lead = $data["leads"][$i]; #Текущая сделка
            $contact = $data["contacts"][$i]; #Текущий контакт
            $company =  $data["companies"][$i]; #Текущая компания

			$row .= '"'.$lead['name'].'";'; #Имя сделки
			$row .= '"'.date("d:m:Y H:i:s", $lead['date_create']).'";'; #Дата создания
			if(count($lead['tags']) == 0) { #Теги сделки
				$row .= '"";';
			} else {
				for($j = 0; $j < count($lead['tags']); $j++) {
					if($j < count($lead['tags']) - 1) {
						$row .= '"'.$lead['tags'].'",';
					} else {
						$row .= '"'.$lead['tags'].'";';
					}
				}
			}

			$row .= '"'.$contact['name'].'";'; #Имя контакта

            if(count($lead['custom_fields']) != 0) {
                $row .= '"'.$company['name'].'";'; #Имя компании

                #Перебераем все поля, если они есть
                for($j = 0; $j < count($lead['custom_fields']); $j++) {
                    $field = $lead['custom_fields'][$j]; #Текущая поле

                    #Заполняем header названиями полей
                    if($i == 0) {
                        if($j < count($lead['custom_fields']) - 1)
                            $header .= '"'.$field['name'].'";';
                        else {
                            $header .= '"'.$field['name'].'"'."\r\n";
                        }
                    }

                    #Заполняем row значениями
                    if(count($field['values']) != 0) {
                        #Перебераем все значения поля, если они есть
                        for($t = 0; $t < count($field['values']); $t++) {
                            $value = $field['values'][$t]['value']; #Одно значение

                            if($t < count($field['values']) - 1) {
                                $row .= '"'.$value.'",';
                            } elseif($j < count($lead['custom_fields']) - 1) {
                                $row .= '"'.$value.'";';
                            } else {
                                $row .= '"'.$value.'"'."\r\n";
                            }
                        }
                    } else {
                        $row .= '"";';
                    }
				}
			} else {
                $row .= '"'.$company['name'].'"'."\r\n";
            }
		}
		return iconv('UTF-8','Windows-1251',$header.$row);
	}

	/**
     * Авторизация
	 * @return bool
	 */
	private function auth() {
		#Формируем ссылку для запроса
		$link='https://'.$this->_configs['SUB_DOMAIN'].'.amocrm.ru/private/api/auth.php?type=json';

		#Неоюходимые данные для авторизации
		$postData=array(
			'USER_LOGIN'=>$this->_configs['USER_LOGIN'],
			'USER_HASH'=>$this->_configs['API_KEY']
		);

		$response = $this->send_request($link, $postData, 'CURLOPT_POST');

		return $response['auth'];
	}

    /**
     * Получает данные по селакам, контактам, компаниям по их id
     * @param $data array - массив с id сущности
     * @param $type string (leads|contacts|companies) - тип сущности
     *
     * @return array
     */
    private function get_data_on_id($data, $type) {
        $link = ""; # Cсылка для запроса
        $return = ""; #Ключ для возвращаемого массива
        switch($type) {
            case 'leads':
                $link = 'https://'.$this->_configs['SUB_DOMAIN'].'.amocrm.ru/private/api/v2/json/leads/list?'.$this->parse_data_to_url($data, 'id');
                break;
            case 'contacts':
                $link = 'https://'.$this->_configs['SUB_DOMAIN'].'.amocrm.ru/private/api/v2/json/contacts/list?'.$this->parse_data_to_url($data, 'id');
                break;
            case 'companies':
                $link = 'https://'.$this->_configs['SUB_DOMAIN'].'.amocrm.ru/private/api/v2/json/company/list?'.$this->parse_data_to_url($data, 'id');
                break;
        }

        $response = $this->send_request($link);

        switch($type) {
            case 'leads':
                $return = 'leads';
                break;
            case 'contacts':
            case 'companies':
                $return = 'contacts';
                break;
        }
        return !empty($response[$return]) ? $response[$return] : FALSE; # Если указан неверный формат $type;
	}

	/**
     * Возращает массив id сделок и свзяанных с ними контактов
	 * @return array
	 */
	private function get_contacts_and_leads_relations() {
		#Формируем ссылку для запроса
		$link='https://'.$this->_configs['SUB_DOMAIN'].'.amocrm.ru/private/api/v2/json/contacts/links?'.$this->parse_data_to_url($this->_leads_data, 'deals_link');

		$response = $this->send_request($link);
		return $response['links'];
	}

    /**
     * Перереводит массив в строку пригодную для передачи в get
     * @param $data
     * @param $keyword
     * @return string
     */
	private function parse_data_to_url($data, $keyword) {
		$url_string = ''; #id[]=value1&id[]=value2&id[]=value3

		for($i = 0; $i < count($data); $i++) {
			if($i < count($data) - 1) {
                $url_string .= $keyword.'[]='.$data[$i]."&";
			} else $url_string .= $keyword.'[]='.$data[$i];
		}

		return $url_string;
	}

	/**
     * Отправляет запрос на сервер
	 * @param $link
	 * @param $post_data array
	 * @param bool|false $type string (CURLOPT_POST | CURLOPT_CUSTOMREQUEST)
     * CURLOPT_CUSTOMREQUEST - POST
     * CURLOPT_POST - GET
	 * @return array
	 */
	private function send_request($link, $post_data = [], $type = FALSE) {
		$curl = curl_init(); #Сохраняем дескриптор сеанса cURL

		#Устанавливаем необходимые опции для сеанса cURL
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl, CURLOPT_URL,$link);
		if($type == 'CURLOPT_POST') {
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_POSTFIELDS,http_build_query($post_data));
		} elseif($type == 'CURLOPT_CUSTOMREQUEST') {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST,'POST');
			curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($post_data));
			curl_setopt($curl, CURLOPT_HTTPHEADER,['Content-Type: application/json']);
		}
		curl_setopt($curl, CURLOPT_HEADER, FALSE);
		curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

		$out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
		$code = curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
		curl_close($curl); #Заверашем сеанс cURL
		$this->сheck_сurl_response($code);

		$response = json_decode($out, TRUE);
		return $response['response'];
	}

    /**
     * Проверяет код ответа сервера
     * В случае ошибки выбрасывает исключение
     * @param $code - код ответа сервера
     */
	private function сheck_сurl_response($code) {
		$code = (int)$code;
		$errors = [
			301=>'Moved permanently',
			400=>'Bad request',
			401=>'Unauthorized',
			403=>'Forbidden',
			404=>'Not found',
			500=>'Internal server error',
			502=>'Bad gateway',
			503=>'Service unavailable'
		];
		try {
			#Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
			if($code!=200 && $code!=204)
				throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		} catch(Exception $E) {
			die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
		}
	}

    /**
     * Отправляет заголовки для скачивания файла
     * @param $file - путь до сгенерированного файла
     */
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
