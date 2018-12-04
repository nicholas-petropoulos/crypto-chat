<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nicholas
 * Date: 11/21/2018
 * Time: 7:56 PM
 */
// sessions last for 24 hours
ini_set('session.gc_maxlifetime', 86400);
// cookies also last for 24 hours
session_set_cookie_params(86400);
session_start();

if(isset($_SESSION["username"])) {
    header("Location: chat.php");
}

include "includes/config.php";
include "includes/message.php";

$username = $password = $securityPIN = "";
$usernameError = $passwordError = $securityPINError = $noSecurityPINError = $loginError = "";
$publicKey = $privateKey = "";

$msgObj = new message();

if(isset($_POST['btn-login'])){
    if(trim($_POST['username'])=="") {
        $usernameError = "No username entered";
    } else {
        $username = strtolower(trim($_POST['username']));
    }

    if(trim($_POST['password'])=="")
        $passwordError = "No password entered";
    else {
        // check database for password
        $password = trim($_POST['password']);
        $sql = $con->prepare("SELECT password FROM users WHERE username=?");
        if($sql) {
            $sql->bind_param("s", $username);
            $sql->execute();
            $result = $sql->get_result();
            $val = $result->fetch_assoc();
            if (!password_verify($password, $val["password"])) {
                $passwordError = "Invalid password";
            }
        }
    }
    // get security pin
    if(trim($_POST['security-pin'])=="") {
        $noSecurityPINError = "No security PIN entered";
    } else {
        $securityPIN = strtolower(trim($_POST['security-pin']));
    }

    if($usernameError == "" && $passwordError=="" && $noSecurityPINError == ""){
        // get key from db
        $encryptedPrivateKey = $msgObj->getKey("private_key", $username);
        // turn off error reporting or we get an error for wrong key
        //error_reporting(0);

        // returns string or false on failure
        $pkeyTest = openssl_pkey_get_private($encryptedPrivateKey, $securityPIN);
        echo "test: ". $pkeyTest;
        if($pkeyTest == false){
            $securityPINError = "Incorrect PIN entered.";
        }
        // get unencrypted
        openssl_pkey_export($pkeyTest,$privateKey,null, $openSSLConfig);

        echo "Decrypted private key: " . $privateKey;
        if($securityPINError == "") {
            // these values need to be stored in cookies so they can make requests to get keys later
            setcookie("username", $username);
            setcookie("pin", $securityPIN);
            $_SESSION["username"] = $username;
            // TODO: implement token usage
            $token = md5(uniqid());
            $_SESSION["_token"] = $token;
            echo "Success";
            header("Location: chat.php");
        }
    }

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
                    <li><a href="#">Learn More</a></li>
                    <li><a href="register.php">Join Now</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="#">Log In <span class="sr-only">(current)</span></a></li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</div>
<div class="container container-login">
    <div class="jumbotron login-jumbotron">
        <h3>Please fill out your details to log in</h3>
        <br/>
        <form class="form-horizontal" method="post" enctype="multipart/form-data" action="login.php">
            <div class="form-group">
                <label class="control-label col-sm-2" for="username">Username</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="username" id="username" placeholder="Enter username">
                    <p><?php echo $usernameError ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" for="pwd">Password</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter password">
                    <p><?php echo $passwordError ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" for="pwd">Security PIN</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="security-pin" id="security-pin" placeholder="Enter security PIN">
                    <p><?php echo $securityPINError ?></p>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" name="btn-login" id="btn-login" class="btn btn-success">Log In</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/autosize.js"></script>
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
