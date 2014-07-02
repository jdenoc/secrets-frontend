<?php
/**
 * User: jdenoc
 * Date: 2014-05-11
 */

$session_title = include_once(__DIR__ . '/config/config.session.php');
session_name($session_title);
session_start();
if(!empty($_SESSION['email'])){
    header('Location: main.php');
    exit;
}

?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signin</title>
    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Custom styles for this template -->
    <link href="css/login.css" rel="stylesheet" type="text/css" />
    <link href='http://fonts.googleapis.com/css?family=Niconne' rel='stylesheet' type='text/css'>

    <script type="text/javascript" src="js/gapi.plus.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="http://apis.google.com/js/client:plusone.js"></script>
</head>
<body>

<div id="wrapper">
    <h1>Secrets</h1>
    <div id="signin-button" class="show">
        <h4>Login using Google+</h4>
        <div
            class="g-signin"
            data-callback="loginFinishedCallback"
            data-clientid="821440935585-7ldieb0965if15sqfagnjvtgljjknj65.apps.googleusercontent.com"
            data-scope="https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.profile.emails.read"
            data-approvalprompt="force"
            data-cookiepolicy="single_host_origin"
            data-requestvisibleactions="http://schemas.google.com/AddActivity"
            ></div><br/>
        <a href="">Signup</a>
    </div>

    <div id="profile" class="hide">
        <div>
            Hello <span id="name"></span>,<br/>
            It seems that you don't have permission to access this site right now.<br/>
            Why not contact the <a href="mailto:info@jdenoc.com">admin</a> to get setup.
        </div>
    </div>
</div>

</body>
</html>