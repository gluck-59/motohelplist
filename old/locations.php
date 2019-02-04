<?
error_reporting(E_ALL);
include_once('../config/config.inc.php');
//header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Methods: GET, POST');
//header('Access-Control-Max-Age: 1000');

//$text = print_r($_COOKIE,true);
//$text = file_get_contents('php://input');


//$text = print_r($_POST['data']['location']['coords'],true);

//$text .= print_r($_GET,true); 
//$coord = print_r($_POST['data']['location']['coords'], true);

/*    
    postLocation: function (data) {
        console.log('[стр.511] '+JSON.stringify(data));
        return $.ajax({
            url: app.postUrl,
            method: 'POST',
            crossDomain: true,
            type: 'POST',
//            data: {data : JSON.stringify(data)},
            data: {data : data},
            dataType: 'html',
            contentType: 'application/x-www-form-urlencoded'
        });
    },
*/

if ($_POST['data'])
{

    $coord = $_POST['data'];
//    $debug = $_POST['data']['debug'];
    
    
    $id_track = 2;
    $id_user = 2;
    $track_name = 'test_'.date("H:i:s");
    $lat = $coord['0'];
    $lng = $coord['1'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $rawdata = print_r($_POST['data'], true);

    
    // пишем координаты в базу
        $stmt = $pdo->prepare('INSERT INTO `tracks`(`id_track`, `id_user`, `track_name`, `lat`, `lng`, `ip`, `timestamp`, `rawdata`) VALUES (:id_track, :id_user, :track_name, :lat, :lng, :ip, NOW(), :rawdata )');
        $stmt->execute(array(
        ':id_track' => $id_track, 
        ':id_user' => $id_user,     
        ':track_name' => $track_name,         
        ':lat' => $lat, 
        ':lng' => $lng, 
        ':ip' => $ip,
        ':rawdata' => $rawdata
        ));




        curl_setopt_array($ch = curl_init(), array(
        CURLOPT_URL => "https://pushall.ru/api.php",
        CURLOPT_POSTFIELDS => array(
            "type" => "broadcast",
            "id" => "74",
            "key" => "1adbcb37cc049e1b9115936ec1747c9b",
            "text" => "$track_name",
            "title" => "Moto Helplist ".date("Y-m-d H:i:s") 
          ),
    //      CURLOPT_SAFE_UPLOAD => true, // выдаст респонс в морду клиента - не включать
          CURLOPT_RETURNTRANSFER => true
        ));
        $a = curl_exec($ch); 
        curl_close($ch);
 }




 
 elseif ($_POST['task'] == 'getTrack')
 {
    $stmt = $pdo->query('SELECT track_name, lat, lng, accuracy, timestamp 
    FROM tracks
/*    WHERE id_track = 1 */
    ');
    $tracks = $stmt->fetchAll();
    echo "<?xml version=\"1.0\"?>\r\n<markers>"; 
    
    foreach ($tracks as $track) {
      echo "<marker>\r\n";
        echo "<track_name>{$track->track_name}</track_name>\r\n";      
        echo "<lat>{$track->lat}</lat>\r\n";
        echo "<lng>{$track->lng}</lng>\r\n";
        echo "<accuracy>{$track->accuracy}</accuracy>\r\n";        
        echo "<timestamp>{$track->timestamp}</timestamp>\r\n";    
      echo "</marker>\r\n"; 
    }
    
    echo '</markers>';
 }
 
 
 
else
{
    
    $stmt = $pdo->query('SELECT track_name, lat, lng, accuracy, timestamp
    FROM tracks ORDER BY timestamp DESC
    /*    WHERE id_track = 4*/
    ');
    $tracks = $stmt->fetchAll();
    echo "<html><table width='600px'><thead>
    <td>track_name</td><td>lat</td><td>lng</td><td>timestamp</td>
    </thead>
    "; 
    
    foreach ($tracks as $track) {
    echo "<tbody><tr>";
    echo "<td>{$track->track_name}</td>";
    echo "<td>{$track->lat}</td>";
    echo "<td>{$track->lng}</td>";
    echo "<td>{$track->timestamp}</td>";        
    echo "</tr>"; 
    }
    
    echo '</tbody></table>';
}
 
    
?>