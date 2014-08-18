<?php
/**
 * User: denis
 * Date: 2014-08-12
 */

require_once(__DIR__ . '/../Lib/php/Cypher.php');

class ProcessData {

    private static $auth;
    private static $user_id;
    public static $error_title = 'Secrets Request Error:';

    public static function make_call($url, $post=false, $post_data=array()){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:'.self::get_auth(),
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_POST, $post);
        if($post){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            error_log(self::$error_title."services connection issue\n".curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    private static function set_auth(){
        // TODO - find a better way to obtain this value. Maybe from a DB.
        self::$auth = 'test';
    }

    private static function get_auth(){
        if(is_null(self::$auth)){
            self::set_auth();
        }
        return self::$auth;
    }

    public static function clean_input($post_element){
        return empty($_POST[$post_element]) ? '' : $_POST[$post_element];
    }

    public static function do_nothing($data){
        return $data;
    }

    private static function base_process($data){
        return json_decode(base64_decode($data), true);
    }

    public static function list_secrets($data){
        $secrets = self::base_process($data);
        $display_secrets = '';
        foreach($secrets as $row){
            $display_secrets .= "<tr id='secret_".$row['id']."' class='secret_row'>\r\n";
            $display_secrets .= "  <td></td>\r\n";
            $display_secrets .= '  <td class="secret_name">'.$row['name']."</td>\r\n";
            $display_secrets .= "  <td></td>\r\n";
            $display_secrets .= '</tr>';
        }
        return $display_secrets;
    }

    public static function display($data){
        $secret = self::base_process($data);
        $display_secret = '';
        if(!empty($secret)){
            $secret['url'] = (strpos($secret['url'], 'http')!==false ? '' : 'http://').$secret['url'];
            $display_secret .= "<tr>\r\n";
            $display_secret .= "  <td></td>\r\n";
            $display_secret .= '  <td colspan="2" class="display_secret">'."\r\n";
            $display_secret .= '      <label>Username: ';
            $display_secret .= '          <input type="text" name="secret_username" class="form-control" value="'.$secret['username'].'" readonly/>';
            $display_secret .= self::display_unlock_button('username', 1);
            $display_secret .= self::display_copy_button();
            $display_secret .= '      </label>'."\r\n";
            $display_secret .= '      <label>Password: ';
            $display_secret .= '          <input type="password" name="secret_password" class="form-control" placeholder="'.str_repeat("&bull;", $secret['password_length']).'" readonly/>';
            $display_secret .= self::display_unlock_button('password', 2);
            $display_secret .= '          <button type="button" title="show/hide" id="show_password" class="btn btn-default glyphicon glyphicon-eye-open" onclick="secretField.revealHandler();"></button>';
            $display_secret .= self::display_copy_button();
            $display_secret .= '      </label>'."\r\n";
            $display_secret .= '      <label>URL: ';
            $display_secret .= '          <input type="text" name="secret_url" class="form-control" value="'.$secret['url'].'" readonly/>';
            $display_secret .= self::display_unlock_button('url', 3);
            $display_secret .= '          <button type="button" title="open" id="open" class="btn btn-default glyphicon glyphicon-new-window" onclick="window.open(\''.$secret['url'].'\');"></button>';
            $display_secret .= self::display_copy_button();
            $display_secret .= '      </label>'."\r\n";
            $display_secret .= '      <label>Notes: ';
            $display_secret .= '          <textarea name="secret_notes" class="form-control" readonly>'.$secret['notes'].'</textarea>';
            $display_secret .= self::display_unlock_button('notes', 4);
            $display_secret .= '      </label>'."\r\n";
            $display_secret .= "      <div id='btn_zone'>\r\n";
            $display_secret .= '          <button type="button" class="btn btn-default glyphicon glyphicon glyphicon-repeat" onclick="secretField.revert();"></button>';
            $display_secret .= '          <button type="button" class="btn btn-success glyphicon glyphicon-floppy-saved" onclick="secrets.save('.$secret['id'].');"></button>';
            $display_secret .= '          <button type="button" class="btn btn-danger glyphicon glyphicon-trash" onclick="secrets.del('.$secret['id'].');"></button>';
            $display_secret .= "      </div>\r\n";
            $display_secret .= '  </td>\r\n';
            $display_secret .= '</tr>';
        }
        return $display_secret;
    }

    private static function display_unlock_button($element, $node){
        return '          <button type="button" title="edit" id="edit_'.$element.'" class="btn btn-default glyphicon glyphicon-pencil" onclick="secretField.edit('.$node.');"></button>';
    }

    private static function display_copy_button(){
        //'          <img src="" alt="copy" title="copy"/>';    //TODO - get copy code.
        return '';
    }

    public static function encrypt($user, $data){
        $cypher = new Cypher($user, __DIR__.'/../config/config.user_key.json');
        return $cypher->encrypt($data);
    }

    public static function decrypt($user, $data){
        $cypher = new Cypher($user, __DIR__.'/../config/config.user_key.json');
        return $cypher->decrypt($data);
    }

    public static function get_password($data){
        $password_data = self::base_process($data);
        $decrypted = ProcessData::decrypt(self::get_user_id(), $password_data['encrypted_password']);
        return substr($decrypted, 0, $password_data['password_length']);
    }

    public static function set_user_id($user_id){
        self::$user_id = $user_id;
    }

    public static function get_user_id(){
        return self::$user_id;
    }

    private static function get_env(){
        return getenv('environment');
    }

    public static function get_url(){
        if(self::get_env() == 'live'){
            return 'https://services.jdenoc.com/api/secrets/';
        } else {
            return 'http://services.local/api/secrets/';
        }
    }
} 