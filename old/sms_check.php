<?
    if ( $_GET == '' && basename(__FILE__) == basename($_SERVER['PHP_SELF']) ) 
    { 
        header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); 
        die (); 
    }
    
    include_once('../config/config.inc.php');
    $sms_id = $_GET['sms_id'];
    $code = $_GET['code'];
    $phone = $_GET['phone'];
    
    if ($sms_id)
    {
        
        // ищем такую смс в базе
        $stmt = $pdo->query("select `id_sms` from `sms` where `id_sms_gate` = \"$sms_id\" ");    
        $id_sms = $stmt->fetchColumn();
        
// если смс имеет статус отличный от delivered, то идем дальше
$stmt = $pdo->query("SELECT id_sms, status from sms where id_sms_gate = \"$sms_id\" ");    
$stmt = $stmt->fetch();

if ($stmt->status != 'delivered')
{
    $id_sms = $stmt->id_sms;
}
// иначе не лохматим бабушку и выходим
else exit();        


        if ($id_sms) // если нашли 
        {
            // запрашиваем ее новый статус
            include('sms.php'); 
            $status = explode(';', $status);
            
            // пишем новый статус в базу
            $stmt = $pdo->prepare('UPDATE `sms` SET `status` = :status WHERE `id_sms` = :id_sms');
            $stmt->execute(array(':status' => $status['1'], ':id_sms' =>  $id_sms));

            // смотрим сколько прошло времени
            $stmt = $pdo->query("SELECT id_sms, code, status, MINUTE(TIMEDIFF(NOW(),date_sms)) as timeout FROM `sms` WHERE `id_sms` = $id_sms ");
            $stmt = $stmt->fetch();

            // если не "доставлено" и не "в очереди" и не "отправлено  СМС центр"
            if ($stmt->status != 'delivered' AND $stmt->status != 'queued' AND $stmt->status != 'smsc submit')
            { 
              echo $stmt->code;  
            }
            else if ($stmt->timeout > 1 ) echo $stmt->code;
            
            else 
            {
                echo $stmt->status;
            }
        }
        else // если не нашли 
        { 
            exit('Нет СМС с таким id');
        }
        
        

//echo $id_sms;
        


    }
    else 
        if ($code AND $phone)
        {
            $stmt = $pdo->query("SELECT id_sms FROM sms WHERE code = \"$code\" AND phone = \"$phone\" ");
            $passed = $stmt->fetchColumn();
            if ($passed) echo '1'; else echo '0'; 
        }
?>