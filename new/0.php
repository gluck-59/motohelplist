<?php
header('X-Accel-Buffering: no');
ob_get_flush();


$arr1 = array(
'http://ro-moto.com/img/items/photo/29/1.jpg',
'http://ro-moto.com/img/items/photo/9/4.jpg',
'http://ro-moto.com/img/items/photo/22/1.jpg'
);

$arr = array(
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/2/2.jpg',
'http://ro-moto.com/img/items/photo/16/1.jpg',
'http://ro-moto.com/img/items/photo/5/4.jpg',
'http://ro-moto.com/img/items/photo/17/1.jpg',
'http://ro-moto.com/img/items/photo/4/6.jpg',
'http://ro-moto.com/img/items/photo/18/1.jpg',
'http://ro-moto.com/img/items/photo/3/7.jpg',
'http://ro-moto.com/img/items/photo/13/4.jpg',
'http://ro-moto.com/img/items/photo/20/1.jpg',
'http://ro-moto.com/img/items/photo/9/4.jpg',
'http://ro-moto.com/img/items/photo/10/2.jpg',
'http://ro-moto.com/img/items/photo/11/3.jpg',
'http://ro-moto.com/img/items/photo/12/3.jpg',
'http://ro-moto.com/img/items/photo/14/1.jpg',
'http://ro-moto.com/img/items/photo/15/1.jpg',
'http://ro-moto.com/img/items/photo/24/1.jpg',
'http://ro-moto.com/img/items/photo/2/2.jpg',
'http://ro-moto.com/img/items/photo/16/1.jpg',
'http://ro-moto.com/img/items/photo/5/4.jpg',
'http://ro-moto.com/img/items/photo/17/1.jpg',
'http://ro-moto.com/img/items/photo/4/6.jpg',
'http://ro-moto.com/img/items/photo/18/1.jpg',
'http://ro-moto.com/img/items/photo/7/4.jpg',
'http://ro-moto.com/img/items/photo/3/7.jpg',
'http://ro-moto.com/img/items/photo/19/1.jpg',
'http://ro-moto.com/img/items/photo/13/4.jpg',
'http://ro-moto.com/img/items/photo/20/1.jpg',
'http://ro-moto.com/img/items/photo/9/4.jpg',
'http://ro-moto.com/img/items/photo/11/3.jpg',
'http://ro-moto.com/img/items/photo/12/3.jpg',
'http://ro-moto.com/img/items/photo/14/1.jpg',
'http://ro-moto.com/img/items/photo/15/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/2/2.jpg',
'http://ro-moto.com/img/items/photo/16/1.jpg',
'http://ro-moto.com/img/items/photo/6/6.jpg',
'http://ro-moto.com/img/items/photo/5/4.jpg',
'http://ro-moto.com/img/items/photo/17/1.jpg',
'http://ro-moto.com/img/items/photo/4/6.jpg',
'http://ro-moto.com/img/items/photo/18/1.jpg',
'http://ro-moto.com/img/items/photo/3/7.jpg',
'http://ro-moto.com/img/items/photo/13/4.jpg',
'http://ro-moto.com/img/items/photo/20/1.jpg',
'http://ro-moto.com/img/items/photo/9/4.jpg',
'http://ro-moto.com/img/items/photo/10/2.jpg',
'http://ro-moto.com/img/items/photo/11/3.jpg',
'http://ro-moto.com/img/items/photo/12/3.jpg',
'http://ro-moto.com/img/items/photo/14/1.jpg',
'http://ro-moto.com/img/items/photo/15/1.jpg',
'http://ro-moto.com/img/items/photo/24/1.jpg',
'http://ro-moto.com/img/items/photo/2/2.jpg',
'http://ro-moto.com/img/items/photo/16/1.jpg',
'http://ro-moto.com/img/items/photo/6/6.jpg',
'http://ro-moto.com/img/items/photo/5/4.jpg',
'http://ro-moto.com/img/items/photo/17/1.jpg',
'http://ro-moto.com/img/items/photo/4/6.jpg',
'http://ro-moto.com/img/items/photo/18/1.jpg',
'http://ro-moto.com/img/items/photo/7/4.jpg',
'http://ro-moto.com/img/items/photo/3/7.jpg',
'http://ro-moto.com/img/items/photo/19/1.jpg',
'http://ro-moto.com/img/items/photo/13/4.jpg',
'http://ro-moto.com/img/items/photo/20/1.jpg',
'http://ro-moto.com/img/items/photo/9/4.jpg',
'http://ro-moto.com/img/items/photo/11/3.jpg',
'http://ro-moto.com/img/items/photo/12/3.jpg',
'http://ro-moto.com/img/items/photo/14/1.jpg',
'http://ro-moto.com/img/items/photo/15/1.jpg',
'http://ro-moto.com/img/items/photo/24/1.jpg',
'http://ro-moto.com/img/items/photo/2/2.jpg',
'http://ro-moto.com/img/items/photo/16/1.jpg',
'http://ro-moto.com/img/items/photo/6/6.jpg',
'http://ro-moto.com/img/items/photo/5/4.jpg',
'http://ro-moto.com/img/items/photo/17/1.jpg',
'http://ro-moto.com/img/items/photo/4/6.jpg',
'http://ro-moto.com/img/items/photo/18/1.jpg',
'http://ro-moto.com/img/items/photo/3/7.jpg',
'http://ro-moto.com/img/items/photo/19/1.jpg',
'http://ro-moto.com/img/items/photo/13/4.jpg',
'http://ro-moto.com/img/items/photo/20/1.jpg',
'http://ro-moto.com/img/items/photo/9/4.jpg',
'http://ro-moto.com/img/items/photo/11/3.jpg',
'http://ro-moto.com/img/items/photo/12/3.jpg',
'http://ro-moto.com/img/items/photo/14/1.jpg',
'http://ro-moto.com/img/items/photo/24/1.jpg',
'http://ro-moto.com/img/items/photo/7/4.jpg',
'http://ro-moto.com/img/items/photo/15/1.jpg',
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/5/4.jpg',
'http://ro-moto.com/img/items/photo/17/1.jpg',
'http://ro-moto.com/img/items/photo/4/6.jpg',
'http://ro-moto.com/img/items/photo/18/1.jpg',
'http://ro-moto.com/img/items/photo/15/1.jpg',
'http://ro-moto.com/img/items/photo/22/1.jpg',
'http://ro-moto.com/img/items/photo/5/4.jpg',
'http://ro-moto.com/img/items/photo/17/1.jpg',
'http://ro-moto.com/img/items/photo/4/6.jpg',
'http://ro-moto.com/img/items/photo/18/1.jpg',
'http://ro-moto.com/img/items/photo/7/4.jpg',
'http://ro-moto.com/img/items/photo/15/1.jpg',
'http://ro-moto.com/img/items/photo/7/4.jpg',
'http://ro-moto.com/img/items/photo/29/1.jpg',
'http://ro-moto.com/img/items/photo/15/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/32/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/32/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/25/3.jpg',
'http://ro-moto.com/img/items/photo/27/1.jpg',
'http://ro-moto.com/img/items/photo/34/1.jpg',
'http://ro-moto.com/img/items/photo/33/1.jpg',
'http://ro-moto.com/img/items/photo/26/4.jpg',
'http://ro-moto.com/img/items/photo/29/1.jpg',
'http://ro-moto.com/img/items/photo/31/1.jpg',
'http://ro-moto.com/img/items/photo/32/1.jpg',
'http://ro-moto.com/img/items/photo/28/1.jpg',
'http://ro-moto.com/img/items/photo/30/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/32/1.jpg',
'http://ro-moto.com/img/items/photo/22/1.jpg',
'http://ro-moto.com/img/items/photo/25/3.jpg',
'http://ro-moto.com/img/items/photo/27/1.jpg',
'http://ro-moto.com/img/items/photo/34/1.jpg',
'http://ro-moto.com/img/items/photo/33/1.jpg',
'http://ro-moto.com/img/items/photo/26/4.jpg',
'http://ro-moto.com/img/items/photo/29/1.jpg',
'http://ro-moto.com/img/items/photo/31/1.jpg',
'http://ro-moto.com/img/items/photo/32/1.jpg',
'http://ro-moto.com/img/items/photo/30/1.jpg',
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/34/1.jpg',
'http://ro-moto.com/img/items/photo/26/4.jpg',
'http://ro-moto.com/img/items/photo/29/1.jpg',
'http://ro-moto.com/img/items/photo/31/1.jpg',
'http://ro-moto.com/img/items/photo/30/1.jpg',
'http://ro-moto.com/img/items/photo/34/1.jpg',
'http://ro-moto.com/img/items/photo/26/4.jpg',
'http://ro-moto.com/img/items/photo/29/1.jpg',
'http://ro-moto.com/img/items/photo/31/1.jpg',
'http://ro-moto.com/img/items/photo/30/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/34/1.jpg',
'http://ro-moto.com/img/items/photo/29/1.jpg',
'http://ro-moto.com/img/items/photo/31/1.jpg',
'http://ro-moto.com/img/items/photo/30/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/34/1.jpg',
'http://ro-moto.com/img/items/photo/29/1.jpg',
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/24/1.jpg',
'http://ro-moto.com/img/items/photo/29/1.jpg',
'http://ro-moto.com/img/items/photo/30/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/21/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/23/1.jpg',
'http://ro-moto.com/img/items/photo/22/1.jpg',
'http://ro-moto.com/img/items/photo/22/1.jpg'
);

foreach ( $arr as $key=>$value)
{
    echo $key.',';
    ob_get_flush();
        
    $path = (str_ireplace(basename($value), '', $value));
    
    $string = array();
    $string[] = $value;    
    
    $cover = substr($value, -5, 1); 
    for ($i=1; $i<=8; $i++)
    {
        $headers = get_headers($path.$i.'.jpg');
         if ( strripos($headers[0], "200") and $i != $cover)
            {
                $string[] = $path.$i.'.jpg';
            }
    }
    echo implode("|", $string);
    echo('<br>');    
    ob_get_flush();
}


?>
