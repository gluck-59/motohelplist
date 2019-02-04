<?php

/* Database Configuration. Add your details below */

$dbOptions = array(
	'db_host' => 'localhost',
	'db_user' => 'motokofr_dbuser', // юзер с ограниченными правами для уменьшения checking_permissions
	'db_pass' => 'Ji)ouDR2K!1}',
	'db_name' => 'motokofr_motohelplist'
);

/* Database Config End */

error_reporting(E_ALL ^ E_NOTICE);

require "../../classes/DB.class.php";
require "../../classes/Chat.class.php";
require "../../classes/ChatBase.class.php";
//require "../../classes/ChatLine.class.php";
//require "../../classes/ChatUser.class.php";

include_once('../../config/config.inc.php');
//session_name('webchat');
session_start();
if (!isset($_SESSION['id_user']))
{
 echo "Session not found";
 exit;
}

if(get_magic_quotes_gpc()){
	
	// If magic quotes is enabled, strip the extra slashes
	array_walk_recursive($_GET,create_function('&$v,$k','$v = stripslashes($v);'));
	array_walk_recursive($_POST,create_function('&$v,$k','$v = stripslashes($v);'));
}

try{
	
	// Connecting to the database
	DB::init($dbOptions);
	
	$response = array();
	
	// Handling the supported actions:


// если юзер запросил канал тип 3 на который не подписан, выдадим ему пустой экран	
if ($_GET['channel']) {
if ($_GET['channel']!=0) {
$result = DB::query('SELECT DISTINCT 
CASE 
WHEN wc.type_channel=3 
THEN (select id from webchat_subscribe ws where ws.id_channel='.$_GET['channel'].' and ws.id_user='.$_SESSION['id_user'].') ELSE 1 END as type_channel  
FROM `webchat_channels` wc where wc.id_channel='.$_GET['channel']);
$access = $result->fetch_assoc();	    
if (!$access['type_channel']) {
//echo '{}';
//exit();
die(json_encode(array('error' => 'nodata')));
}
}
}

	switch($_GET['action']){
		
		case 'login':
			$response = Chat::login($_POST['name'],''/*$_POST['email']*/);
		break;
		
		case 'checkLogged':
			$response = Chat::checkLogged($_GET['channel'], $_GET['id_to']);
		break;
		
		case 'logout':
			$response = Chat::logout();
		break;
		
		case 'submitChat':
			$response = Chat::submitChat($_GET['channel'],$_POST['id_to'],$_POST['chatText']);
		break;
		
		case 'getUsers':
			$response = Chat::getUsers();
		break;
		
		case 'getChats':
			$response = Chat::getChats($_GET['lastID'],$_GET['channel'],$_GET['id_from'],$_GET['getold']);
		break;

		case 'setRead':
			$response = Chat::setRead($_POST['msgID']);
		break;
		
		case 'getUnread':
			$response = Chat::getUnread($_POST['id_user']);
		break;		

/*
case 'getPrivateChats':
	$response = Chat::getPrivateChats($_GET['lastID'],$_GET['id_from']);
break;
*/		
		default:
		break;
//			throw new Exception('Wrong action');
	}
	
    if ($response) echo json_encode($response);
}
catch(Exception $e){
	die(json_encode(array('error' => $e->getMessage())));
}

?>