<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE');
error_reporting(E_ALL ^ E_NOTICE);

function json_response($code = 200, $message = null, $objRespuesta = null)
{
    // clear the old headers
    header_remove();
    header("Access-Control-Allow-Origin: *");
    // set the actual code
    http_response_code($code);
    // set the header to make sure cache is forced
    header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
    // treat this as json
    header('Content-Type: application/json');
    $status = array(
        200 => '200 OK',
        201 => '201 Created',
        204 => '204 No content',
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        404 => '404 Not found',
        422 => 'Unprocessable Entity',
        500 => '500 Internal Server Error',
    );
    // ok, validation error, or failure
    header('Status: ' . $status[$code]);
    // return the encoded json

    if ($objRespuesta != null) {

        $respuesta = [
            'status' => $code, // success or not?
            'message' => $message,
            'data' => $objRespuesta,
        ];
    } else {
        $respuesta = [
            'status' => $code, // success or not?
            'message' => $message,
        ];
    }

    echo json_encode($respuesta);
}
