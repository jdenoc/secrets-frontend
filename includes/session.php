<?php
/**
 * User: jdenoc
 * Date: 2014-03-05
 */

require_once(__DIR__ . '/../Lib/php/PDO_Connection.php');
$db = new PDO_Connection('jdenocco_secrets', __DIR__.'/../config/config.db.php');
$user = $db->getValue("SELECT id FROM users WHERE email=:email", array('email'=>$_REQUEST['email']));
if(empty($user)){
    print 0;
} else {
    $session_title = include_once(__DIR__ . '/../config/config.session.php');
    session_name($session_title);
    session_start();
    $_SESSION['name'] = $_REQUEST['name'];
    $_SESSION['pic'] = $_REQUEST['pic'];
    $_SESSION['email'] = $_REQUEST['email'];
    $_SESSION['user_id'] = $user;

    print 1;
}