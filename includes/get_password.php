<?php
/**
 * User: denis
 * Date: 2014-07-04
 */

require_once(__DIR__ . '/../Lib/php/Cypher.php');
require_once(__DIR__ . '/../Lib/php/PDO_Connection.php');
$cyper = new Cypher($_REQUEST['u'], __DIR__.'/../config/config.user_key.json');
$db = new PDO_Connection('jdenocco_secrets', __DIR__.'/../config/config.db.php');
$secret = $db->getRow(
    "SELECT encrypted_password, password_length
    FROM secrets WHERE id=:sid AND user_id=:uid",
    array('sid'=>$_REQUEST['s'], 'uid'=>$_REQUEST['u'])
);
$decrypted_pass = $cyper->decrypt($secret['encrypted_password']);
echo base64_encode(substr($decrypted_pass, 0, $secret['password_length']));