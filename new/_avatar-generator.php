<?php
    
// вход - юзеры у которых есть аватары через запятую
// выход - файлы аватаров
    
error_reporting(E_ALL);
include_once('../config/config.inc.php');
mb_internal_encoding("UTF-8");



echo '<pre>';


$dir    = 'img/avatar/';
$files = scandir($dir);

foreach ($files as $file)
{
    $file = str_ireplace('.jpg', ',', $file);
    echo($file);
}
die;





$stmt = $pdo->prepare('SELECT id_user, name FROM `users`
where `id_user` not in (1,2,3,5053,5054,5055,5057,5058,5059,5061,5064,5066,5067,5068,5071,5072,5076,5077,5078,5080,5082,5083,5084,5086,5088,5089,5092,5093,5094,5097,5098,5101,5102,5105,5109,5110,5111,5116,5129,5130,5131,5135,5136,5137,5138,5139,5141,5142,5143,5148,5150,5157,5158,5161,5168,5172,5174,5175,5184,5189,5193,5202,5203,5206,5208,5211,5222,5226,5228,5229,5234,5241,5244,5245,5249,5250,5253,5255,5260,5269,5271,5272,5275,5276,5277,5279,5291,5292,5293,5294,5296,5301,5303,5304,5308,5309,5310,5311,5313,5317,5318,5320,5323,5324,5326,5331,5337,5340,5343,5356,5362,5372,5373,5374,5380,5381,5386,5387,5391,5395,5402,5404,5405,5412,5420,5428,5431,5432,5439,5440,5461,5465,5479,5497,5498,5501,5502,5508,5512,5514,5519,5521,5528,5530,5536,5537,5540,5541,5570,5581,5584,5589,5591,5592,5593,5595,5597,5600,5601,5603,5608,5609,5611,5612,5616,5619,5620,5624,5625,5626,5639,5641,5642,5643,5646,5649,5650,5651,5657,5659,5664,5665,5666,5667,5668,5669,5670,5671,5672,5673,5674,5675,5676,851,5062,5063,5065,5069) order by :id_user  ');

$stmt->execute(array(':id_user' => "id_user"));
$files = $stmt->fetchAll();



foreach ($files as $file)
{
    echo($id_user = $file->id_user);
    echo '<br>';
    echo($name = $file->name);
    echo '<br>';    
    
    makeAvatar($id_user,$name);
}



function makeAvatar($id, $name = 0)
{
    $fontsize = 36;
    
    if (file_exists('img/avatar/'.$id.'.jpg')) 
    {
        echo ('<a href="//app.motohelplist.com/img/avatar/'.$id.'.jpg">link</a><br><br>');

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
     
    // выводим изображение в файл
    imagejpeg($img,'img/avatar/'.$id.'.jpg',75);
    $response = Array('avatar' => "//app.motohelplist.com/img/avatar/".$id.".jpg");

    echo ('<a href="//app.motohelplist.com/img/avatar/'.$id.'.jpg">link</a><br><br>');
    
    // освобождаем память
    imagedestroy($img);
}


?>




