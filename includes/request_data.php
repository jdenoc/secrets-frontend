<?php
/**
 * User: denis
 * Date: 2014-07-27
 */

if(empty($_REQUEST['user'])){
    exit;
} else {
    $user = $_REQUEST['user'];
}

switch($_REQUEST['type']){
    case 'count':
        $uri = 'count/id/'.$user;
        $post = false;
        $decrypt = false;
        $post_data = array();
        break;

    case 'delete':
        $uri = 'delete/id/'.$_POST['secret_id'].'/user/'.$user;
        $post = false;
        $decrypt = false;
        $post_data = array();
        break;

    case 'list':
        $limit = empty($_POST['limit']) ? 50 : $_POST['limit'];
        $start = intval($_POST['start']);
        $uri = 'list/id/'.$user.'/start/'.$start.'/limit/'.$limit;
        $post = false;
        $decrypt = false;
        $post_data = array();
        break;

    case 'get':
        $id = intval($_REQUEST['secret_id']);
        $uri = 'display/id/'.$id.'/user/'.$user;
        $post = false;
        $decrypt = false;
        $post_data = array();
        break;

    case 'save':
        $uri = 'save';
        $post = true;
        $decrypt = false;
        $secret_data = json_decode(base64_decode($_POST['data']), true);
        if(!empty($secret_data['password'])){
            $encrypt_url = 'http'.(empty($_SERVER['HTTPS']) ? '' : 's').'://'.$_SERVER['SERVER_NAME'].'/includes/crypt.php';
            $secret_data['password_length'] = strlen($secret_data['password']);
            $secret_data['encrypted_password'] = make_call($encrypt_url.'?u='.$user.'&t=1&d='.urlencode($secret_data['password'].$_GET['x']));
            unset($secret_data['password'], $encrypt_url);
        }
        $post_data = array(
            'user'=>$user,
            'data'=>base64_encode(json_encode($secret_data))
        );
        break;

    case 'password':
        $id = intval($_REQUEST['secret_id']);
        $uri = 'password/id/'.$id.'/user/'.$user;
        $post = false;
        $decrypt = true;
        $post_data = array();
        break;

    default:
        $uri = '';
}

$api_url = 'http://services.local/index.php/api/secrets/';
$json_response = make_call($api_url.$uri, $post, $post_data);
$response_array = json_decode($json_response, true);

if(empty($response_array['error'])){
    if($decrypt){
        $decrypt_url = 'http'.(empty($_SERVER['HTTPS']) ? '' : 's').'://'.$_SERVER['SERVER_NAME'].'/includes/crypt.php';
        $decrypted = make_call($decrypt_url.'?u='.$user.'&t=0&d='.urlencode($response_array['result']['encrypted_password']));
        echo base64_encode(substr($decrypted, 0, $response_array['result']['password_length']));
    } else {
        echo $response_array['result'];
    }
} else {
    error_log($response_array['error']);
}

function make_call($url, $post=false, $post_data=array()){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Api:test',
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
        error_log("services connection issue".curl_error($ch));
    }
    curl_close($ch);
    return $result;
}
