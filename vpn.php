<?php

$url = 'https://api.telegram.org/bot282576978:AAG4iW5xXo6LeG1oDc1CI408c344Q1d-pLU/getMe';
$proxyauth = 'gluck:gjhyj';
$proxy = 'opengluck.online';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
//proxy suport
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
//https
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 100);
       
$output = curl_exec($ch);   
curl_close($ch);

print_r($output);

?>