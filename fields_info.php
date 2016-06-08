<?php
$need = array_flip(array('POSITION','PHONE','EMAIL','IM')); #Поля, которые нужно заполнить
$fields = []; #Массив с id всех полей, которые мы получили в account_current
if(isset($contacts_fields))
	do {
        foreach ($contacts_fields as $field) {
            if (is_array($field) && isset($field['id'])) {
                if (isset($field['code']) && isset($need[$field['code']]))
                    $fields[$field['code']] = (int)$field['id'];

                $diff = array_diff_key($need, $fields);

                if (empty($diff))
                    break 2;
            }
        }
        if (isset($diff))
            die('В amoCRM отсутствуют следующие поля' . ': ' . join(', ', $diff));
        else
            die('Невозможно получить дополнительные поля');

    } while(false);
else
	die('Невозможно получить дополнительные поля');
$custom_fields = isset($fields) ? $fields : false;
