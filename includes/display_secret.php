<?php
/**
 * User: denis
 * Date: 2014-05-25
 */

require_once(__DIR__ . '/../Lib/php/PDO_Connection.php');
$db = new PDO_Connection('jdenocco_secrets', __DIR__.'/../config/config.db.php');
$secret = $db->getRow("SELECT * FROM secrets WHERE user_id=:user AND id=:sid", array('user'=>$_REQUEST['user'], 'sid'=>$_REQUEST['secret_id']));
$secret['url'] = (strpos($secret['url'], 'http')!==false ? '' : 'http://').$secret['url'];
$display_secret = '';
$display_secret .= "<tr>\r\n";
$display_secret .= "  <td></td>\r\n";
$display_secret .= '  <td colspan="2" class="display_secret">'."\r\n";
$display_secret .= '      <label>Username: ';
$display_secret .= '          <input type="text" name="secret_username" class="form-control" value="'.$secret['username'].'" readonly/>';
$display_secret .= '          <button type="button" title="edit" id="edit_username" class="btn btn-default glyphicon glyphicon-pencil" onclick="secretField.edit(1);"></button>';
//$display_secret .= '          <img src="" alt="copy" title="copy"/>';    TODO - get copy code.
$display_secret .= '      </label>'."\r\n";
$display_secret .= '      <label>Password: ';
$display_secret .= '          <input type="password" name="secret_password" class="form-control" placeholder="'.str_repeat("&bull;", $secret['password_length']).'" readonly/>';
$display_secret .= '          <button type="button" title="edit" id="edit_password" class="btn btn-default glyphicon glyphicon-pencil" onclick="secretField.edit(2);"></button>';
$display_secret .= '          <button type="button" title="edit" id="show_password" class="btn btn-default glyphicon glyphicon-eye-open" onclick="secretField.revealHandler();"></button>';
//$display_secret .= '          <img src="" alt="copy" title="copy"/>';   TODO - get copy code.
$display_secret .= '      </label>'."\r\n";
$display_secret .= '      <label>URL: ';
$display_secret .= '          <input type="text" name="secret_url" class="form-control" value="'.$secret['url'].'" readonly/>';
$display_secret .= '          <button type="button" title="edit" id="edit_url" class="btn btn-default glyphicon glyphicon-pencil" onclick="secretField.edit(3);"></button>';
$display_secret .= '          <button type="button" title="open" id="open" class="btn btn-default glyphicon glyphicon-new-window" onclick="window.open(\''.$secret['url'].'\');"></button>';
//$display_secret .= '          <img src="" alt="copy" title="copy"/>';    TODO - get copy code.
$display_secret .= '      </label>'."\r\n";
$display_secret .= '      <label>Notes: ';
$display_secret .= '          <textarea name="secret_notes" class="form-control" readonly>'.$secret['notes'].'</textarea>';
$display_secret .= '          <button type="button" title="edit" id="edit_username" class="btn btn-default glyphicon glyphicon-pencil" onclick="secretField.edit(4);"></button>';
$display_secret .= '      </label>'."\r\n";
$display_secret .= "      <div id='btn_zone'>\r\n";
$display_secret .= '          <button type="button" class="btn btn-default glyphicon glyphicon glyphicon-repeat" onclick="secretField.revert();"></button>';  // TODO - make revert work changes
$display_secret .= '          <button type="button" class="btn btn-success glyphicon glyphicon-floppy-saved" onclick="secrets.save('.$secret['id'].');"></button>';
$display_secret .= '          <button type="button" class="btn btn-danger glyphicon glyphicon-trash" onclick="secrets.del('.$secret['id'].');"></button>';
$display_secret .= "      </div>\r\n";
$display_secret .= '  </td>\r\n';
$display_secret .= '</tr>';

echo base64_encode($display_secret);
