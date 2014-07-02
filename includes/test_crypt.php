<?php
/**
 * User: denis
 * Date: 2014-06-30
 */

require_once(__DIR__ . '/../Lib/Cypher.php');

$user = 1;
$password = '123456789*';

for($i=0; $i<=2; $i++){
    $cyper = new Cypher($user);
    $encrypted_pass = $cyper->encrypt($password);
    $decrypted_pass = $cyper->decrypt($encrypted_pass);

    echo 'round:'.($i+1)."\r\n";
    echo 'password:'.$password."\r\n";
    echo 'encrypt:'.$encrypted_pass."\r\n";
    echo 'decrypt:'.$decrypted_pass."\r\n";
    echo 'errors:'.$cyper->get_error_message()."\r\n";
    $cyper->save_user_keys();
    $cyper->add_user_key($user, 'test');
    $user++;
}
