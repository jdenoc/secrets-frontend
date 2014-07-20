<?php
/**
 * User: denis
 * Date: 2014-07-05
 */

require_once(__DIR__ . '/../Lib/php/PDO_Connection.php');
require_once(__DIR__ . '/../Lib/php/Cypher.php');
$config_dir = __DIR__.'/../config/config.';
$secret_data = json_decode(base64_decode($_POST['data']), true);
if(empty($secret_data['user']) || !is_numeric($secret_data['user'])){
    print 0;
} else {
    $db = new PDO_Connection('jdenocco_secrets', $config_dir.'db.php');
    $cypher = new Cypher($secret_data['user'], $config_dir.'user_key.json');
    if(isset($secret_data['id'])){
        // UPDATE existing SECRET
        $data = array();
        foreach($secret_data as $key=>$value){
            if(!empty($value)){
                if($key=='password'){
                    $data['password_length'] = strlen($value);
                    $data['encrypted_password'] = $cypher->encrypt($value.$_GET['x']);
                } elseif(in_array($key, array('id', 'user'))) {
                    // Do Nothing
                } else {
                    $data[$key] = $value;
                }
            }
        }
        if(!empty($data)){
            $db->update(
                'secrets',
                $data,
                "id=:sid AND user_id=:uid",
                array('sid'=>$secret_data['id'], 'uid'=>$secret_data['user'])
            );
        } else {
            print 0;
            exit;
        }
    } else {
        // New SECRET
        $password_length = strlen($secret_data['password']);
        $encrypted_password = $cypher->encrypt($secret_data['password'].$_GET['x']);
        $db->insert('secrets', array(
            'user_id'=>$secret_data['user'],
            'name'=>$secret_data['name'],
            'url'=>$secret_data['url'],
            'username'=>$secret_data['username'],
            'encrypted_password'=>$encrypted_password,
            'password_length'=>$password_length,
            'notes'=>$secret_data['notes'],
            'create_stamp'=>'NOW()'
        ));
    }
    print 1;
}