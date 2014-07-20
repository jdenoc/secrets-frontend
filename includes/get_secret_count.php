<?php
/**
 * User: denis
 * Date: 2014-05-25
 */

require_once(__DIR__ . '/../Lib/php/PDO_Connection.php');
$db = new PDO_Connection('jdenocco_secrets', __DIR__.'/../config/config.db.php');
$count = $db->count("secrets", "user_id=:user", array('user'=>$_REQUEST['user']));
print $count;