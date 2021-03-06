<?php
require_once 'widget.php';

$configs = [
    'USER_LOGIN' => 'twist.tracer@gmail.com',
    'API_KEY' => '3152adcab5a33956a198f997412f4310',
    'SUB_DOMAIN' => 'bogdanov',
    'EXPORT_FILE' => 'export/amocrm_leads.csv'
];

$widget = new MyExportWidget($_GET['data'], $configs);

if($widget->generate_csv_file()) {
  $widget->file_force_download($configs['EXPORT_FILE']);
}
