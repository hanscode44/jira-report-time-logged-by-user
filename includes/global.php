<?php

include 'autoloader.php';
include "src/autoload.php";

session_start();
$error = [];

checkForSettings();

include_once "header.php";

// set config settings
autoloader(
    array(
        array(
            'basepath' => '.', // basepath is used to define where your project is located
        )
    )
);

// now we can set class autoload paths
autoloader(
    array(
        'classes',
    )
);

function checkForSettings(){
    global $error;

    if(!file_exists('settings.php')){
        $error[] =  "Settingsfile does not exist, please copy settings_demo.php to settings.php and modify it to your needs";
    }
}

function arrayPrint($array,$end = false)
{
    echo "<pre>";
    print_r($array);

    if($end) {
        die;
    }
}
