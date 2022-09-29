<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE');
error_reporting(E_ALL ^ E_NOTICE);

include 'conexion.php';
include 'log.php';
include 'jwt_utils.php';
include 'pwdencrypt.php';
include 'respuestaJSON.php';
