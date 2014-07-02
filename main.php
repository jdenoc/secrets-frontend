<?php
/**
 * User: denis
 * Date: 2014-05-23
 */
$session_title = include_once(__DIR__ . '/config/config.session.php');
session_name($session_title);
session_start();
if(empty($_SESSION['email'])){
    header('Location: logout.php');
    exit;
}
require_once(__DIR__ . '/Lib/PDO_Connection.php');
$db = new Connection('jdenocco_secrets');
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <!--  TODO - <link rel="shortcut icon" href="../../assets/ico/favicon.ico">-->
    <title>Secrets</title>
    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <!-- Custom styles for this template -->
    <link href="css/dashboard.css" rel="stylesheet" type="text/css"/>
    <link href="css/custom_bootstrap.css" rel="stylesheet" type="text/css"/>

    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript"><?php echo 'var user = "'.$_SESSION['user_id'].'";'; ?></script>
    <script type="text/javascript" src="js/main.js"></script>
    <link href="css/loading.css" rel="stylesheet" type="text/css" />
</head>
<body>

<!-- Top Nav Bar -->
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!-- TODO - create logo -->
            <a class="navbar-brand" href="#">Secrets</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#" data-toggle="modal" data-target="#add-modal" id="add_secret">Add Secret</a></li>
                <li><a href="#" data-toggle="dropdown" id="user_menu"><img src="<?php echo $_SESSION['pic']; ?>" alt="<?php echo $_SESSION['email']; ?>" /></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="user_menu">
                        <li role="presentation" class="dropdown-header"><?php echo $_SESSION['name']; ?></li>
                        <li role="presentation" class="divider"></li>
                        <li><a href="settings.php"><span class="glyphicon glyphicon-cog"></span> Settings</a></li>
                        <li role="presentation" class="divider"></li>
                        <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- END - Top Nav Bar -->

<!-- Main body -->
<div class="container-fluid">
    <div class="row">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-condensed" id="secrets_table">
                <tr>
                    <th></th>
                    <th>Secret Name</th>
                    <th></th>
                </tr>
            </table>
            <button type="button" class="btn btn-default" id="prev"><span class="glyphicon glyphicon-chevron-left"></span></button>
            <button type="button" class="btn btn-default" id="next"><span class="glyphicon glyphicon-chevron-right"></span></button>
        </div>
    </div>
</div>
<!-- END - Main body -->

<!-- New Secret Modal -->
<div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="entry-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">New Secret</h4>
            </div>
            <div class="modal-body">
                <label><span>URL:</span><input type="text" name="url" id="add-url" class="form-control"/></label>
                <label><span>Username:</span><input type="text" name="username" id="add-username" class="form-control"/></label>
                <label><span>Password:</span><input type="text" name="password" id="add-password" class="form-control"/></label>
                <label><span>Notes:</span><textarea name="notes" id="add-notes" class="form-control"></textarea></label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="add-cancel">Cancel</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="add-save"><span class="glyphicon glyphicon-ok"></span> Save</button>
                <input type="hidden" name="secret_data" id="secret_data"/>
            </div>
        </div>
    </div>
</div>
<!-- END - New Secret Modal -->

</body>
</html>