<?php

include '../utiles/log.php';
test();

function test(){

    escribir_log("Test");
    echo($_SERVER['DOCUMENT_ROOT']);
}
