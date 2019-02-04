<?php
session_start();

$img = $_POST['file'];
if ($_SESSION['id_user']) $id_user = $_SESSION['id_user'];
else $id_user = '-000000000-no-session';

// ПЕРЕДЕЛАТЬ ЭТОТ ЕБАНЫЙ ПОЗОР
$img = str_replace('data:image/jpeg;base64,', '', $img);
//$img = str_replace(' ', '+', $img);
// ПЕРЕДЕЛАТЬ ЭТОТ ЕБАНЫЙ ПОЗОР

$fileData = base64_decode($img);
$fileName = "img/avatar/$id_user.jpg";

if (file_put_contents($fileName, $fileData))
    echo 'Аватар загружен';

else echo 'Ошибка загрузки аватара. Попробуйте позже.';

?>