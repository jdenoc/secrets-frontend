<?php
/**
 * User: jdenoc
 * Date: 2014-05-11
 */

$session_title = include_once(__DIR__ . '/config/config.session.php');
session_name($session_title);
session_start();
$_SESSION['name'] = '';
$_SESSION['pic'] = '';
$_SESSION['email'] = '';

header("Location: index.php");
exit;