<?
header('Content-type: image/jpg');
header('Pragma: public');
header('Cache-Control: max-age=86400');
header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
mb_internal_encoding("UTF-8");


function makeAvatar($id_user,$name) 
{
    $fontsize = 36;
    
    if (!$id_user OR !$name ) return;
    if (file_exists('img/avatar/'.$id_user.'.jpg')) 
    {
        $fp = fopen('img/avatar/'.$id_user.'.jpg', 'rb');
        fpassthru($fp);
        return; 
    }
        
    $color = array();
    for ($i = 0; $i < mb_strlen($name); $i++) 
    {
        $hash = ord($name[$i]) + (($hash << 5) - $hash);
    }
    
    for ($i = 0; $i < 3; $i++) 
    {
        $value = ($hash >> ($i * 8)) & 0xFF;
        ($value > 240 ? $value=240 : $value=$value);                
        $color[$i] =  $value;//dechex($value);
    }
    
    $ftext = explode(' ', $name);
    $name = mb_strtoupper(mb_substr($ftext[0],0,1).mb_substr($ftext[1],0,1));
    
    // размер изображения
    $img = imagecreatetruecolor(100, 100);
     
    // цвет фона
    $bg = imagecolorallocate($img, $color[0], $color[1], $color[2]);
    imagefilledrectangle($img, 0, 0, 100, 100, $bg);
     
    // шрифт
    $font = 'fonts/PTSansCaptionRegular.ttf';

    // цвет текста
    $black = imagecolorallocate($img, 250, 250, 250);
     
    // вычисляем сколько места займёт текст
    $bbox = imageftbbox($fontsize, 0, $font, $name);
     
    $x = $bbox[0] + (imagesx($img) / 2) - ($bbox[4] / 2);// - 5;
    $y = $bbox[1] + (imagesy($img) / 2) - ($bbox[5] / 2);// - 5; 

    // добавляем текст на изображение
    imagefttext($img, $fontsize, 0, $x, $y, $black, $font, $name);
     
    // выводим изображение
    imagejpeg($img,NULL,100);

    // освобождаем память
    imagedestroy($img);
} 

if (!$_GET['name']) $_GET['name'] = '00';
makeAvatar($_GET['id_user'], $_GET['name']);

?>