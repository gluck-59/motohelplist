<?php
    session_start();
//    error_reporting(E_ALL);
//    include_once('../config/config.inc.php');
//    include_once('functions.php');
//    if (!isset($smarty))
//	exit('Smarty умер');

header('Location: http:/'.$_SERVER['REQUEST_URI']);
// нужен один слеш, потому что какого-то хуя в request uri передается один начальный слеш


/*
echo '<pre>';
print_r($_SERVER);
die;
*/

?>