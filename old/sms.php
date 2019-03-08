<?php 

if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) { 
    header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); 
    die (); 
}

/////////////////////////////////////////////////////////////
/* 
/*  НИЧЕГО НЕ ЭХАТЬ, ВСЕ ВЫВОДИТСЯ В КЛИЕНТА
/*  ТОЛЬКО ПУШ И EMAIL
/*
/*  инклюдить скрипт с заранее известными нужными переменными:
/*  $code AND $phone - отправка смс
/*  $balance - проверка баланса
/*  $sms_id - статус отправки смс с заданным id
/*
/////////////////////////////////////////////////////////////*/


//echo $code;
//echo $phone;
//die;


$host = 'api.smsfeedback.ru';
$port = 80;
$login = 'gluck59';
$password = 'gjhyj1';
//$phone = '79028013696'; не включать, придет из login.php
$text = $code.' - пароль для входа в Moto Helplist';
$sender = 'Helplist';
$wapurl = false;
$ip = $_SERVER['HTTP_X_REAL_IP'];


 
/* 
* использование функций
*/

if ($code and $phone)  // если при инклюде скрипта есть И $code И $phone, то это запрос на отсылку смс
{
    $send = send($host, $port, $login, $password, $phone, $text, $sender = false, $wapurl = false );
    //$send = send_sms($host, $port, $login, $password, $phone, $text, $sender = false, $wapurl = false ); Ali - не работает
}
    

if ($balance)  // если при инклюде скрипта есть $balance, то это проверка баланса
{    
    $balance = balance($host, $port, $login, $password);
}
 
if ($sms_id)  // если при инклюде скрипта есть $sms_id, то это проверка статуса
{
    $status = status($host, $port, $login, $password, $sms_id);
} 
 
 
 ////////////////////////////////////

 
/* функция отправки смс */

function send_sms($host, $port, $login, $password, $phone, $text, $sender = false, $wapurl = false ) {
    curl_setopt_array($ch = curl_init(), array(
    CURLOPT_URL => "http://".$host."/messages/v2/send/".
        "?phone=" . rawurlencode($phone) .
        "&text=" . rawurlencode($text) .
        ($sender ? "&sender=" . rawurlencode($sender) : "") .
        ($wapurl ? "&wapurl=" . rawurlencode($wapurl) : ""),
//      CURLOPT_SAFE_UPLOAD => true, // выдаст респонс в морду клиента - не включать
      CURLOPT_RETURNTRANSFER => true
      //CURLOPT_HEADER => true
    ));
    $a = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    if (empty($info['http_code']) || $info['http_code']!=200)
      return false;
     else 
      return $a;
}

function send($host, $port, $login, $password, $phone, $text, $sender = false, $wapurl = false )
{
    $fp = fsockopen($host, $port, $errno, $errstr);
    if (!$fp) {
        return "errno: $errno \nerrstr: $errstr\n";
    }
    fwrite($fp, "GET /messages/v2/send/" .
        "?phone=" . rawurlencode($phone) .
        "&text=" . rawurlencode($text) .
        ($sender ? "&sender=" . rawurlencode($sender) : "") .
        ($wapurl ? "&wapurl=" . rawurlencode($wapurl) : "") .
        "  HTTP/1.0\n");  
    fwrite($fp, "Host: " . $host . "\r\n");
    if ($login != "") {
        fwrite($fp, "Authorization: Basic " . 
            base64_encode($login. ":" . $password) . "\n");
    }
    fwrite($fp, "\n");
    $response = "";
    while(!feof($fp)) {
        $response .= fread($fp, 1);
    }
    fclose($fp);
    list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
    return $responseBody;
}




/* функция проверки состояния отправленного сообщения */

function status($host, $port, $login, $password, $sms_id)
{
    $fp = fsockopen($host, $port, $errno, $errstr);
    if (!$fp) {
        return "errno: $errno \nerrstr: $errstr\n";
    }
    fwrite($fp, "GET /messages/v2/status/" .
        "?id=" . $sms_id .
        "  HTTP/1.0\n");
    fwrite($fp, "Host: " . $host . "\r\n");
    if ($login != "") {
        fwrite($fp, "Authorization: Basic " . 
            base64_encode($login. ":" . $password) . "\n");
    }
    fwrite($fp, "\n");
    $response = "";
    while(!feof($fp)) {
        $response .= fread($fp, 1);
    }
    fclose($fp);
    list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
    return $responseBody;
}
 



/* функция проверки баланса */
 
function balance($host, $port, $login, $password)
{
    $fp = fsockopen($host, $port, $errno, $errstr);
    if (!$fp) {
        return "errno: $errno \nerrstr: $errstr\n";
    }
   fwrite($fp, "GET /messages/v2/balance/  HTTP/1.0\n");
    fwrite($fp, "Host: " . $host . "\r\n");
    if ($login != "") {
        fwrite($fp, "Authorization: Basic " . 
            base64_encode($login. ":" . $password) . "\n");
    }
    fwrite($fp, "\n");
    $response = "";
    while(!feof($fp)) {
        $response .= fread($fp, 1);
    }
    fclose($fp);
    list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
    return $responseBody;
}




// в конце сообщим мне ответ от сервера
push( 
($phone     ? '+Тел. '.$phone.' ('.$code.')': '').
($send      ? ' , '.$send : '').
($balance   ? 'Баланс '.$balance : '').
($status    ? $status : '').
($ip        ? ', '.$ip : '')
);
function push($text)
{
    curl_setopt_array($ch = curl_init(), array(
    CURLOPT_URL => "https://pushall.ru/api.php",
    CURLOPT_POSTFIELDS => array(
        "type" => "self",
        "id" => "1105",
        "key" => "65d28daa7a2f17944bbde01f49e70c53",
        "text" => "$text",
        "icon" => "//app.motohelplist.com/img/logo_152.png",
        "title" => "Moto Helplist"//date("Y-m-d H:i:s") 
      ),
//      CURLOPT_SAFE_UPLOAD => true, // выдаст респонс в морду клиента - не включать
      CURLOPT_RETURNTRANSFER => true
    ));
    $a = curl_exec($ch); 
    curl_close($ch);
    return false;
//    return $a;
}

?>