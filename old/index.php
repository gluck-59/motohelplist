<?php
include_once('../../config/config.inc.php');
session_start();

// статистика кликов по меню
if ($_POST['plusone'])
{
    $url = $_POST['plusone'];
    $stmt = $pdo->prepare('INSERT INTO `click_stat` (`url`, `count`) VALUES (:url, `count`+1) ON DUPLICATE KEY UPDATE `count` = `count`+1');
    $stmt->execute(array(':url' => $url));
    return false;
}


/* */
/* определим страну откуда зашел юзер
/* */
include_once('../../tools/SxGeo/SxGeo.php');
    $SxGeo = new SxGeo('../../tools/SxGeo/SxGeo.dat');
    $ip = $_SERVER['REMOTE_ADDR'];
    if ($SxGeo->get($ip))
    {
        setcookie("currentCountry", strtolower($SxGeo->get($ip)), time()+86400);  /* срок действия 1 сутки */
    }

/* */
/* получим все доступные коды языков из браузера юзера
/* */
if (!$_COOKIE['language']) // только если у юзера не установлен язык из приложения
{
    require_once('lang.php');
    $sLanguage = tryToFindLang($aLanguages, $_SERVER['HTTP_ACCEPT_LANGUAGE'], 'en');
    if ($sLanguage)
    {
        setcookie("language", $sLanguage, time()+60*60*24*365);  /* срок действия 1 год */
    }
}




/*
print_r($_GET);
exit();    
*/



if (!isset($_SESSION['id_user'])) 
{
   echo "<script>localStorage.removeItem('phone'); window.open('login.html', '_self');</script>";   
//sleep(2);
//   header( 'Location: /login.html', true, 303 );    
   exit();
} 

elseif (isset($_GET['logout']))
{
    session_destroy();
    header( 'Location: /old/login.html', true, 303 );     
    exit();

}

else 
{
    // перенаправим юзера на основную страницу
    header( 'Location: /old/onair.php', true, 303 ); 
    exit();

}


?>