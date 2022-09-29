
<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE');
// error_reporting(E_ALL ^ E_NOTICE);

function escribir_log_php($mensaje)
{

    $path = "C:\inetpub\wwwroot\configAddin";
    $nombreIni = $path . "\Addin.ini";
    $ini = new ini($nombreIni);
    $version = $ini->get("version", "Configuracion");

    $texto = "BackendPhp Versión:[$version] - ";

    $texto = $texto . $mensaje;

    escribir_log($texto);

}
function escribir_log($texto)
{

    global $cantidad_de_logs;
    global $dir_log;
    global $nombre_log;
    
    $ini = parse_ini_file('../config.ini', true);
    $callswaiting = $ini['Configuracion']['callswaiting'];


    $dir_log  = $_SERVER['DOCUMENT_ROOT'] . '/Wallboard/Api/';
    $nombre_log = $ini['Configuracion']['nombre_log'];
    $cantidad_de_logs = $ini['Configuracion']['cantidad_de_logs'];
    
    // echo $nombre_log;
    

    
    $id_log = rand(1000, 99999);
    $version = "1.000";
    date_default_timezone_set('America/Montevideo');

    $long = strlen($nombre_log);
    $archivo_sin_extension = substr($nombre_log, 0, $long - 4);

    if (file_exists($dir_log . $nombre_log)) {
        clearstatcache();
        $tamanio = filesize($dir_log . $nombre_log);
        if ($tamanio <= 10485760) {
            write($archivo_sin_extension, $dir_log, $nombre_log, $texto);
            return;
        } else {
            list($existe, $disponible) = existen($archivo_sin_extension);
            if ($existe == "1") {
                $log_numero = archivo_mas_antiguo($archivo_sin_extension);
                rename($dir_log . $nombre_log, $dir_log . $archivo_sin_extension . $log_numero . ".log.bak");
                unlink($dir_log . $nombre_log);
                escribir_log($texto);
            } else {
                rename($dir_log . $nombre_log, $dir_log . $archivo_sin_extension . $disponible . ".log.bak");
                unlink($dir_log . $nombre_log);
                escribir_log($texto);
            }
        }
    } else {
        write($archivo_sin_extension, $dir_log, $nombre_log, $texto);
    }
}
function archivo_mas_antiguo($archivo_sin_extension)
{
    global $cantidad_de_logs;
    global $dir_log;

    $fecha_menor = 0;

    for ($j = 1; $j <= $cantidad_de_logs; $j++) {

        $fecha_arch = filectime($dir_log . $archivo_sin_extension . $j . ".log.bak"); // fecha del actual arch

        if (($fecha_menor == 0) || ($fecha_menor > $fecha_arch)) {
            $fecha_menor = $fecha_arch;
            $arch_menor = $j;
        }
    }

    return $arch_menor;
}
function existen($archivo_sin_extension)
{
    global $cantidad_de_logs;
    global $dir_log;

    for ($j = 1; $j <= $cantidad_de_logs; $j++) {
        if (file_exists($dir_log . $archivo_sin_extension . $j . ".log.bak")) {
            $existe = "1";
        } else {
            $existe = "0";
            return array($existe, $j);
        }
    }
    return array($existe, $j);
}
function write($archivo_sin_extension, $dir_log, $nombre_log, $texto)
{
    global $version;
    global $id_log;
    $now = (string) microtime();
    $now = explode(' ', $now);
    $mm = explode('.', $now[0]);
    $mm = $mm[1];
    $rest = substr($mm, 0, -5);
    $now = $now[1];
    $segundos = $now % 60;
    $segundos = $segundos < 10 ? "$segundos" : $segundos;

    $hoy = strval(date("Y-m-d H:i:s.", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"))) . "$rest");

    //$hoy = date("Y/m/d H:i:s");

    $heading = $version . "  " . $hoy . " - ";
    // $heading = $version."  ".$hoy."   ".$id_log."   ";

    $gestor = fopen($dir_log . $nombre_log, 'a');
    if (!$gestor) {
        exit;
    }
    if (fwrite($gestor, $heading . $texto . "\r\n") === false) {
        exit;
    }
    fclose($gestor);
}