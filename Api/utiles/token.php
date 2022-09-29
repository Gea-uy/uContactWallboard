<?php



function getToken()
{
    $token = getUserToken();

    if ($token == false || $token == 404) {
        $token = getTokenUserLogin();
    }

    $token = str_replace('"', "", $token);
    return $token;
}
function getUserToken()
{

    $ini = parse_ini_file('../config.ini', true);
    $username = $ini['Api']['username'];
    $password = $ini['Api']['password'];
    $url = $ini['Api']['URLgetUserToken'];

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
        CURLOPT_POSTFIELDS => 'user=' . $username . '&password=' . $password,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);

    switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {

        case 200:
            curl_close($curl);
            // print_r($response);
            // die;
            if ($response != "0") {
                return $response;
            }
            return false;
            break;
        case 404:
            return 404;
            break;
        default:
            return false;
            curl_close($curl);
    }
}

function getTokenUserLogin()
{

    $ini = parse_ini_file('../config.ini', true);

    $username = $ini['Api']['username'];
    $password = $ini['Api']['password'];
    $url = $ini['Api']['URLgetUserLogin'];

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'user=' . $username . '&password=' . $password,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);

    // curl_close($curl);
    // echo gettype($response);

    switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {

        case 200:
            curl_close($curl);
            if ($response != "0") {


                $respuestaJSON = json_decode($response);
                // print_r($respuestaJSON);
                // echo gettype($respuestaJSON);
                // print_r($respuestaJSON[2]);
                // die;
                return ($respuestaJSON[2]);
            }
            return false;
            break;
        default:
            return false;
            curl_close($curl);
    }
}
function endSession($token)
{

    $ini = parse_ini_file('../config.ini', true);
    $url = $ini['Api']['UrlEndSessionApi'];
    $username = $ini['Api']['username'];

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url . '?user=' . $username . '&token=' . $token,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            'content-type: application/x-www-form-urlencoded; charset=UTF-8'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    // echo "endSession ".$response;
}
