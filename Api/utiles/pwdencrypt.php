<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE');
error_reporting(E_ALL ^ E_NOTICE);
// Md5 etlsynapsis
$secret = '0ee769f8576fd36154cdd310872c9e0d';
$method = 'aes128';
$iv = "1234567887654321";



function encrypt($pwd)
{
	$passwordEncriptada = openssl_encrypt($pwd, $GLOBALS['method'], $GLOBALS['secret'], false, $GLOBALS['iv']);

	return $passwordEncriptada;
	// echo $passwordEncriptada;
	// decrypt($passwordEncriptada);method
}

function decrypt($pwd)
{
	$passwordDesEncriptada = openssl_decrypt($pwd, $GLOBALS['method'], $GLOBALS['secret'], false, $GLOBALS['iv']);

	return $passwordDesEncriptada;

	// echo $passwordDesEncriptada;

}
