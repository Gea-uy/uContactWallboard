<?php

//CALLBACKS<<<--------------------
use function PHPSTORM_META\type;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE,PUT');
error_reporting(E_ALL ^ E_NOTICE);

include_once '../utiles/respuestaJSON.php';
include_once '../utiles/conexion.php';
include_once '../utiles/log.php';
include_once '../token/getToken.php';
// include '../utiles/token.php';


if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET["campaign"]) {

  if (existCapaign($_GET["campaign"])) {
    getCallbacksData($_GET["campaign"]);
  } else {
    return null;
  }
}


function getCallbacksData($campaign)
{

  $ini = parse_ini_file('../config.ini', true);
  $campaignsList = $ini['Callback']['CallbackCampaigns'];
  $dialersList = $ini['Callback']['CallbackDialers'];
  $arrayCampaigns = explode(",", $campaignsList);
  $arrayDialers = explode(",", $dialersList);

  $data = array();

  try {

    $indexDialer = array_search($campaign, $arrayCampaigns);
    // var_dump($indexDialer);
    if ($indexDialer === false) {
      return null;
    }
    $data["$campaign"] = array();
    $data["$campaign"]["Dialer"] = $arrayDialers[$indexDialer];
    $data["$campaign"]["CallsSpool"] = getCallsSpool($arrayDialers[$indexDialer]);
    $data["$campaign"]["CallsScheduler"] = getCallsScheduler($arrayDialers[$indexDialer]);
    $data["$campaign"]["CompletedCalls"] =  getDialerCalls($arrayDialers[$indexDialer]);
    return ($data);
  } catch (\Throwable $th) {
    json_response(500, "Error. Revisar la conexión a la base de datos");
  }
}

function getCallsSpool($campaign)
{

  $mysqli = conexionDB();
  $consulta = "SELECT count(*) FROM ccdata.calls_spool where campaign = '$campaign'";

  $result = $mysqli->query($consulta);

  if ($result->num_rows > 0) {
    $mysqli->close();
    return $result->fetch_assoc()['count(*)'];
  } else {
    $mysqli->close();
    return false;
  }
}

function getCallsScheduler($campaign)
{

  $mysqli = conexionDB();

  $consulta = "SELECT count(*) FROM ccdata.calls_scheduler where campaign = '$campaign'";

  $result = $mysqli->query($consulta);

  if ($result->num_rows > 0) {
    $mysqli->close();
    return $result->fetch_assoc()['count(*)'];
  } else {
    $mysqli->close();
    return false;
  }
}


function existCapaign($campaign)
{
  $ini = parse_ini_file('../config.ini', true);
  $campaignsList = $ini['Callback']['CallbackCampaigns'];
  $arrayCampaigns = explode(",", $campaignsList);
  // print_r($arrayCampaigns);
  // var_dump(array_search($campaign, $arrayCampaigns));

  if (array_search($campaign, $arrayCampaigns) === false) {
    // echo "false";
    return false;
  } else {
    // echo "true";
    return true;
  }
}

function getDialerCalls($campaign)
{

  $ini = parse_ini_file('../config.ini', true);
  $url = $ini['Api']['ApiTelephonyUrl'];

  $token = getTokenFileValue();
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



  // endSession($token);

  switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {

    case 200:
      curl_close($curl);
      $JSON = json_decode($response);
      return ($JSON->$campaign->completed);
      // print_r($JSON->$campaign);
      // die;
    default:
      return false;
      curl_close($curl);
  }
}
