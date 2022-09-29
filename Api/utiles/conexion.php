<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE');
error_reporting(E_ALL ^ E_NOTICE);

// conexionDB();

function conexionDB()
{
    
    $ini = parse_ini_file('../config.ini', true);
 
    $campaigns = $ini['Conexion_BD']['servername'];

    $servername = $ini['Conexion_BD']['servername'];
    $username = $ini['Conexion_BD']['username'];
    $password = $ini['Conexion_BD']['password'];
    $dbname = $ini['Conexion_BD']['dbname'];

    $mysqli = new mysqli("$servername", "$username", "$password", "$dbname");
    
    if (mysqli_connect_errno()) {
        json_response(500, "Error al conectarse a la base de datos");
        escribirLog("Error al conectarse a la base de datos: error " . mysqli_connect_errno());
        exit();
    } else {

        return $mysqli;
    }
}
