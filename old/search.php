<?php
include_once('../../config/config.inc.php');

$data = $_GET['data'];
$term = $_GET['term'];

switch ($data) {

    case "city":
        //if ($_COOKIE['language']) $lang = $_COOKIE['language'];
        //else $lang = 'en';
        $stmt = $pdo->query("SELECT id_city as id, CONCAT_WS(', ',cities.name, cities.region, countries.name_en) as label FROM cities 
        join countries on cities.id_country=countries.id_country 
        where cities.name LIKE ('%$term%') 
        order by cities.name
        "); 
    break;    
    


    case "phoneCode":
        $stmt = $pdo->query("SELECT phone_code
        FROM countries
        where 
        iso LIKE ('$term%') 
        "); 
    break;    



    case "motorcycle":
        $stmt = $pdo->query("SELECT id_motorcycle as id, name as label
        FROM motorcycles
        where name LIKE ('%$term%') 
/*        or manufacturer LIKE ('%$term%') */
        order by name
        "); 
    break;    
    
    

    case "name_channel":
        $stmt = $pdo->query("SELECT id_channel as id, name as label
        FROM webchat_channels
        where name LIKE ('%$term%') 
        and type_channel != 1 and type_channel != 3
        "); 
    break;        


    
    default:
       Die("error");
}

    while ($row = $stmt->fetch() )
    {
        
        $result[] = $row;
    }
print_r(json_encode($result));


//printf('%.2f сек', (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]));
//echo '<br><pre>'.sizeof($result).' шт.<br><br>';
//print_r($result);
//print_r(json_encode($result));

?>