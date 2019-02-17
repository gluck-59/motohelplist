<?php
//error_reporting(E_ALL);
//ini_set('display_errors','On');
//ini_set('default_charset', 'utf-8');


// Kickstart the framework
$f3=require('lib/base.php');
include_once('lib/jwt.php');

// selectel API
include ("../selectel/SelectelStorage.php");
include ("../selectel/SelectelContainer.php");
include ("../selectel/SelectelStorageException.php");
include ("../selectel/SCurl.php");

    
$secret = 'test';

$f3->set('DEBUG',0);
$f3->set('CACHE',TRUE);

if ((float)PCRE_VERSION<7.9)
	trigger_error('PCRE version is out of date');

// Load configuration
$f3->config('config.ini');


$f3->set('DB', new \DB\SQL('mysql:host=localhost;port=3306;dbname=motohelplist','root','NhbUdjplz',array(
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC // generic attribute
)));

//print_r($f3->get('DB')->schema('oauth_users'));
/*
class User extends \DB\SQL\Mapper {
	public function __construct() {
		parent::__construct( \Base::instance()->get('DB'), 'oauth_users' );
	}
}

$user = new User();
$user->get('id');

var_dump($user);
*/


//! фильтры
function processFilters($query,$filters,$allowFields = array(), $default_filter = NULL) {

//$allowFields = array('ts'=>'FROM_UNIXTIME(%)');
$allowOperators = array('equal'=>'=','notequal'=>'!=','greater'=>'>','less'=>'<','greaterequal'=>'>=','lessequal'=>'<=','like'=>'like');
//$allowFilters = array('filter'=>3,'results'=>1,'skip'=>1);

$filter = array();	

$def_filter = array('%skip%'=>0,'%results%'=>50,'%order%'=>'ASC','%filter%'=>'');//,':filter'=>'') ;		


if ($default_filter) {
 foreach($def_filter as $key=>$value) {
    if ($default_filter[$key]) $def_filter[$key]=$default_filter[$key];
}
   
}
  

parse_str($filters,$filter);



foreach ($filter as $key=>$value) {
    switch (strtoupper($key)) {
        case 'FILTER':
              $tokens = explode(" ", $value);
//              print_r($tokens);
              if (count($tokens)!=3) {echo 'count missmatch';break;}
              if (!array_key_exists(strtolower($tokens[0]), $allowFields)) {echo 'field not allowed';break;}
              if (!array_key_exists(strtolower($tokens[1]), $allowOperators)) {echo 'operator not allowed';break;}
              $tokens[1]=$allowOperators[strtolower($tokens[1])];

//              print_r($allowFields[strtolower($tokens[0])]);

              if (is_array($allowFields[strtolower($tokens[0])]))
                $def_filter['%filter%']='and '.$allowFields[strtolower($tokens[0])][1].' '.$tokens[1].str_replace('&',$tokens[2],$allowFields[strtolower($tokens[0])][0]);
               else
              $def_filter['%filter%']='and '.$tokens[0].' '.$tokens[1].str_replace('&',$tokens[2],$allowFields[strtolower($tokens[0])]);
/*              ts=>array('unxitime','IF()')   
    
*/
        break;
        case 'SKIP':
              if (trim($value)=='' or !is_numeric($value)) break;
              $def_filter['%skip%']=$value;   
        break;
        case 'RESULTS':
              if (trim($value)=='' or !is_numeric($value)) break;
              $def_filter['%results%']=$value;   
        break;
        case 'ORDER':
              if (trim($value)=='' or (strtoupper($value)!='DESC' and strtoupper($value)!='ASC')) break;
              $def_filter['%order%']=$value;   
        break;

    }
}  
   
foreach($def_filter as $key=>$value) {
    $query = str_replace($key, $value, $query);
}

return $query;    
}






/*
//! личка - получить от конкретного юзера
*/
$f3->route('GET /users/@id/messages',
	function($f3,$params) 
	{
        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
        $querystr = $f3->get('QUERY'); 	

        $query = "SELECT * FROM 
            (SELECT id, id_from, id_to, text, users.name as name, users.gender, UNIX_TIMESTAMP(ts) as ts, IF(webchat_unreaded.id_lines, 1, 0) as unread
            FROM webchat_lines 
            JOIN users ON id_from=users.id_user
            LEFT JOIN webchat_unreaded ON (webchat_lines.id = webchat_unreaded.id_lines AND webchat_unreaded.id_user = $id )
            WHERE id_channel = 0 
            AND ((id_from = :id_from and id_to=:id_to) or (id_to = :id_from and id_from= :id_to))
            
            %filter%
            order by ts DESC LIMIT %skip%, %results%) t1

        order BY t1.ts %order%";
        
        $query = processFilters($query,$querystr,array('ts'=>'FROM_UNIXTIME(&)','text'=>' \'%&%\'','id'=>'&'));

        $rows = $f3->get('DB')->exec($query,array(':id_from'=>$params['id'],':id_to'=>$id));
        header("Content-Type: application/json");        
        echo json_encode($rows);
    }, 0 // кеширование в сек, больше нельзя
);



/*
//! личка — отправляет сообщение 
*/    
$f3->route('POST /users/@id/messages',
	function($f3,$params) 
	{
        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
        $data = json_decode($f3->get('BODY'),true);
        if (!isset($data) OR trim($data['message'])=='') 
               $f3->error('400','{"error":"empty data"}');
        try 
        {
            $user = $f3->get('DB')->exec('SELECT id_user from users where id_user=:id_user',array(':id_user'=>$params['id']));
            if (!isset($user[0]))
                   $f3->error('400','{"error":"bad user"}');
             
            $rows = $f3->get('DB')->exec("INSERT INTO webchat_lines (id_channel, id_from, id_to, text) values(0, :id_from, :id_to, :text)",
                array(':id_from' => $id, ':id_to' => $params['id'], ':text' => $data['message']));
    
            $response = $f3->get('DB')->exec('SELECT id, id_from, id_to, text, UNIX_TIMESTAMP(ts) as ts, 0 as id_channel FROM webchat_lines WHERE id = '.$f3->get('DB')->lastInsertId());
                echo json_encode($response);
                
            // сообщаем корреспонденту, что lastInsertId им непрочитана
            $unread = $f3->get('DB')->exec("INSERT INTO webchat_unreaded (id_lines, id_user) values(:id_line, :id_to)",
            array(':id_line' => $f3->get('DB')->lastInsertId(), ':id_to' => $params['id']));
                
        }
        catch (Exception $e) {
                $f3->error('500','{"error":"'.$e->getMessage().'"}');
        }
    });



//
//! каналы - получить сообщения
//
$f3->route('GET /channels/@id/messages',
	function($f3,$params) 
  	{
        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
    
        header("Content-Type: application/json");        
        	
        //проверка на приватный канал
        //проверка на существование канала
        $rows = $f3->get('DB')->exec('
        SELECT DISTINCT 
            CASE 
            WHEN wc.type_channel=3 
                THEN (select ws.id_channel from webchat_subscribe ws where ws.id_channel=:id_channel and ws.id_user=:id_user) 
            ELSE 
                wc.id_channel
            END 
            as id_channel  
        FROM `webchat_channels` wc where wc.id_channel=:id_channel',array(':id_channel'=>$params['id'],':id_user'=>$id));
    
       if (empty($rows[0]['id_channel']))
         {
                $f3->error('404','{"error":"channel not found"');
         }
        
            $querystr = $f3->get('QUERY');
            $query = "SELECT * 
            FROM (
                    SELECT id, id_from, users.gender, users.name as name, text, UNIX_TIMESTAMP(ts) as ts, IF(webchat_unreaded.id_lines, 1, 0) as unread
                    FROM webchat_lines 
                    JOIN users ON id_from=users.id_user
                    LEFT JOIN webchat_unreaded ON (webchat_lines.id = webchat_unreaded.id_lines AND webchat_unreaded.id_user = $id )

                    WHERE id_channel = :id_channel 
                    
                    %filter%
            
                    order by ts DESC LIMIT %skip%, %results%) t1
                    order BY t1.ts %order%";

            $query = processFilters($query,$querystr,array('ts' => 'FROM_UNIXTIME(&)','id'=>'&'));
            // print_r($query);
            $rows = $f3->get('DB')->exec($query,array(':id_channel'=>$params['id']));
        	echo json_encode($rows);
    }, 0 // кеширование в сек, больше нельзя
    );



//
//! каналы — отправляет сообщение
//
$f3->route('POST /channels/@id/messages',
	function($f3,$params) 
	{
        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
    
        $rows = $f3->get('DB')->exec('SELECT DISTINCT 
        CASE 
            WHEN wc.type_channel=3 
            THEN (select ws.id_channel from webchat_subscribe ws where ws.id_channel=:id_channel and ws.id_user=:id_user) 
            ELSE wc.id_channel
        END as id_channel  
        FROM `webchat_channels` wc where wc.id_channel=:id_channel',array(':id_channel'=>$params['id'],':id_user'=>$id));

        if (empty($rows[0]['id_channel']))
        {
            $f3->error('404','{"error":"channel not found"');
        }
    
        $data = json_decode($f3->get('BODY'),true);
        if (!isset($data) OR trim($data['message'])=='') 
            $f3->error('400','{"error":"empty data"}');

        try 
        {
            $rows = $f3->get('DB')->exec('INSERT INTO webchat_lines (id_channel, id_from, id_to, text) VALUES (:id_channel, :id_user, 0, :text)',array(':id_channel'=>$params['id'],':id_user'=>$id, ':text' => $data['message']));

            $response = $f3->get('DB')->exec('SELECT id, id_channel, id_from, text, UNIX_TIMESTAMP(ts) as ts FROM webchat_lines WHERE id = '.$f3->get('DB')->lastInsertId());

            $unread = $f3->get('DB')->exec("INSERT INTO webchat_unreaded (id_lines, id_user) SELECT :id_line, id_user from webchat_subscribe where id_channel = :id_channel and webchat_subscribe.id_user != :id_user",
    array(':id_line' => $f3->get('DB')->lastInsertId(), ':id_channel' => $params['id'],':id_user' => $id));
    
            echo json_encode($response);
        }
        catch (Exception $e) {
            $f3->error('500','{"error":"'.$e->getMessage().'"}');
        }
    }
);




//! messages - загрузка фотки
//
//
$f3->route('POST /img/@id_channel/@id_to',
function($f3,$params) 
{
    $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
    $file = $f3->get('BODY');
    try 
    {
        $newName = $id.'_'.date('d-m-Y_H-i-s');
        if (file_put_contents($newName.'.jpg', $file))
        {
            $selectelStorage = new SelectelStorage("54917_chatpics", "FnmTBSl3aU");
            $containerList = $selectelStorage->listContainers();
            $container = $selectelStorage->getContainer($containerList[0]);
        	$container->putFile(__DIR__.'/'.$newName.'.jpg', $newName.'.jpg');
        
            // запишем ссылку на картинку в базу
            $f3->get('DB')->exec('INSERT LOW_PRIORITY INTO webchat_lines  (id_channel, 	id_from, id_to, text, ts) values (:id_channel, :id_from, :id_to, :text, NOW() )',array(':id_channel' => $params['id_channel'], ':id_from' => $id, ':id_to' => $params['id_to'], ':text' => $selectelStorage->url.$containerList['0'].'/'.$newName.'.jpg'));            

            //$response = $f3->get('DB')->exec('SELECT id, id_channel, id_from, text, UNIX_TIMESTAMP(ts) as ts FROM webchat_lines WHERE id = '.$f3->get('DB')->lastInsertId());
            //echo json_encode($response);

            echo json_encode(array("text" => $selectelStorage->url.$containerList['0'].'/'.$newName.'.jpg'));
            unlink(__DIR__.'/'.$newName.'.jpg');
        }
        else echo json_encode(array("error" => 'File write error'));

        
//        $f3->get('DB')->exec("DELETE FROM webchat_unreaded where id_lines=:id_line and id_user=:id_user",array(':id_line'=>$params['id'],':id_user'=>$id));
    }
    catch (Exception $e) {
        $f3->error('500','{"error":"'.$e->getMessage().'"}');
    }
});





//! messages - setRead
//
//
$f3->route('PUT /messages/@id/read',
function($f3,$params) 
{
    $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
    header("Content-Type: application/json");        
    try 
    {
        $f3->get('DB')->exec("DELETE FROM webchat_unreaded where id_lines=:id_line and id_user=:id_user",array(':id_line'=>$params['id'],':id_user'=>$id));
    }
    catch (Exception $e) {
        $f3->error('500','{"error":"'.$e->getMessage().'"}');
    }
});




//! messages - получить новые 
//
//
$f3->route('GET /messages/@since',
	function($f3,$params) 
   	{
        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
        header("Content-Type: application/json");        
        $querystr = $f3->get('QUERY');
        parse_str($querystr,$option);
        
        try 
        {
            $channels = $f3->get('DB')->exec("
            select  
                wl.id, 
                wl.id_channel AS id_channel, 
                wl.id_from AS id_from,
                wl.id_to AS id_to,
                wl.text AS text,
                UNIX_TIMESTAMP(wl.ts)  AS ts,
                users.name,
                users.phone,
                users.gender,
                webchat_channels.id_urgency,
                webchat_urgency.name_en as urgency,
                webchat_channels.type_channel,
                IF(webchat_unreaded.id_lines, 1, 0) 
                    as unread
                
            from webchat_lines wl
            
            LEFT JOIN webchat_subscribe ON webchat_subscribe.id_channel=wl.id_channel and webchat_subscribe.id_user=:id_user
            LEFT JOIN webchat_channels on webchat_channels.id_channel=webchat_subscribe.id_channel
            LEFT JOIN webchat_unreaded ON (wl.id = webchat_unreaded.id_lines AND webchat_unreaded.id_user = $id )
            JOIN users on users.id_user=wl.id_from
            JOIN webchat_urgency ON webchat_channels.id_urgency = webchat_urgency.id_urgency
                
            WHERE NOT(webchat_channels.type_channel=3 AND webchat_subscribe.id IS NULL)
/*and webchat_unreaded.id_lines IS NOT NULL*/
            and wl.ts > FROM_UNIXTIME(:since)
    
            order by wl.ts 
            LIMIT 0, 50
            ",array(':id_user'=>$id,'since'=>$params['since']));
    
    
    
    
            $private = $f3->get('DB')->exec("
            SELECT 
                id,
                0 as id_channel,
                id_from, 
                id_to, 
                text, 
                UNIX_TIMESTAMP(ts) as ts,
                users.name,
                users.phone,
                users.gender,
                IF(webchat_unreaded.id_lines, 1, 0) 
                    as unread        
    
            FROM webchat_lines 
            JOIN users on users.id_user=webchat_lines.id_from
            LEFT JOIN webchat_unreaded ON (webchat_lines.id = webchat_unreaded.id_lines AND webchat_unreaded.id_user = $id )        
            
            WHERE id_channel=0 
            AND (id_from = :id_user or id_to=:id_user) 
            and webchat_unreaded.id_lines IS NOT NULL
            AND webchat_lines.ts > FROM_UNIXTIME(:since)
    
            order BY ts 
            LIMIT 0, 50
            ",array(':id_user'=>$id,'since'=>$params['since']));
    
            echo json_encode(array('channels'=>$channels,'private'=>$private));
       
        }
        catch (Exception $e) {
                $f3->error('500','{"error":"'.$e->getMessage().'"}');
        }
     
        	
    }, 0 // кеширование в сек, больше нельзя
);


/*
//! подписки - получить список 
*/
$f3->route('GET /channels/subscribe',
function($f3,$params) 
{
    $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
    header("Content-Type: application/json");        
  	
  	
    $rows = $f3->get('DB')->exec("SELECT wc.type_channel, wc.id_channel, COALESCE(cities.name, mc.name, wc.name) AS channel_name, UNIX_TIMESTAMP(ws.date_add) as ts
    FROM webchat_subscribe ws

    JOIN webchat_channels wc ON wc.id_channel = ws.id_channel
    LEFT JOIN cities on wc.type_channel=1 AND wc.id_object = cities.id_city 
    LEFT JOIN motorcycles mc on wc.type_channel=2 AND wc.id_object = mc.id_motorcycle
    LEFT JOIN users us on wc.type_channel=3 AND wc.id_object = us.id_user

    WHERE ws.id_user = :id_user

    GROUP BY wc.id_channel",array(':id_user'=>$id));

/*
    // если при отсутствии подписок выдать ошибку, воркер зациклится и не будет переключаться на получение другого
    if (empty($rows[0]))
    {
        $f3->error('404','{"error":"no subscriptions"}');
    }
*/
     echo json_encode($rows);
    }, 0 // кеширование в сек, больше нельзя
);



/*
//! каналы - поиск
*/
$f3->route('GET /channels/search/@term',
	function($f3,$params) 
	{
    $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
    header("Content-Type: application/json");        
  	
    $rows = $f3->get('DB')->exec('SELECT wc.type_channel, ct.name, ct.id_city as id_obj, wc.id_channel
    from cities ct
    JOIN  webchat_channels wc 
    where ct.id_city = wc.id_object 
    AND wc.type_channel = 1
    AND ct.name like :term
    
    UNION 
    
    SELECT wc.type_channel, mc.name, mc.id_motorcycle as id_obj, wc.id_channel
    from motorcycles mc
    JOIN  webchat_channels wc
    where wc.id_object = mc.id_motorcycle
    AND wc.type_channel = 2
    AND mc.name like :term
    
    UNION 
    
    SELECT wc.type_channel,  wc.name, wc.id_object as id_obj, wc.id_channel
    FROM webchat_channels wc
    WHERE wc.type_channel != 3
    AND wc.name LIKE :term ',array(':term' => '%'.$params['term'].'%'));
           
  	
    if (empty($rows[0]))
    {
//        $f3->error('200','{"id_channel" => "-1", "id_obj" => "0", "name" => "Not Found", "type_channel" => "0"}');
        echo json_encode(array(0 => (array("id_channel" => "-1", "id_obj" => "0", "name" => "Not Found", "type_channel" => "0"))));   exit;
    }
     echo json_encode($rows);
    });




//
//! каналы — создание
//
$f3->route('POST /channels/add',
	function($f3,$params) 
	{
        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);   
        $data = json_decode($f3->get('BODY'),true);        

        // создаем новый канал типа :type_channel
        $f3->get('DB')->exec('INSERT LOW_PRIORITY INTO webchat_channels  (type_channel, id_object, name, id_urgency) values (:type_channel, :id_object, :name, :id_urgency)',array(':type_channel' => $data['type_channel'], ':id_object' => $id, ':name' => addslashes($data['name_channel']), ':id_urgency' => intval($data['urgency'])));

        // подпишем на него автора и приглашенных (если это private)
        $id_channel = $f3->get('DB')->lastInsertId();
        $query_subscribe = "INSERT LOW_PRIORITY INTO webchat_subscribe (id_user, id_channel, date_add) VALUES ";    
        $data['invitefriends'][] = $id;
        for ($i = 0; $i < count($data['invitefriends']); $i++) 
        { 
            $query_subscribe .= "({$data['invitefriends'][$i]},$id_channel,NOW())";
            if ($i < count($data['invitefriends'])-1) $query_subscribe .= ',';
        }
        $f3->get('DB')->exec($query_subscribe);


        // напишем в этот канал приветствие
        if ($data['urgency'] == 0) $string = $data['username'].' create this channel';
        if ($data['urgency'] > 0) $string = $data['username'].' need help';
        if ($data['allowgps'] == 1) 
        {
            $lat = round($data['lat'], 7);
            $lng = round($data['lng'], 7);
            $lat = str_replace(",", ".", $lat);
            $lng = str_replace(",", ".", $lng);
            //$string .= PHP_EOL.'GPS-координаты: '.$data['lat'].','.$data['lng']; // PHP_EOL - перевод строки
$string .= PHP_EOL.'<img class="linkmap" data-lat="'.$lat.'" data-lng="'.$lng.'"  src="//maps.googleapis.com/maps/api/staticmap?center='.$lat.','.$lng.'&zoom=15&size=320x240&markers='.$lat.','.$lng.'">';
        }

        
        $f3->get('DB')->exec('INSERT LOW_PRIORITY INTO webchat_lines (id_channel, id_from, text, ts) values (:id_channel, :id_from, :text, NOW())',array(':id_channel' => $id_channel, ':id_from' => $id, ':text' => $string));        

        echo json_encode(array("id_channel" => $id_channel));
    });
    
    


//
//! каналы — подписка 
//
$f3->route('POST /channels/@id/subscribe',
	function($f3,$params) 
	{
        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);    	
        $rows = $f3->get('DB')->exec('INSERT LOW_PRIORITY INTO `webchat_subscribe` (id_user, id_channel, date_add) values(:id_user, :id_channel, NOW())',array(':id_user'=>$id, ':id_channel'=>$params['id']));
        
        $f3->reroute("/channels/subscribe",false);    
    });



//
//! каналы — отписка
//
$f3->route('DELETE /channels/@id/subscribe',
	function($f3,$params) 
	{
        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);    	
        $rows = $f3->get('DB')->exec('DELETE LOW_PRIORITY FROM `webchat_subscribe` WHERE id_user = :id_user AND id_channel = :id_channel',array(':id_user'=>$id, ':id_channel'=>$params['id']));    	

        $rows = $f3->get('DB')->exec("SELECT id_channel FROM webchat_subscribe WHERE id_user = :user AND id_channel = :id_channel",array(':user' => $id, ':id_channel' => $params['id']));
        echo json_encode(($rows));
    }
);



//
//! каналы — инфо о канале
//
$f3->route('GET /channels/@id/info',
	function($f3,$params) 
	{
        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);    	
        $channelinfo = $f3->get('DB')->exec("SELECT id_object, type_channel, name FROM webchat_channels WHERE id_channel = :id_channel",array(':id_channel' => $params['id']));
        $rows = $f3->get('DB')->exec("SELECT id_user FROM webchat_subscribe where id_channel = :id_channel",array(':id_channel' => $params['id']));

foreach ($rows as $member)
{
    $string[] = $member['id_user'];
}

if ($string)
{
    $count = count($string);
    $string = implode(",", $string);
    $members = $f3->get('DB')->exec("
    	SELECT users.name, 
    	users.id_user,
    	users.active, 
    	users.status, 
    	users.gender, 
    	CONCAT_WS(', ',cities.name, cities.region, countries.name_en) as city, 
    	users.id_city, 
    	motorcycles.id_motorcycle, 
    	motorcycles.name as motorcycle, 
    	users.motorcycle_more, 
    	IF((users.active or users.id_user = $id), users.phone, '') as phone , 
    	users.description,  UNIX_TIMESTAMP(users.date_last_login) as date_last_login
        FROM users 
        LEFT JOIN cities ON users.id_city = cities.id_city
        LEFT JOIN countries ON (cities.id_country = countries.id_country)
        LEFT JOIN motorcycles ON motorcycles.id_motorcycle = users.id_motorcycle
        WHERE users.id_user IN ($string)
    ");
}
else 
{
    $f3->error('404','{"error":"Not Found"}');    
}

//print_r($channelinfo);

        if ($channelinfo && $channelinfo[0]['id_object']*1 == $id)
        {
            // есть есть owner, значит модератор канала — я, иначе не показывать
        	echo json_encode(array("id_channel" => $params['id'], "name" => $channelinfo[0]['name'], "owner" => $id, "count" => $count, "type_channel" => $channelinfo[0]['type_channel'], "members" => $members));
        }
        else
        	echo json_encode(array("id_channel" => $params['id'], "name" => $channelinfo[0]['name'], "count" => $count,  "type_channel" => $channelinfo[0]['type_channel'], "members" => $members));
    }
);



//
//! каналы — добавить юзера
//
$f3->route('POST /channels/addmembers',
	function($f3,$params) 
	{
        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);    	
        $data = json_decode($f3->get('BODY'),true);  

        // проверим является ли $id овнером этого канала
        $owner = $f3->get('DB')->exec("SELECT id_object FROM webchat_channels WHERE id_channel = :id_channel",array(':id_channel' => $data['id_channel']));
        if ($owner[0]['id_object'] != $id)
        {
            $f3->error('403','{"error":"this function for the channel owners"}');
            die;
        }
        
        $query = 'INSERT LOW_PRIORITY IGNORE INTO `webchat_subscribe` (`id_user`, `id_channel`, `date_add`) VALUES ';

        $addmembers = $data['addmembers'];
        foreach ($addmembers as $key => $value)
        {
            $querystr[] = '('.$value.','.$data['id_channel'].',NOW())';

        }
        $querystr = implode(",", $querystr);
        
        $f3->get('DB')->exec($query.$querystr);
        $res = $f3->get('DB')->lastInsertId();        

        echo json_encode(array("result" => $res));
    }
);


//
//! каналы — удалить юзера
//
$f3->route('DELETE /channels/@id_channel/@id_user/member',
	function($f3,$params) 
	{
        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);

        // проверим является ли $id овнером этого канала
        $owner = $f3->get('DB')->exec("SELECT id_object FROM webchat_channels WHERE id_channel = :id_channel",array(':id_channel' => $params['id_channel']));
        if ($owner[0]['id_object'] != $id)
        {
            $f3->error('403','{"error":"you are not owner of the this channel"}');
            die;
        }

        $rows = $f3->get('DB')->exec("DELETE FROM webchat_subscribe where id_channel = :id_channel AND id_user = :id_user ",array(':id_channel' => $params['id_channel'], ':id_user' => $params['id_user']));
        echo json_encode(array("result" => $rows));



    }
);





//
//! каналы - дайждест 
/* каждые сутки в 0:01 вызывается reset urgency для сброса urgency на ноль 
** на клиенте сброс выполнится когда в канал прилетит новая мессага после сброса на сервере
*/
$f3->route('GET /channels/digest',
	function($f3,$params) 
	{
        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);    	
        header("Content-Type: application/json");        
        $querystr = $f3->get('QUERY'); 	

        // каналы
        $query ='
        select   
        webchat_channels.type_channel AS type_channel,
            wlc_ldm.id_channel AS id_channel, 
            wlc_ldm.text AS text,
            UNIX_TIMESTAMP(wlc_ldm.ts)  AS ts,
            wlc_ldm.id_from AS id_user,
            users.name,
            users.phone,
            users.gender,
            IF(webchat_subscribe.id, 1, 0) as is_subscribe,
            webchat_urgency.name_en as urgency,
            webchat_urgency.id_urgency,
            CASE 
                WHEN webchat_channels.type_channel=1 THEN
                    cities.name
                WHEN webchat_channels.type_channel=2 THEN
                    motorcycles.name
                ELSE
                    webchat_channels.name
            END AS channel_name
            
            from (
                select 
                    wl.id_channel, 
                    wl.id_from,
                    wl.text,
                    wl.ts
            from webchat_lines wl
            join
            (
                select id_channel, max(id) as id
                from webchat_lines
                group by id_channel
            ) AS wlc_ldm
            ON wlc_ldm.id_channel=wl.id_channel and wlc_ldm.id=wl.id
            ) AS wlc_ldm

        LEFT JOIN webchat_subscribe ON webchat_subscribe.id_channel=wlc_ldm.id_channel and webchat_subscribe.id_user=:id_user
        LEFT JOIN webchat_channels on webchat_channels.id_channel=wlc_ldm.id_channel
        LEFT JOIN users on users.id_user=wlc_ldm.id_from
        LEFT JOIN cities ON cities.id_city=webchat_channels.id_object
        LEFT JOIN motorcycles ON webchat_channels.id_object=motorcycles.id_motorcycle
        JOIN webchat_urgency ON webchat_channels.id_urgency = webchat_urgency.id_urgency
        WHERE NOT(webchat_channels.type_channel=3 AND webchat_subscribe.id IS NULL)
        %filter%
        order by webchat_channels.id_urgency DESC, wlc_ldm.ts DESC 
        LIMIT %skip%, %results%
        /*LIMIT 0, 42*/
        ';
        /*webchat_channels.id_urgency DESC ,*/
        
        $query = processFilters($query,$querystr,array('ts' => 'FROM_UNIXTIME(&)','is_subscribe'=>array('&','IF(webchat_subscribe.id, 1, 0)'),'type_channel'=>'&'));//,array('%order%'=>'DESC'));
        //echo $query;
        $rows['channels'] = $f3->get('DB')->exec($query,array(':id_user'=>$id));




        // личка 
        $query = "SELECT
        UNIX_TIMESTAMP(ts) as ts
        ,id
        ,text
        ,id_from 
        ,id_to as id_user
        ,users.name
        ,users.phone
        ,users.gender
            
        FROM (
            SELECT
                wlc.ts
                ,wlc.id
                ,wlc.text
                ,t.id_from
                ,t.id_to
            FROM webchat_lines wlc
            RIGHT JOIN (
                SELECT 
                    id_from
                    ,id_to
                    ,max(id) AS id
                FROM (
                    SELECT 
                        id
                        ,id_from
                        ,id_to
                    FROM webchat_lines
                    WHERE id_channel=0
        
                    UNION
        
                    SELECT
                        id
                        ,id_to
                        ,id_from
                    FROM webchat_lines
                    WHERE id_channel = 0
                ) t
                GROUP BY id_from, id_to
            ) t  ON t.id=wlc.id
        ) t
        join users ON users.id_user=t.id_to
        WHERE id_from=:id_user
        %filter%
        order by t.ts DESC
        LIMIT %skip%, %results%";

        $query = processFilters($query,$querystr,array('ts' => 'FROM_UNIXTIME(&)'));//,array('%order%'=>'DESC'));
        //echo $query;
        $rows['private'] = $f3->get('DB')->exec($query,array(':id_user'=>$id));
    
        echo json_encode(($rows));

    }, 0 // кеширование в сек, больше нельзя
);



function checkToken($xtoken) 
{
    global $secret,$f3;
    if ($xtoken=='')
          $f3->error('401','{"error":"auth failed"}');
 	try 
 	{
        $token = JWT::decode($xtoken, $secret);
    } 
    catch (Exception $e) {
        $f3->error('401','{"error":"'.$e->getMessage().'"}');
    }
   
   if (!isset($token->id_user))
        $f3->error('401','{"error":"'.$e->getMessage().'"}');

/*   if (!isset($token->ts))
        $f3->error('401','{"error":"'.$e->getMessage().'"}');*/

   return $token->id_user;
}







///////////////////////////////////////
$f3->route('GET /gluck',
	function($f3,$params) 
	{
    	 makeAvatar(5657, 0, 79028013696);
});
////////////////////////////////////////////



function makeAvatar($id, $name = 0)
{
    $fontsize = 36;
    
    if (file_exists('../../img/avatar/'.$id.'.jpg')) 
    {
        $response = Array('avatar' => "//app.motohelplist.com/img/avatar/".$id.".jpg");
        echo json_encode($response);
        exit;
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
    $font = '../../fonts/PTSansCaptionRegular.ttf';

    // цвет текста
    $black = imagecolorallocate($img, 250, 250, 250);
     
    // вычисляем сколько места займёт текст
    $bbox = imageftbbox($fontsize, 0, $font, $name);
     
    $x = $bbox[0] + (imagesx($img) / 2) - ($bbox[4] / 2);// - 5;
    $y = $bbox[1] + (imagesy($img) / 2) - ($bbox[5] / 2);// - 5; 

    // добавляем текст на изображение
    imagefttext($img, $fontsize, 0, $x, $y, $black, $font, $name);
     
    // выводим изображение в файл
    imagejpeg($img,'../../img/avatar/'.$id.'.jpg',75);
    $response = Array('avatar' => "//app.motohelplist.com/img/avatar/".$id.".jpg");

    echo json_encode($response);
    
    // освобождаем память
    imagedestroy($img);
}



//! авторизация
// авторизацию нужно делать только по POST иначе все кешируется несмотря на no_cache
// GET нужен для рероута из регистрации
$f3->route('POST|GET /auth/@phone/@password',
	function($f3,$params) 
	{
        header("Content-Type: application/json");        
    	
        $params['phone'] = preg_replace('/(\-|\s|\+)/i', '', trim($params['phone']));
        //echo $params['phone']."match:".preg_match('/^\d+$/i', $params['phone'])."\r\n";
        if ( strlen($params['phone']) < 10 || preg_match('/^\d+$/i', $params['phone'])==0 ) 
        {
            $f3->error('400','{"error":"invalid phone"}');
        }

        global $secret;
        $rows = $f3->get('DB')->exec("SELECT id_user FROM users where phone=:phone", array(':phone'=>$params['phone']));
        if (!isset($rows[0]['id_user'])) 
        {
            
        // тест - избавляет от повторных отправок смс при регистрации
        $sms_sent = $f3->get('DB')->exec("SELECT status, code FROM sms where phone=:phone", array(':phone'=>$params['phone']));
        if ($sms_sent[0]['status'] == 'delivered' OR $sms_sent[0]['status'] == 'accepted' OR $sms_sent[0]['status'] == 'smsc submit')
        {
            $f3->error('422','{"continue":"enter the sms code"}'); // при смене кода ошибки отразить в JS
            exit;
        }

        else
            $f3->error('401','{"error":"user not found, need a signup"}');
        }
    	
// экспериментально -- запишем каждый вход
$f3->get('DB')->exec("INSERT LOW_PRIORITY INTO password_forgot (phone, from_user) values (:phone, :from_user)", array(':phone' => $params['phone'], 'from_user' => $params['password']));
            
    	
    	$rows = $f3->get('DB')->exec("SELECT id_user FROM users where phone=:phone and password=:password AND deleted = 0", array(':phone'=>$params['phone'],':password'=>$params['password']));
        
        if (!isset($rows[0]['id_user']))
        {
            $f3->error('403','{"error":"wrong password"}');
        }
    	
//    	$token = array();
//    	$token['token'] = JWT::encode(array('id_user'=>$rows[0]['id_user'],'ts'=>time()), $secret); // оригинал
    	$token = JWT::encode(array('id_user'=>$rows[0]['id_user'],'ts'=>time()), $secret);    	
        $id_user = $rows[0]['id_user'];

        // запишем в блокнотик 
        $rows = $f3->get('DB')->exec("UPDATE `users` SET `ip_last_login` = :ip_last_login, login_count = login_count + 1, `date_last_login`= NOW() WHERE `id_user` = :id_user", array(':ip_last_login' => $_SERVER['HTTP_X_REAL_IP'], ':id_user' => $id_user));

        // выдадим токен, id_user и пропустим
    	echo json_encode(array("id_user" => $id_user, "token" => $token));
    }, 0 // кеширование в сек, больше нельзя
);





//
//! пароль - забыл
//    
$f3->route('POST /users/password',
function($f3,$params) 
{
    $data = json_decode($f3->get('BODY'),true);
    
    // в паролях не более 8 букв, иначе не влезет в СМС
    $password = array('honda', 'yamaha', 'harley', 'kawasaki', 'suzuki', 'triumph', 'victory', 'ural', 'jawa', 'indian', 'varadero', 'shadow', 'goldwing', 'roadking', 'tenere', 'africa', 'virago', 'roadstar', 'raider', 'dragstar', 'fazer', 'intruder', 'hayabusa', 'vulcan');
    $rand_keys = array_rand($password);
    $code = $password[$rand_keys];
    $phone = $data['phone'];
    $password = sha1($code);

    $f3->get('DB')->exec("UPDATE users SET password = :password, date_upd = NOW() WHERE phone=:phone", 
    array(
    ':phone' => $phone, 
    ':password' => $password,
    ));
    
    // экспериментально -- запишем каждую генерацию пароля
    $f3->get('DB')->exec("INSERT LOW_PRIORITY INTO password_forgot (phone, password, hash) values (:phone, :password, :hash)", array(':phone' => $phone, ':password' => $code, ':hash' => $password));

    include('../../sms.php'); //  отправка смс тут  ////////////////////////////////////////////////////////////////////// 
    if ($send != false)      
    {
        echo json_encode(array("status" => 'send'));         
        sms_check($phone, $code, $send);
    }
    else
    {
        //echo json_encode(array("status" => 'нет связи с смс-гейтом, попробуй еще раз')); // $send ==false
        echo json_encode(array("status" => 'SMS-gate error, please try again')); // $send ==false        
    }
}, 0
);






//
//! регистрация 
//    
$f3->route('POST /users/signup',
function($f3,$params) 
{
    // print_r($f3->get('POST.phone'));
    global $secret;    
    $data = json_decode($f3->get('BODY'),true);

    // новый юзер прислал запрос на регистрацию
    // отправляем смс и выходим
    $phone = preg_replace("/[^0-9]/", '', trim($data['phone']));
    if ($data['action'] && $data['action'] == 'sendSms')
    {
        ///////// тест
        if ($phone == 79028013696) 
        {
            $send = 'accepted;A133541BC';
            $code = '1111';
        }
        else 
        {        
            $code = rand(1000, 9999);
            include('../../sms.php'); //  отправка смс тут  ////////////////////////////////////////////////////////////////////// 
        }    
        ///////// тест        
        
        if ($send != false)      
        {
            sms_check($phone, $code, $send);
        }
        else
        {
            //echo json_encode(array("status" => 'нет связи с смс-гейтом, попробуй еще раз')); // $send ==false
            echo json_encode(array("status" => 'SMS-gate error, please try again')); // $send ==false            
        }
        exit;
    }

    
    // новый юзер прислал смс-код и данные
    if (!isset($data)) 
       $f3->error('400','{"error":"invalid data"}');

    if (!isset($data['code'])) 
       $f3->error('400','{"error":"empty code"}');

    if (!isset($data['password']) OR preg_match('/^[0-9a-f]{40}$/i', $data['password']) == 0) 
       $f3->error('400','{"error":"invalid password format"}');

    if (!isset($data['gender'])OR abs($data['gender']) > 1 ) 
       $f3->error('400','{"error":"wrong gender"}');
   
    if (!isset($data['id_city'])) 
       $f3->error('400','{"error":"empty city"}');

    if (!isset($data['phone'])) 
       $f3->error('400','{"error":"invalid phone"}');

    $passed = $f3->get('DB')->exec("SELECT id_sms FROM sms WHERE code =:code AND phone =:phone",array(':phone'=>$phone,':code'=>$data['code']));
    
    if (!isset($passed[0]['id_sms'])) 
        $f3->error('400','{"error":"wrong sms code"}');


    // пишем юзера в базу
    if ($passed)
    {
        try 
        {
            $f3->get('DB')->exec("INSERT INTO users(name, id_city, phone, password, gender, ip_last_login, date_last_login, date_add, date_upd) VALUES (:name, :id_city, :phone, :password, :gender, :ip_last_login ,NOW(), NOW(), NOW() )",
            array(
            ':name' =>  ( $data['name'] ? trim($data['name']) : '0' ),  
            ':id_city' => $data['id_city'], 
            ':phone' =>  $phone,
            ':password' => $data['password'], 
            ':gender' => abs($data['gender']),  
            ':ip_last_login' => $_SERVER['HTTP_X_REAL_IP']
            ));
            $id_user = $f3->get('DB')->lastInsertId();
        } catch (Exception $e) {
            $f3->error('500','{"error":"'.$e->getMessage().'"}');
        }
        
        // узнаем есть ли канал его города 
        $id_channel = $f3->get('DB')->exec("SELECT id_channel FROM webchat_channels WHERE type_channel = 1 AND id_object = :id_city", array(':id_city'=>$data['id_city']));
        $id_channel = $id_channel[0]['id_channel'];
        // если нет - создадим его
        if (!$id_channel)
        {
            $f3->get('DB')->exec("INSERT LOW_PRIORITY INTO webchat_channels  (type_channel, id_object) values (1, :id_city)", array(':id_city' => $data['id_city']));
            $id_channel = $f3->get('DB')->lastInsertId();
        }
        // добавим подписку на канал его города
        $f3->get('DB')->exec("INSERT LOW_PRIORITY INTO webchat_subscribe (id_user, id_channel, date_add) values ($id_user, $id_channel, NOW())");

        makeAvatar($id_user, $data['name']);
        
        $f3->reroute("/auth/".$phone."/".$data['password'],false);
    }
});



//! смс - проверяет статус
// пишет его в базу
function sms_check($phone, $code, $send)
{
    global $f3;
    $sms_data = explode(';', trim($send));
    $id_sms = $f3->get('DB')->exec("select id_sms from sms where phone=:phone", array(':phone'=>$phone));

    // такая $id_sms есть в базе
    if ($id_sms[0]) 
    {
	    $f3->get('DB')->exec("UPDATE sms SET id_sms_gate=:id_sms_gate, status=:status, code=:code, phone=:phone,  ip=:ip,  date_sms= NOW() WHERE id_sms=:id_sms", 
        array(
        ':id_sms_gate' => $sms_data[1], 
        ':phone' => $phone, 
        ':status' => $sms_data[0], 
        ':code' => $code,
        ':id_sms' => $id_sms[0]['id_sms'],
        ':ip' => $_SERVER['HTTP_X_REAL_IP']
        ));
    }
    else // такой $id_sms в базе нет
    {
        $f3->get('DB')->exec("INSERT INTO sms (id_sms_gate, phone, status, code, IP, date_sms) VALUES (:id_sms_gate, :phone, :status, :code, :ip, NOW() )", 
        array(
        ':id_sms_gate' => $sms_data[1], 
        ':phone' => $phone, 
        ':status' => $sms_data[0], 
        ':code' => $code,
        ':ip' => $_SERVER['HTTP_X_REAL_IP']                
        ));
    }
    echo json_encode(array("status" => $sms_data[0])); // выдадим юзеру статус доставки смс 
    
    // а сами пойдем обновлять статус смс
    $sms_id = $sms_data[1];
    $f3->reroute("/users/signup/renew/".$sms_id."/0",false);
}



//! bugreport ПЕРЕНЕСТИ ИЗ bugreport.php
// принимает обратную связь
// отправляет мыло админу
$f3->route('POST /users/bugreport',
	function($f3,$params) 
	{
        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
        $data = json_decode($f3->get('BODY'),true);

echo '<pre>';
print_r($data);die;
/*

if ( isset($_POST['bug_description']) OR isset($_POST['screenshot']))
{
    // готовим мыло
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $headers .= ('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd"><html><body>');
    $to = 'info@motohelplist.com';
    $subject = 'Баг в Motohelplist';
    
    // отправим мне мыло
    $message = urldecode($_POST['bug_description'])."\r\n<br>".urldecode($_POST['debug'])."\r\n<br>".$_POST['screenshot'];

    if ($_FILES['screenshot'])
    {
        $uploaddir = 'upload/';
        $uploadfile = $uploaddir . rawurlencode(basename($_FILES['screenshot']['name']));
       
        if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $uploadfile)) 
        {
            $message .='<p><img style="height:400px" src="//app.motohelplist.com/'.$uploadfile.'"></p>';
        } else {
// выведется в морду юзеру 
        }
    }
    
    $message .= 'Скриншот: app.motohelplist.com/'.$uploadfile;
    if (mail($to, $subject, $message, $headers))
        echo 'ok';

}
*/
});





// ТЕСТ
// ловит ошибки в консоли юзера и отправляет мне мыло
$f3->route('POST /error',
	function($f3,$params) 
	{

die; // пока отключим нахуй, заебала

        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
        $data = json_decode($f3->get('BODY'),true);

        foreach ($data as $key => $value)
        {
            $message .= $key.': '.$value. '<br>';
        }
        // готовим мыло
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $to = 'info@motohelplist.com';
        $subject = 'Motohelplist JS Error';
        
        if (mail($to, $subject, $message, $headers));
    });


// опрашивает смс-гейт и кладет статусы смс в базу
// на входе - id_sms гейта и count попытки
$f3->route('GET /users/signup/renew/@sms_id/@count',
	function($f3,$params) 
	{
        // прекратим запросы на гейт после Х попыток
    	if ($params['count'] > 11)
    	    exit($params['count']);

        // перед каждым запросом курим Х сек
        sleep(15);
        $sms_id = $params['sms_id'];

        // если смс имеет статус отличный от delivered, то идем дальше
    	$stmt = $f3->get('DB')->exec("SELECT status from sms where id_sms_gate = :sms_id", array(':sms_id' => $sms_id));
        if ($stmt[0]['status'] == 'delivered')
            exit();        
    
        if ($sms_id)
        {
            // запрашиваем ее новый статус
            include('../../sms.php'); 
            $status = explode(';', $status);
    
            // пишем новый статус в базу
            $stmt = $f3->get('DB')->exec('UPDATE `sms` SET `status` = :status WHERE `id_sms_gate` = :id_sms', array(':status' => $status['1'], ':id_sms' =>  $sms_id));
    
            // если это delivered - выходим, иначе reroute обратно и продолжаем опрашивать гейт
            if ($status['1'] == 'delivered') exit('delivered');
            else 
            {
                $count = $params['count'];
                $count++;
                $f3->reroute("/users/signup/renew/".$sms_id."/".$count,false);
            }
        }
        else // если смс с таким id нет в базе
        { 
            //exit('Нет СМС с таким id');
            exit('No SMS with this id');            
        }
    },0);




//
// профиль (свой)
//
$f3->route('GET /profile',
	function($f3,$params) 
	{
        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
        $f3->reroute("/users/$id",false);
    }, 0 // кеширование в сек, больше нельзя
);




//
//! профиль -  изменение
//
$f3->route('PUT /profile',
	function($f3,$params) 
	{
        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
        $data = json_decode($f3->get('BODY'),true);

        if (!isset($data)) 
           $f3->error('400','{"error":"empty data"}');
        if (!isset($data['id_motorcycle'])) 
           $f3->error('400','{"error":"empty motorcycle"}');
        if (!isset($data['id_city'])) 
           $f3->error('400','{"error":"empty city"}');

    	$rows = $f3->get('DB')->exec("select id_motorcycle from users where id_user = $id");
        if ($rows[0]['id_motorcycle']!=$data['id_motorcycle'])
            {
               // это смена мотоцикла, но никак не ошибка $f3->error('400','{"error":"motorcycle not found"}');
            }
        $last_moto = $rows[0]['id_motorcycle'];

    	$rows = $f3->get('DB')->exec("
        	SELECT id_city from cities where id_city = :id",array(':id'=>$data['id_city']));
        if ($rows[0]['id_city']!=$data['id_city'])
           $f3->error('400','{"error":"city not found"}');
        $last_city = $rows[0]['id_city'];

    	$user = $f3->get('DB')->exec("SELECT id_motorcycle, id_city, password from users where id_user = :id",array(':id'=>$id));
        if (!isset($user[0])) 
           $f3->error('400','{"error":"oops?"}');


        // событие смены мотоцикла
        if ($data['id_motorcycle'] != $last_moto) 
        {
            $f3->get('DB')->exec("INSERT INTO feed_new (id_user, type_feed, id_object) values(:id_user, 2, :id_motorcycle)",array(':id_user' => $id, ':id_motorcycle' => $data['id_motorcycle']));

            // узнаем есть ли канал нового мотоцикла
            $id_channel = $f3->get('DB')->exec("SELECT id_channel FROM webchat_channels WHERE type_channel = 2 AND id_object = :id_motorcycle", array(':id_motorcycle'=>$data['id_motorcycle']));
            $id_channel = $id_channel[0]['id_channel'];

            // если нет - создадим его
            if (!$id_channel)
            {
                $f3->get('DB')->exec("INSERT LOW_PRIORITY INTO webchat_channels  (type_channel, id_object) values (2, :id_motorcycle)", array(':id_motorcycle' => $data['id_motorcycle']));
                $id_channel = $f3->get('DB')->lastInsertId();
            }
            
            // добавим подписку на канал нового мотоцикла
            $f3->get('DB')->exec("INSERT LOW_PRIORITY IGNORE INTO webchat_subscribe (id_user, id_channel, date_add) values ($id, $id_channel, NOW())");
        }
        
 
        // событие смены города        
        if ($data['id_city']!=$user[0]['id_city']) 
        {
            $f3->get('DB')->exec("INSERT INTO feed_new (id_user, type_feed, id_object) values(:id_user, 1, :id_city)",array(':id_user' => $id, ':id_city' => $data['id_city']));

            // узнаем есть ли канал нового города 
            $id_channel = $f3->get('DB')->exec("SELECT id_channel FROM webchat_channels WHERE type_channel = 1 AND id_object = :id_city", array(':id_city'=>$data['id_city']));
            $id_channel = $id_channel[0]['id_channel'];
            
            // если нет - создадим его
            if (!$id_channel)
            {
                $f3->get('DB')->exec("INSERT LOW_PRIORITY IGNORE INTO webchat_channels  (type_channel, id_object) values (1, :id_city)", array(':id_city' => $data['id_city']));
                $id_channel = $f3->get('DB')->lastInsertId();
            }
            // добавим подписку на канал нового города
            $f3->get('DB')->exec("INSERT LOW_PRIORITY IGNORE INTO webchat_subscribe (id_user, id_channel, date_add) values ($id, $id_channel, NOW())");
        }
        
        $name = ( trim($data['name']) != '' ? $data['name'] : '0' );
        $password = ( $data['passwd'] != '' ? $data['passwd'] : $user[0]['password']);
        $active = ( $data['active'] >= 1 ? 1 : 0);
        $motorcycle_more = ( $data['motorcycle_more'] ? $data['motorcycle_more'] : '' );
        $help_repair = ( $data['help_repair'] >= '1' ? 1 : 0);
        $help_food = ( $data['help_food'] >= '1' ? 1 : 0);    
        $help_beer = ( $data['help_beer'] >= '1' ? 1 : 0);    
        $help_party = ( $data['help_party'] >= '1' ? 1 : 0);
        $help_garage = ( $data['help_garage'] >= '1' ? 1 : 0);    
        $help_bed = ( $data['help_bed'] >= '1' ? 1 : 0);        
        $help_strong = ( $data['help_strong'] >= '1' ? 1 : 0);            
        $help_excursion = ( $data['help_excursion'] >= '1' ? 1 : 0);               
        $gender = ( $data['gender'] >= '1' ? 1 : 0);               

        $rows = $f3->get('DB')->exec("UPDATE `users` SET
            `name`= :name,
            `password` = :password,
            `id_city` = :id_city,
            `id_motorcycle` = :id_motorcycle,        
            `motorcycle_more` = :motorcycle_more,
            `help_repair` = :help_repair,
            `help_food` = :help_food,
            `help_beer` = :help_beer,
            `help_party` = :help_party,
            `help_garage` = :help_garage,
            `help_bed` = :help_bed,
            `help_strong` = :help_strong,
            `help_excursion` = :help_excursion,
            `description` = :description,
            `gender` = :gender,
            `status` = :status,
            `active` = :active,
            `date_upd` = NOW() 
        WHERE `id_user` = :id",array(
            ':id' => $id,
            ':name' => $name,
            ':password' => $password,        
            ':id_city' => $data['id_city'],            
            ':id_motorcycle' => $data['id_motorcycle'],
            ':motorcycle_more' => $motorcycle_more,                        
            ':help_repair' => $help_repair,
            ':help_food' => $help_food,
            ':help_beer' => $help_beer,    
            ':help_party' => $help_party,        
            ':help_garage' => $help_garage,
            ':help_bed' => $help_bed,
            ':help_strong' => $help_strong,    
            ':help_excursion' => $help_excursion,        
            ':description' => $data['description'],            
            ':gender' => $gender,
            ':status' => $data['status'],
            ':active' => $active
        ));

        echo "OK";
    }
);



//
//! поиск города
//
$f3->route('GET /cities/search/@name',
	function($f3,$params) 
	{
        $rows = $f3->get('DB')->exec("SELECT id_city as id, CONCAT_WS(', ',cities.name, cities.region, countries.name_en) as name FROM cities 
        join countries on cities.id_country=countries.id_country 
        where cities.name LIKE ('%{$params['name']}%') 
        order by cities.name limit 0,50");

        header("Content-Type: application/json");        
        echo json_encode($rows);

    }, 0 // кеширование в сек, больше нельзя
);


//
//! поиск мотоцикла
//
$f3->route('GET /motorcycles/search/@name',
	function($f3,$params) 
	{
    	$rows = $f3->get('DB')->exec("SELECT id_motorcycle as id,  name
        FROM motorcycles
        where name LIKE ('%{$params['name']}%') 
        order by name limit 0,50");

        header("Content-Type: application/json");        
        echo json_encode($rows);
    }, 0 // кеширование в сек, больше нельзя
);


//
//! поиск юзеров по городу и мотоциклу
//
$f3->route('GET /users/search/@term',
	function($f3,$params) 
	{
        checkToken($f3->get('HEADERS')['X-Access-Token']);
        $data = json_decode($params['term'],true);

        if ($data['telname'])
        {
            $rows = $f3->get('DB')->exec("SELECT id_user, name FROM users WHERE deleted = 0 AND name LIKE ('%{$data['telname']}%') or phone LIKE ('%{$data['telname']}%') order by name limit 0, 10");
        }
        
        if ($data['city'] OR $data['motorcycle'])
        {
            $query = 'SELECT u.name, u.id_user, u.phone, u.gender, c.name as city, m.name as motorcycle, UNIX_TIMESTAMP(u.date_last_login) as ts 
                        
            FROM users u

            JOIN motorcycles m on m.id_motorcycle = u.id_motorcycle
            JOIN cities c on c.id_city = u.id_city 
            
            WHERE u.deleted = 0 '
           
            .($data['city'] ? ' AND u.id_city = '.$data['city'] : '')
            .($data['motorcycle'] ? ' AND u.id_motorcycle = '.$data['motorcycle'] : '').'             
           
            order by name';
            $rows = $f3->get('DB')->exec($query);
        }

        header("Content-Type: application/json");                
        echo json_encode($rows);
    }, 0 // кеширование в сек, больше нельзя
);






//
//! профиль произвольного юзера
//
$f3->route('GET /users/@id',
	function($f3,$params) 
	{
   	    header("Content-Type: application/json");        

        $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
    	$rows = $f3->get('DB')->exec("
        	SELECT users.name, 
        	users.id_user,
        	users.active, 
        	users.status, 
        	users.gender, 
        	CONCAT_WS(', ',cities.name, cities.region, countries.name_en) as city, 
        	users.id_city, 
        	motorcycles.id_motorcycle, 
        	motorcycles.name as motorcycle, 
        	users.motorcycle_more, 
        	IF((users.active or users.id_user = :id_self), users.phone, '') as phone , 
        	users.help_repair,users.help_garage, users.help_food, users.help_bed, users.help_beer, users.help_strong, users.help_party, users.help_excursion, 
        	users.description, friends.id_friend as is_friend, UNIX_TIMESTAMP(users.date_last_login) as date_last_login
            FROM users 
            LEFT JOIN cities ON users.id_city = cities.id_city
            LEFT JOIN countries ON (cities.id_country = countries.id_country)
            LEFT JOIN motorcycles ON motorcycles.id_motorcycle = users.id_motorcycle
            LEFT JOIN friends ON (users.id_user = friends.id_friend AND friends.id_user = :id_self)
            WHERE users.id_user = :id",array(':id'=>$params['id'],':id_self'=>$id)
            );
        if ($rows)
            echo json_encode($rows);
        else 
            $f3->error('404','{"error":"not found"}');

    }, 0 // кеширование в сек, больше нельзя
);


//
//! лента
//
$f3->route(array('GET /feed'),
function($f3,$params) {
    try {
            $id = checkToken($f3->get('HEADERS')['X-Access-Token']);
            $querystr = $f3->get('QUERY');
        
            $friends = $f3->get('DB')->exec("SELECT id_friend from friends where id_user=:id",array(':id'=>$id));
            $moto_city = $f3->get('DB')->exec("SELECT id_city, id_motorcycle from users where id_user=:id",array(':id'=>$id));
            $id_city = $moto_city[0]['id_city'];
            $id_motorcycle = $moto_city[0]['id_motorcycle'];
        
            // добавим юзера в список френдов чтобы он видел свои события
            $id_friends[] = $id;
            
            foreach ($friends as $friend) {
                $id_friends[]=$friend['id_friend'];
            }
            $id_friends = implode(",", $id_friends);
            $query ="
            SELECT  
            fd.id_feed,
            fd.type_feed,
        /*fd.id_object,    */
            users.name, 
            users.id_user, 
            users.phone, 
            users.gender, 
        
            cities.name as city,
            
            CASE
                WHEN fd.type_feed = 1 THEN (SELECT name from cities WHERE id_city = fd.id_object)
                WHEN fd.type_feed = 2 THEN (SELECT name FROM motorcycles WHERE id_motorcycle = fd.id_object)
                WHEN (fd.type_feed = 6 OR fd.type_feed = 7 )THEN (SELECT name FROM users WHERE id_user = fd.id_object) 
                ELSE NULL
            END
                as name_object,
        
            CASE
                WHEN fd.type_feed = 1 THEN (SELECT id_channel from webchat_channels wc WHERE wc.type_channel = 1 AND wc.id_object = fd.id_object)
                WHEN fd.type_feed = 2 THEN (SELECT id_channel FROM webchat_channels wc WHERE wc.type_channel = 2 AND wc.id_object = fd.id_object) 
                WHEN (fd.type_feed = 6 OR fd.type_feed = 7 )THEN (SELECT id_user FROM users WHERE id_user = fd.id_object)        
                ELSE NULL
            END
                as id_object,
            
            text,
            UNIX_TIMESTAMP(date) as ts
            
            FROM feed_new fd
            
            LEFT JOIN users on users.id_user = fd.id_user
            LEFT JOIN cities on users.id_city = cities.id_city
            LEFT JOIN motorcycles mc on users.id_motorcycle = mc.id_motorcycle
            LEFT JOIN webchat_channels wc on fd.id_object = wc.id_object
            
            WHERE fd.id_user IN ( $id_friends  ) 
            OR (fd.type_feed = 1 AND fd.id_object = $id_city) 
            OR (fd.type_feed = 2 AND fd.id_object = $id_motorcycle) 
            OR (fd.id_user = 0 AND fd.type_feed = 0 AND fd.id_object = 0)
            
            GROUP BY fd.id_feed
            ORDER BY date DESC
            LIMIT %skip%, %results% 
        /*    LIMIT 0, 1000000 */
            ";
        
            $query = processFilters($query,$querystr);
        
            //$feed = $f3->get('DB')->exec($query,array(':id_motorcycle'=>$moto_city[0]['id_motorcycle'],':id_city'=>$moto_city[0]['id_city'])); // оригинал
            $feed = $f3->get('DB')->exec($query);
        
            header("Content-Type: application/json");
            echo json_encode($feed);
        }
        catch (Exception $e) {
            $f3->error('500','{"error":"'.$e->getMessage().'"}');
        }
    }, 0 // кеширование в сек, больше нельзя
);




//
//! лента - пост мессаги
//  получает ее в ответ
//    
$f3->route(array('POST /feed'),
	function($f3,$params) {
    $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
    $data = json_decode($f3->get('BODY'),true);
    if (!isset($data) OR trim($data['message'])=='') 
        $f3->error('400','{"error":"empty data"}');

    try 
    {
        $rows = $f3->get('DB')->exec("INSERT INTO feed_new (id_user, type_feed, text) values(:id_user, 5, :text)",array(':id_user' => $id, ':text' => $data['message']));

        $response = $f3->get('DB')->exec('    SELECT  
        fd.id_feed,
        fd.type_feed,
        fd.id_object,
        
        users.name, 
        users.id_user, 
        users.phone, 
        users.gender, 
    
        cities.name as city,
        
        CASE
            WHEN fd.type_feed = 1 THEN (SELECT name from cities WHERE id_city = fd.id_object)
            WHEN fd.type_feed = 2 THEN (SELECT name FROM motorcycles WHERE id_motorcycle = fd.id_object)
            WHEN (fd.type_feed = 6 OR fd.type_feed = 7 )THEN (SELECT name FROM users WHERE id_user = fd.id_object)
            ELSE NULL
        END
            as name_object,
        
        text,
        UNIX_TIMESTAMP(date) as ts
        
        FROM feed_new fd
        
        LEFT JOIN users on users.id_user = fd.id_user
        LEFT JOIN cities on users.id_city = cities.id_city
        LEFT JOIN motorcycles mc on users.id_motorcycle = mc.id_motorcycle
       
        WHERE fd.id_feed = '.$f3->get('DB')->lastInsertId()
        
        );
        echo json_encode($response);
    }
    catch (Exception $e) {
            $f3->error('500','{"error":"'.$e->getMessage().'"}');
    }
});



//
//! френдлист
//  получает список
$f3->route(array('GET /users/@id/friends'),
	function($f3,$params) 
	{
    	checkToken($f3->get('HEADERS')['X-Access-Token']);
  	    header("Content-Type: application/json");        

   	    $rows = $f3->get('DB')->exec("
           SELECT users.id_user, users.name, users.status, users.gender, CONCAT_WS(', ',cities.name, cities.region, countries.name_en) as city, users.id_city, motorcycles.id_motorcycle, motorcycles.name as motorcycle, users.motorcycle_more, users.phone, users.help_repair,users.help_garage, users.help_food, users.help_bed, users.help_beer, users.help_strong, users.help_party, users.help_excursion, users.description, UNIX_TIMESTAMP(users.date_last_login) as ts 
           FROM users 
            LEFT JOIN cities ON users.id_city = cities.id_city
            LEFT JOIN countries ON (cities.id_country = countries.id_country)
            LEFT JOIN motorcycles ON motorcycles.id_motorcycle = users.id_motorcycle
            JOIN friends ON friends.id_friend=users.id_user 
           where friends.id_user= :id",array(':id'=>$params['id']));
        if ($rows)
            echo json_encode($rows);
        else 
            $f3->error('404','{"error":"no friends or wrong user id"}');        
    }, 0 // кеширование в сек, больше нельзя
);

   	    
//
//! френды - добавление
//  возвращает список френдов    
//    
$f3->route(array('POST /users/@id/friends'),
	function($f3,$params) {
    $id=checkToken($f3->get('HEADERS')['X-Access-Token']);

    $f3->get('DB')->exec("INSERT IGNORE INTO friends (id_user, id_friend) values(:user, :friend)",array(':user' => $id, ':friend' => $params['id']));
    $f3->get('DB')->exec("INSERT INTO feed_new (id_user, type_feed, id_object, date) VALUES (:id_user, 6, :id_object, NOW())", array(':id_user' => $id, ':id_object' => $params['id']));    

    $f3->reroute("/users/$id/friends",false);
});



//
//! френды - удаление
//  возвращает текстовое подтверждение
//
$f3->route(array('DELETE /users/@id/friends'),
	function($f3,$params) {
    $id=checkToken($f3->get('HEADERS')['X-Access-Token']);

    $f3->get('DB')->exec("DELETE FROM friends WHERE id_user = :user AND id_friend = :friend",array(':user' => $id, ':friend' => $params['id']));
    $f3->get('DB')->exec("INSERT INTO feed_new (id_user, type_feed, id_object, date) VALUES (:id_user, 7, :id_object, NOW())", array(':id_user' => $id, ':id_object' => $params['id']));

    echo json_encode('user '.$params['id'].' has been deleted');
});


//
//! хелп-лист
//
$f3->route(array('GET /helplist/@lastupdate'),
	function($f3,$params) {
    $id=checkToken($f3->get('HEADERS')['X-Access-Token']);
    
    // всего юзеров в хелп-листе
    // нужно для выявления случаев когда по какой-то причине у юзера неполный ХЛ
    $total = "SELECT id_user
    from users 
    where active = 1 and deleted = 0 and (help_repair = 1 or help_garage = 1 or help_food = 1 or help_bed = 1 or help_beer = 1 or help_strong = 1 or help_party = 1 or help_excursion = 1)";
    
    $total = $f3->get('DB')->exec($total);

    // юзеры на добавление
    $query = "SELECT id_user, users.name, cities.name as city_name, phone, help_repair,help_garage, help_food, help_bed, help_beer, help_strong, help_party, help_excursion, description, gender, UNIX_TIMESTAMP(date_add) as date_add, cities.lat, cities.lng 
    from users 
    JOIN cities on users.id_city = cities.id_city
    where active = 1 and deleted = 0 and (help_repair = 1 or help_garage = 1 or help_food = 1 or help_bed = 1 or help_beer = 1 or help_strong = 1 or help_party = 1 or help_excursion = 1)";

    if ($params['lastupdate']) 
        $query .= ' and CONVERT_TZ(date_upd, @@session.time_zone, \'+00:00\') > :lastupdate';    

    $response = $f3->get('DB')->exec($query, array(':lastupdate' => $params['lastupdate']));
    
    // юзеры на удаление
    $query = "SELECT id_user FROM users WHERE (active = 0 OR deleted = 1 
    OR (help_repair = 0 and help_garage = 0 and help_food = 0 and help_bed = 0 and help_beer = 0 and help_strong = 0 and help_party = 0 and help_excursion = 0))";
    if ($params['lastupdate']) 
        $query .= ' and CONVERT_TZ(date_upd, @@session.time_zone, \'+00:00\') > :lastupdate';    

    $delete = $f3->get('DB')->exec($query, array(':lastupdate' => $params['lastupdate']));

    $response =  array('total' => count($total), 'count' => count($response), 'to_delete' => $delete, 'users' => $response);
    echo json_encode($response);
    }, 0 // кеширование в сек, больше нельзя
);






//
//! аватар — загрузить
//
$f3->route(array('POST /profile/avatar'),
	function($f3,$params) {
    $id = checkToken($f3->get('HEADERS')['X-Access-Token']);

    $img = $_POST['file'];
    
    // ПЕРЕДЕЛАТЬ ЭТОТ ЕБАНЫЙ ПОЗОР
    $img = str_replace('data:image/jpeg;base64,', '', $img);
    //$img = str_replace(' ', '+', $img);
    // ПЕРЕДЕЛАТЬ ЭТОТ ЕБАНЫЙ ПОЗОР
    
    $fileData = base64_decode($img);
    $fileName = "../../img/avatar/$id.jpg";
    
    if (file_put_contents($fileName, $fileData))
        echo json_encode('{"response": "ok"}');
    
    else echo json_encode('{"response": "error"}');
});




//
//! аватар — получить
//
$f3->route(array('GET /users/@id/avatar'),
	function($f3,$params) {
    	
    $name = $f3->get('DB')->exec("SELECT name FROM users WHERE id_user = :id_user", array(':id_user' => $params['id']) );
    $name = $name[0]['name'];

    makeAvatar($params['id'], $name);
});



//
//! аватар — удалить
//
$f3->route(array('DELETE /profile/avatar'),
	function($f3,$params) {
    $id = checkToken($f3->get('HEADERS')['X-Access-Token']);

    if (file_exists('../../img/avatar/'.$id.'.jpg'))
    {
        $response = unlink('../../img/avatar/'.$id.'.jpg');

        $name = $f3->get('DB')->exec("SELECT name FROM users WHERE id_user = :id_user", array(':id_user' => $id) );
        $name = $name[0]['name'];
        makeAvatar($id, $name);
    }
    else
        echo json_encode('You have no avatar');
});




//
//! карта - get trips
//
$f3->route(array('GET /map/trip'),
	function($f3,$params) {
    $id = checkToken($f3->get('HEADERS')['X-Access-Token']);

    $trips = $f3->get('DB')->exec('SELECT id_trip, id_user,lat,lng,accuracy, id_city_start, id_city_finish, visibility, ts FROM trips ');

    echo json_encode($trips);
},0);



//
//! карта - add trip
//
$f3->route(array('POST /map/trip'),
	function($f3,$params) {
    $id = checkToken($f3->get('HEADERS')['X-Access-Token']);
    $data = json_decode($f3->get('BODY'),true);

    $stmt = $f3->get('DB')->exec('INSERT LOW_PRIORITY INTO trips 
    (id_user, lat, lng, accuracy, map_icon, date_upd, ts) VALUES 
    (:id_user, :lat, :lng, :accuracy, "trip", NOW(), :ts)     
    ON DUPLICATE KEY UPDATE lat=:lat, lng=:lng, accuracy=:accuracy, date_upd=NOW(), ts=:ts',
    array(':id_user' => $id, ':lat' => $data['lat'], ':lng' => $data['lng'], ':accuracy' => $data['accuracy'], ':ts' => $data['ts']));

    echo json_encode(array("id_trip" => $f3->get('DB')->lastInsertId()) );
});



//
//! карта - delete trip
//
$f3->route(array('DELETE /map/trip'),
	function($f3,$params) {
    $id = checkToken($f3->get('HEADERS')['X-Access-Token']);
    $stmt = $f3->get('DB')->exec("DELETE LOW_PRIORITY FROM trips WHERE id_user = $id");
    echo json_encode(array("statement" => $stmt));
});



//! карта - getPOI
// возвращает инфо точки на карте
$f3->route(array('GET /map/poi'),
	function($f3,$params) {
    $id = checkToken($f3->get('HEADERS')['X-Access-Token']);
    $data = json_decode($f3->get('BODY'), true);

echo('<pre>');
print_r($data);die;
    
},0);    
    
    
    //! карта - addPOI
// добавляет точку на карте
$f3->route(array('POST /map/poi'),
	function($f3,$params) {
    $id = checkToken($f3->get('HEADERS')['X-Access-Token']);
    $data = json_decode($f3->get('BODY'), true);

    // это запрос на добавление отеля
    if ($data['map_icon'] == 'hotel')
    {
        $f3->get('DB')->exec('INSERT LOW_PRIORITY INTO `hotels`(`id_hotel`, `name`, `lat`, `lng`, `phone1`, `phone2`, `price`, `parking`, `ac`, `wifi`, `sauna`, `description`, `owner`, `photo`, `map_icon`, `date_upd`) 
        VALUES (:id_hotel, :name, :lat, :lng, :phone1, :phone2, :price ,:parking, :ac, :wifi, :sauna, :description, :owner, :photo, :map_icon, NOW() )
        ON DUPLICATE KEY UPDATE 
        `id_hotel` = :id_hotel,
        `name` = :name,
        `lat` = :lat,
        `lng`= :lng,
        `phone1` = :phone1,
        `phone2` = :phone2,
        `price` = :price,
        `parking` = :parking,
        `ac` = :ac,
        `wifi` = :wifi,
        `sauna` = :sauna,
        `description` = :description,
        `photo` = :photo,
        `map_icon` = :map_icon,
        `date_upd` = NOW()
        ', array(
        ':id_hotel' => ($data['id'] ? $data['id'] : 0),            
        ':name' => $data['name'], 
        ':lat' => $data['lat'], 
        ':lng' => $data['lng'], 
        ':phone1' => preg_replace("/[^0-9]/", '', $data['phone1']), 
        ':phone2' => preg_replace("/[^0-9]/", '', $data['phone2']),  
        ':price' => $data['price'], 
        ':parking' => ( isset($data['parking']) ? $data['parking'] : 0 ), 
        ':ac' => ( isset($data['ac']) ? $data['ac'] : 0 ),
        ':wifi' => ( isset($data['wifi']) ? $data['wifi'] : 0 ),
        ':sauna' => ( isset($data['sauna']) ? $data['sauna'] : 0 ),
        ':description' => $data['description'],         
        ':owner' => $id,
        ':photo' => $data['photo'],
        ':map_icon' => $data['map_icon']
        ));
    }

    // это запрос на добавление паркинга
    if ($data['map_icon'] == 'parking')
    {
        $price = preg_replace('([a-zA-Zа-яА-Я\s])', '', $data['price']);        

        // запишем в базу
        $f3->get('DB')->exec('INSERT LOW_PRIORITY INTO `parkings` (`id_parking`, `lat`, `lng`, `phone1`, `phone2`, `price`, `access`, `camera`, `security`, `big`, `description`, `owner`, `id_city`, `map_icon`, `date_upd`) 
        VALUES (:id_parking, :lat, :lng, :phone1, :phone2, :price ,:access, :camera, :security, :big, :description, :owner, :id_city, :map_icon, NOW() )
        ON DUPLICATE KEY UPDATE 
        `id_parking` = :id_parking,
        `lat` = :lat,
        `lng` = :lng,
        `phone1` = :phone1,
        `phone2` = :phone2,
        `price` = :price,
        `access` = :access,
        `camera` = :camera,
        `security` = :security,
        `big` = :big,
        `description` = :description,
        `id_city` = :id_city,
        `map_icon` = :map_icon,
        `date_upd` = NOW()
        ',array(
        ':id_parking' => ($data['id'] ? $data['id'] : 0),
        ':lat' => $data['lat'], 
        ':lng' => $data['lng'], 
        ':phone1' => preg_replace("/[^0-9]/", '', $data['phone1']), 
        ':phone2' => preg_replace("/[^0-9]/", '', $data['phone2']),  
        ':price' => $price, 
        ':access' => ( isset($data['access']) ? $data['access'] : 0 ), 
        ':camera' => ( isset($data['camera']) ? $data['camera'] : 0 ),
        ':security' => ( isset($data['security']) ? $data['security'] : 0 ),
        ':big' => ( isset($data['big']) ? $data['big'] : 0 ),
        ':description' => $data['description'],         
        ':owner' => $id,             
        ':id_city' => 0,
        ':map_icon' => $data['map_icon']
        ));
    }
    
    // это запрос на добавление места сбора    
    if ($data['map_icon'] == 'place')        
    {
        // запишем в базу
        $f3->get('DB')->exec("INSERT LOW_PRIORITY INTO `places` (`id_place`, `name`, `lat`, `lng`, `description`, `owner`, `id_city`, `map_icon`, `date_upd`) 
        VALUES (:id_place, :name, :lat, :lng, :description, :owner, :id_city, 'place', NOW() )
        ON DUPLICATE KEY UPDATE 
        `id_place` = :id_place,
        `name` = :name,
        `lat` = :lat,
        `lng` = :lng,
        `description` = :description,
        `date_upd` = NOW(),
        `id_city` = :id_city
        ",array(
        ':id_place' => ($data['id'] ? $data['id'] : 0),
        ':lat' => $data['lat'], 
        ':lng' => $data['lng'], 
        ':name' => $data['name'],
        ':description' => $data['description'],         
        ':owner' => $id,             
        ':id_city' => 0,
        ));
    }

    // это запрос на добавление мото-сервиса
    if ($data['map_icon'] == 'service')
    {
        // запишем в базу
        $f3->get('DB')->exec('INSERT LOW_PRIORITY INTO `services` (`id_service`, `name`, `phone1`, `phone2`, `lat`, `lng`, `electric`, `weld`, `stock`, `tuning`, `renewal`, `germans`, `japanese`, `chinese`, `description`, `owner`, `id_city`, `map_icon`, `date_upd`) 
        VALUES (:id_service, :name, :phone1, :phone2, :lat, :lng, :electric, :weld, :stock, :tuning, :renewal, :germans, :japanese, :chinese, :description, :owner, :id_city, :map_icon, NOW() )
        ON DUPLICATE KEY UPDATE 
        `id_service` = :id_service,
        `name` = :name,
        `phone1` = :phone1,
        `phone2` = :phone2,
        `lat` = :lat,
        `lng` = :lng,
        `electric` = :electric,
        `weld` = :weld,
        `stock` = :stock,
        `tuning` = :tuning,
        `renewal` = :renewal,
        `germans` = :germans,
        `japanese` = :japanese,
        `chinese` = :chinese,
        `description` = :description,
        `id_city` = :id_city,
        `date_upd` = NOW()
        ',array(
        ':id_service' => ($data['id'] ? $data['id'] : 0),
        ':lat' => $data['lat'], 
        ':lng' => $data['lng'], 
        ':name' => $data['name'],
        ':phone1' => preg_replace("/[^0-9]/", '', $data['phone1']), 
        ':phone2' => preg_replace("/[^0-9]/", '', $data['phone2']),  
        ':electric' => ( isset($data['electric']) ? $data['electric'] : 0 ), 
        ':weld' => ( isset($data['weld']) ? $data['weld'] : 0 ),         
        ':stock' => ( isset($data['stock']) ? $data['stock'] : 0 ),                 
        ':tuning' => ( isset($data['tuning']) ? $data['tuning'] : 0 ),                         
        ':renewal' => ( isset($data['renewal']) ? $data['renewal'] : 0 ),                                 
        ':germans' => ( isset($data['germans']) ? $data['germans'] : 0 ),                                         
        ':japanese' => ( isset($data['japanese']) ? $data['japanese'] : 0 ),                                                 
        ':chinese' => ( isset($data['chinese']) ? $data['chinese'] : 0 ),                                                         
        ':description' => $data['description'],         
        ':owner' => $id,             
        ':id_city' => 0,
        ':map_icon' => $data['map_icon']
        ));
    }
    
    // это запрос на добавление шиномонтажа
    if ($data['map_icon'] == 'tireservice')
    {
        // запишем в базу
        $f3->get('DB')->exec('INSERT LOW_PRIORITY INTO `tireservices` (`id_tireservice`, `phone1`, `phone2`, `lat`, `lng`, `podkat`, `balancer`, `rims`, `tire_repair`, `description`, `owner`, `id_city`, `map_icon`, `date_upd`) 
        VALUES (:id_tireservice, :phone1, :phone2, :lat, :lng, :podkat, :balancer, :rims, :tire_repair, :description, :owner, :id_city, :map_icon, NOW() )
        ON DUPLICATE KEY UPDATE 
        `id_tireservice` = :id_tireservice,
        `phone1` = :phone1, 
        `phone2`= :phone2, 
        `lat`= :lat, 
        `lng` = :lng, 
        `podkat` = :podkat, 
        `balancer` = :balancer, 
        `rims` = :rims, 
        `tire_repair` = :tire_repair, 
        `description` = :description, 
        `id_city` = :id_city,
        `date_upd` = NOW()
        ',array(
        ':id_tireservice' => ($data['id'] ? $data['id'] : 0),
        ':lat' => $data['lat'], 
        ':lng' => $data['lng'], 
        ':phone1' => preg_replace("/[^0-9]/", '', $data['phone1']), 
        ':phone2' => preg_replace("/[^0-9]/", '', $data['phone2']),  
        ':podkat' => ( isset($data['podkat']) ? $data['podkat'] : 0 ), 
        ':balancer' => ( isset($data['balancer']) ? $data['balancer'] : 0 ),         
        ':rims' => ( isset($data['rims']) ? $data['rims'] : 0 ),                 
        ':tire_repair' => ( isset($data['tire_repair']) ? $data['tire_repair'] : 0 ),                         
        ':description' => $data['description'],         
        ':owner' => $id,             
        ':id_city' => 0,
        ':map_icon' => $data['map_icon']
        ));
    }
    
    echo json_encode(array("lastInsertId" => $f3->get('DB')->lastInsertId()));
});





//! карта - loadPOIs
// выдает точки на карте, которые появились/изменились с $since
$f3->route(array('POST /map/loadpois'),
	function($f3,$params) {
//    $id = checkToken($f3->get('HEADERS')['X-Access-Token']);
    $data = json_decode($f3->get('BODY'),true);
    $since = $data['since'];

    $hotels = $f3->get('DB')->exec('SELECT *, UNIX_TIMESTAMP(date_upd) as ts FROM hotels where UNIX_TIMESTAMP(date_upd) > :since', array(':since' => $since));
    $parkings = $f3->get('DB')->exec('SELECT *, UNIX_TIMESTAMP(date_upd) as ts FROM parkings where UNIX_TIMESTAMP(date_upd) > :since', array(':since' => $since));
    $places = $f3->get('DB')->exec('SELECT *, UNIX_TIMESTAMP(date_upd) as ts FROM places where UNIX_TIMESTAMP(date_upd) > :since', array(':since' => $since));
    $services = $f3->get('DB')->exec('SELECT *, UNIX_TIMESTAMP(date_upd) as ts FROM services where UNIX_TIMESTAMP(date_upd) > :since', array(':since' => $since));
    $tireservices = $f3->get('DB')->exec('SELECT *, UNIX_TIMESTAMP(date_upd) as ts FROM tireservices where UNIX_TIMESTAMP(date_upd) > :since', array(':since' => $since));

    echo json_encode(array("hotels" => $hotels, "parkings" => $parkings, "places" => $places, "services" => $services, "tireservices" => $tireservices));

    }, 0 // кеширование в сек, больше нельзя
);



//! счетчик входов
$f3->route(array('POST /users/count'),
	function($f3,$params) {
    $id = checkToken($f3->get('HEADERS')['X-Access-Token']);

    // запишем в блокнотик 
    $rows = $f3->get('DB')->exec("UPDATE `users` SET `ip_last_login` = :ip_last_login, login_count = login_count + 1, `date_last_login`= NOW(), `useragent` = :useragent WHERE `id_user` = :id_user", array(':ip_last_login' => $_SERVER['HTTP_X_REAL_IP'], ':id_user' => $id, ':useragent' => $f3->AGENT));

    echo json_encode(array("count" => $rows));

/*
echo('<pre>');
print_r($f3);
echo('</pre>');
*/

    }, 0 // кеширование в сек, больше нельзя
);





//
// заглушка
//
$f3->route(array('GET /'),
	function($f3,$params) {
    $f3->error('401','{"error":"Unauthorized"}');    	
    //    header( 'Location: http://motohelplist.com/api.html', true, 303 ); 
    });
    
    
$f3->run();
