<?php
    // по прошествии DATEDIFF(NOW(),`date_add`) > Х суток устанавливает id_urgency важных каналов на ноль
    include_once('../config/config.inc.php');
    $stmt = $pdo->query('UPDATE `webchat_channels` SET `id_urgency` = 0 where `id_urgency` > 0 and `id_urgency` < 3 and  HOUR(TIMEDIFF(NOW(),`date_add`)) > 24');
    print_r($stmt);
?>