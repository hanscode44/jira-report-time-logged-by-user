<?php

include 'autoloader.php';

// set config settings
autoloader(array(array(
    'basepath' => '.', // basepath is used to define where your project is located
    // more config settings here as needed
)));

// now we can set class autoload paths
autoloader(array(
    'classes',
    // more paths here as needed
));


function arrayPrint($array)
{
    echo "<pre>";
    var_dump($array);
    die;
}
