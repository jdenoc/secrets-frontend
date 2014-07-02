<?php
/**
 * User: denis
 * Date: 2014-05-25
 *
 * TODO  - build out encryption/decryption code 
 */

require_once(__DIR__ . '/Connection.php');
$db = new Connection('jdenocco_secrets');
$secret = $db->getRow("SELECT * FROM secrets WHERE user_id=:user AND id=:sid", array('user'=>$_REQUEST['user'], 'sid'=>$_REQUEST['secret_id']));
echo "<tr>\r\n";
echo "  <td></td>\r\n";
echo '  <td colspan="2" class="display_secret">'."\r\n";
echo '      <label>Username: ';
echo '          <input type="text" name="secret_username" class="form-control" value="'.$secret['username'].'" readonly/>';
echo '          <button type="button" title="edit" id="edit_username" class="btn btn-default glyphicon glyphicon-pencil" onclick="secretField.edit(1);"></button>';
//echo '          <img src="" alt="copy" title="copy"/>';    TODO - get copy code.
echo '      </label>'."\r\n";
echo '      <label>Password: ';
echo '          <input type="password" name="secret_password" class="form-control" value="'.$secret['password'].'" readonly/>';
echo '          <button type="button" title="edit" id="edit_password" class="btn btn-default glyphicon glyphicon-pencil" onclick="secretField.edit(2);"></button>';
echo '          <button type="button" title="edit" id="show_password" class="btn btn-default glyphicon glyphicon-eye-open" onmousedown="secretField.reveal();" onmouseup="secretField.unreveal();"></button>';
//echo '          <img src="" alt="copy" title="copy"/>';   TODO - get copy code.
echo '      </label>'."\r\n";
echo '      <label>URL: ';
echo '          <input type="text" name="secret_url" class="form-control" value="'.$secret['url'].'" readonly/>';
echo '          <button type="button" title="edit" id="edit_url" class="btn btn-default glyphicon glyphicon-pencil" onclick="secretField.edit(3);"></button>';
echo '          <button type="button" title="open" id="open" class="btn btn-default glyphicon glyphicon-new-window" onclick="window.open(\''.$secret['url'].'\');"></button>';
//echo '          <img src="" alt="copy" title="copy"/>';    TODO - get copy code.
echo '      </label>'."\r\n";
echo '      <label>Notes: ';
echo '          <textarea name="secret_memo" class="form-control" readonly>'.$secret['text'].'</textarea>';
echo '          <button type="button" title="edit" id="edit_username" class="btn btn-default glyphicon glyphicon-pencil" onclick="secretField.edit(4);"></button>';
echo '      </label>'."\r\n";
echo "      <div id='btn_zone'>\r\n";
echo '          <button type="button" class="btn btn-success glyphicon glyphicon-floppy-saved"></button>';  // TODO - make save changes
echo '          <button type="button" class="btn btn-danger glyphicon glyphicon-trash"></button>';          // TODO - make delete secret
echo "      </div>\r\n";
echo '  </td>\r\n';
echo '</tr>';
