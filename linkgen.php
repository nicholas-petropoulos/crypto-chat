<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nicholas
 * Date: 11/27/2018
 * Time: 11:11 AM
 */

session_start();

include "includes/config.php";

$username = "";
$option = "";
// user does not have to be logged in ot view page
if(isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
}
//
if(isset($_REQUEST["option"])) {
    $session = $_REQUEST["option"];
}
$messageID = 0;
$uniqueID = 0;
$isViewingMsg = false;

// only can generate link if logged in
if($option == "linkgen" && isset($username)) {
    // put message details on page
    $uniqueID = uniqid("msg");
    // OR openssl_random_pseudo_bytes($length)
    // store in db
    // associate with msg id
// dont have to be logged in to view link
} else if($option == "viewlink") {
    // search for unique id from db
    // get msg associated with it
    // countdown timer as normal echoed on this page
    // if above verified set to true to display on page
    $isViewingMsg = true;
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CryptoChat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/style.css" rel="stylesheet"/>
    <link href="css/bootstrap.css" rel="stylesheet" />
    <link href="css/bootstrap-theme.css" rel="stylesheet" />
</head>

<body>
<div class="container container-top">
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"
                        aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">CryptoChat</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!--<li class="active"><a href="#">Learn More <span class="sr-only">(current)</span></a></li> -->
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <?php if(isset($username)) {
                        echo '<li id="logged-in-user"><a href="#">Welcome '. $username .'</a></li>';
                        echo '<li class="menu-btn-log-out"><a href="logout.php">Log Out</a></li>';
                    } else {
                        echo '<li class="menu-btn-login"><a href="login.php">Log In</a></li>';
                    } ?>

                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</div>
<div class="container container-article">
    <div class="jumbotron front-jumbotron">
        <h3>Message:</h3>
        <?php
            if($isViewingMsg) {
                // msg here - escape JS to prevent xss
            }
        ?>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/cryptochat.js"></script>
</body>
<footer>
    <div class="container">
        <p>&copy; CryptoChat 2018. All Rights Reserved.</p>
        <ul class="list-inline">
            <li class="list-inline-item">
                <a href="#">Privacy</a>
            </li>
            <li class="list-inline-item">
                <a href="#">Terms</a>
            </li>
            <li class="list-inline-item">
                <a href="#">FAQ</a>
            </li>
        </ul>
    </div>
</footer>

</html>
