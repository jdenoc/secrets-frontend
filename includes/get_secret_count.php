<?php
/**
 * User: denis
 * Date: 2014-05-25
 */

require_once(__DIR__ . '/Connection.php');
$db = new Connection('jdenocco_secrets');
$count = $db->count("secrets", "user_id=:user", array('user'=>$_REQUEST['user']));
print $count;