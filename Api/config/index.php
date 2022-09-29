<?php

//CAMPAIGN INFO<<<--------------------
use function PHPSTORM_META\type;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE,PUT');
error_reporting(E_ALL ^ E_NOTICE);

include '../utiles/respuestaJSON.php';
include '../token/getToken.php';
include '../token/setToken.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  $parametros = getConfigParameters();
  $campaignsData = getCampaignsInfo();

  if ($campaignsData == 401) {
    json_response(401, '401 Unauthorized');
  } else {

    $datos = (object) array(
      'parametros' => $parametros,
      'datoscampana' => $campaignsData
    );

    if ($campaignsData !== false) {
      json_response(200, 'Config info', $datos);
    } else {
      json_response(500, 'Error al obtener informacion de uContact');
    }
  }
}

function getCampaignsInfo()
{

  $ini = parse_ini_file('../config.ini', true);
  $url = $ini['Api']['ApiTelephonyUrl'];

  $token = getTokenFileValue();

  // $token = str_replace('"', "", $token);
  // echo $token;

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => 'queue=',
    CURLOPT_HTTPHEADER => array(
      'Authorization: Basic ' . $token . "'",
      'Content-Type: application/x-www-form-urlencoded'
    ),
  ));

  $response = curl_exec($curl);

  switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {

    case 200:
      curl_close($curl);

      $JSON = json_decode($response);
      $campaignsList = array();

      foreach ($JSON as $campana) {
        array_push($campaignsList, $campana->campaign);
      }
      return $campaignsList;
    case 401:
      // echo $http_code;
      setTokenFileValue();
      return 401;
    default:
      return false;
      curl_close($curl);
  }
}



function getConfigParameters()
{
  $ini = parse_ini_file('../config.ini', true);
  $umbrales = $ini['Configuracion']['callswaiting'];
  $servicelevel = $ini['Configuracion']['servicelevel'];
  $Umbrales = explode(",", $umbrales);
  $NivelServicio = explode(",", $servicelevel);

  $parametros = (object) array(
    'umbralesLlamadas' => $Umbrales,
    'nivelservicio' => $NivelServicio
  );

  return $parametros;
}
