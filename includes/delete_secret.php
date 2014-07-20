<?php
/**
 * User: denis
 * Date: 2014-07-06
 */

require_once(__DIR__ . '/../Lib/php/PDO_Connection.php');
if(empty($_REQUEST['user']) || !is_numeric($_REQUEST['user'])){
    print 0;
} else {
    $db = new PDO_Connection('jdenocco_secrets', __DIR__.'/../config/config.db.php');
    $db->delete(
        'secrets',
        "user_id=:uid AND id=:sid",
        array('uid'=>$_REQUEST['user'],'sid'=>$_REQUEST['secret_id'])
    );
    print 1;
}