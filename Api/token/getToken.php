<?php

include_once '../utiles/log.php';

function getTokenFileValue()
{

    $token = file_get_contents('../token/token.txt');
    return $token;
}


