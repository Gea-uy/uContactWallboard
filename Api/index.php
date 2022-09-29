
<?php

header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Origin: http://smsgatewayapp.com');

echo "ESTO ES UNA API" . "</br>";

date_default_timezone_set('America/Montevideo');
$fecha = date('Y-m-d - G:i:s');
echo $fecha;



?>