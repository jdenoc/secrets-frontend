<?php
/**
 * User: denis
 * Date: 2014-05-25
 */

require_once(__DIR__ . '/Connection.php');
$db = new Connection('jdenocco_secrets');
$secrets = $db->getAllRows("SELECT id, `name` FROM secrets WHERE user_id=:user", array('user'=>$_REQUEST['user']));
foreach($secrets as $row){
    echo "<tr id='secret_".$row['id']."' onclick='displaySecret(".$row['id'].");'>\r\n";
    echo "  <td></td>\r\n";
    echo '  <td class="secret_name">'.$row['name']."</td>\r\n";
    echo "  <td></td>\r\n";
    echo '</tr>';
}