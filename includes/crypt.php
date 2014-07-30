<?php
/**
 * User: denis
 * Date: 2014-07-27
 */

require_once(__DIR__ . '/../Lib/php/Cypher.php');
$user = $_GET['u'];
$cyper = new Cypher($user, __DIR__.'/../config/config.user_key.json');
if(intval($_GET['t'])===0){
    echo $cyper->decrypt($_GET['d']);
} elseif(intval($_GET['t'])===1) {
    echo $cyper->encrypt($_GET['d']);
} else {
    echo '';
}
