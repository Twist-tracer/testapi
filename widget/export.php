<?php
    require_once 'widget.php';

    $configs = array(
        'USER_LOGIN' => 'twist.tracer@gmail.com',
        'API_KEY' => '3152adcab5a33956a198f997412f4310',
        'SUB_DOMAIN' => 'bogdanov'
    );

    $widget = new MyExportWidget($_GET['data'], $configs);

    header('Content-type: text/html; charset="utf-8"');
    print_r($widget->generateCSVFile());
?>