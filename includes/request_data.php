<?php
/**
 * User: denis
 * Date: 2014-07-27
 */

require_once(__DIR__.'/process_data.php');

if(empty($_REQUEST['user'])){
    exit;
} else {
    $user = $_REQUEST['user'];
    ProcessData::set_user_id($user);
}

switch($_REQUEST['type']){
    case 'count':
        $uri = 'count/id/'.$user;
        $post = false;
        $post_data = array();
        $callback = 'do_nothing';
        break;

    case 'delete':
        $uri = 'delete/id/'.ProcessData::clean_input('secret_id').'/user/'.$user;
        $post = false;
        $post_data = array();
        $callback = 'do_nothing';
        break;

    case 'list':
        $limit = empty($_POST['limit']) ? 50 : $_POST['limit'];
        $start = intval(ProcessData::clean_input('start'));
        $uri = 'list/id/'.$user.'/start/'.$start.'/limit/'.$limit;
        $post = false;
        $post_data = array();
        $callback = 'list_secrets';
        break;

    case 'get':
        $id = intval(ProcessData::clean_input('secret_id'));
        $uri = 'display/id/'.$id.'/user/'.$user;
        $post = false;
        $post_data = array();
        $callback = 'display';
        break;

    case 'save':
        $uri = 'save';
        $post = true;
        $secret_data = json_decode(ProcessData::clean_input('data'), true);
        if(!empty($secret_data['password'])){
            $secret_data['password_length'] = strlen($secret_data['password']);
            $secret_data['encrypted_password'] = ProcessData::encrypt($user, $secret_data['password'].$_GET['x']);
            unset($secret_data['password'], $encrypt_url);
        }
        $post_data = array(
            'user'=>$user,
            'data'=>base64_encode(json_encode($secret_data))
        );
        $callback = 'do_nothing';
        break;

    case 'password':
        $id = intval(ProcessData::clean_input('secret_id'));
        $uri = 'password/id/'.$id.'/user/'.$user;
        $post = false;
        $post_data = array();
        $callback = 'get_password';
        break;

    default:
        $uri = '';
        $callback = 'do_nothing';
}

$json_response = ProcessData::make_call(ProcessData::get_url().$uri, $post, $post_data);
if(!$response_array = json_decode($json_response, true)){
    error_log(ProcessData::$error_title.$json_response);
} else {
    if(empty($response_array['error'])){
        $response = call_user_func(array('ProcessData', $callback), $response_array['result']);
        echo $response;
    } else {
        error_log(ProcessData::$error_title.$response_array['error']);
        // TODO - do something that causes AJAX to recognise this as an error
    }
}