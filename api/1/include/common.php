<?php
//require_once '../vendor/autoload.php';

require_once 'OAuthStore.php';
require_once 'OAuthServer.php';

session_start();

// Add a header indicating this is an OAuth server
header('X-XRDS-Location: http://' . $_SERVER['SERVER_NAME'] .
     '/api/1/public/services.xrds.php');

// Connect to database
$db = new PDO('mysql:host=localhost;dbname=motokofr_motohelplist', 'motokofr_dbuser', 'Ji)ouDR2K!1}');

// Create a new instance of OAuthStore and OAuthServer
$store = OAuthStore::instance('PDO', array('conn' => $db));
$server = new OAuthServer();
