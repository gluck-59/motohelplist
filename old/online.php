<?php
    header("Access-Control-Allow-Origin: *");

    include_once('../../tools/SxGeo/SxGeo.php');
    $SxGeo = new SxGeo('../../tools/SxGeo/SxGeo.dat');
    $ip = $_SERVER['REMOTE_ADDR'];
    if ( $country = strtolower($SxGeo->get($ip)) )
    {
        setcookie("currentCountry", $country, time()+86400);  /* срок действия 1 сутки */
    }
    echo 'online'.time();
    
?>