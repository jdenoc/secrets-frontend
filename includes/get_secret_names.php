<?php
/**
 * User: denis
 * Date: 2014-05-25
 */

require_once(__DIR__ . '/../Lib/php/PDO_Connection.php');
$db = new PDO_Connection('jdenocco_secrets', __DIR__.'/../config/config.db.php');
$secrets = $db->getAllRows("SELECT id, `name` FROM secrets WHERE user_id=:user", array('user'=>$_REQUEST['user']));
$display_secrets = '';
foreach($secrets as $row){
    $display_secrets .= "<tr id='secret_".$row['id']."' class='secret_row'>\r\n";
    $display_secrets .= "  <td></td>\r\n";
    $display_secrets .= '  <td class="secret_name">'.$row['name']."</td>\r\n";
    $display_secrets .= "  <td></td>\r\n";
    $display_secrets .= '</tr>';
}
echo base64_encode($display_secrets);