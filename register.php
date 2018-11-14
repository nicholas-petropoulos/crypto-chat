<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nicholas
 * Date: 11/6/2018
 * Time: 12:24 PM
 */

include "includes/config.php";
include "includes/functions.php";
// Registration variables
$username = $email = $password = $confirmPassword = $passwordEncrypt = "";

// Reg Error variables
$usernameError = $emailError = $passwordError = $confirmPasswordError = $passwordMatchError = "";

/**
 * Registration fields
 *  Username
 *  Email
 *  Password
 */
// Submit button pressed
if(isset($_POST["btn-submit"])) {
    // returns an error if unsuccessful, otherwise returns empty string
    // Username and email data, includes 2 index array
    $usernameData = trimAndQuery("username");
    $emailData = trimAndQuery( "email");
    // these are so the values can be stored after submitting with an error
    $username = $usernameData[0];
    $email = $emailData[0];
    // this is the error messages that are thrown, if not blank
    $usernameError = $usernameData[1];
    $emailError = $emailData[1];

    // password checks
    if(trim($_POST['password-confirm'] = "")) {
        $confirmPasswordError = "No confirmation password entered!";
    } else {
        $confirmPassword = trim($_POST["password-confirm"]);
    }
    if(trim($_POST['password']) == "")
        $passwordError = "No password entered!";
    else {
        $password = trim($_POST['password']);
    }
    // check if passwords match, if they do, hash password
    if($password != $confirmPassword) {
        $passwordMatchError = "Passwords do not match!";
    } else {
        $passwordEncrypt = crypt($password,"");
    }

    // If all fields above return no errors (strings are blank)
    if($usernameError == "" && $passwordError == "" && $confirmPasswordError == "" && $emailError == "" && $passwordMatchError == ""){

        // CHANGE TO PREPARED STATEMENT
        $sql = "insert into user(username, email, first_name, last_name, picture, password) values ('$username', '$email', '$firstname', '$lastname', '$imgContent', '$passwordEncrypt')";

        // if con true
        if($con->query($sql)){
            $registerDone = "New user added successfully";
            //$username = $email = $password = "";
        }
        else{
            echo "Error ".$con->error;
        }

    }

}
$title = "CryptoChat Registration";
//include "includes/header.php";
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
                    <li class="active"><a href="#">Join Now <span class="sr-only">(current)</span></a></li>

                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="menu-btn-login"><a href="login.html">Log In</a></li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</div>
<div class="container container-register">
    <div class="jumbotron register-jumbotron">
        <h3>Please fill out your details to join</h3>
        <br/>
        <form class="form-horizontal" id="register" method="post" enctype="multipart/form-data" action="register.php" onsubmit="validateRegForm()">
            <div class="form-group">
                <label class="control-label col-sm-2" for="username">Username</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="username" id="username" value="<?php echo $username; ?>" placeholder="<?php if($usernameError==""){ echo "Enter username"; } else { echo $usernameError; }?> ">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" for="email">Email</label>
                <div class="col-sm-9">
                    <input type="email" class="form-control" name="email" id="email" value="<?php echo $email; ?>" placeholder="<?php if($emailError==""){ echo "Enter email"; } else { echo $emailError; } ?> ">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" for="password">Password</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control" name="password" id="password" placeholder="<?php if($passwordError==""){ echo "Enter password"; } else { echo $passwordError; } ?> ">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" for="password-confirm">Confirm password</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control" name="password-confirm" id="password-confirm" placeholder="<?php if($confirmPasswordError==""){ echo "Enter password again"; } else { echo $confirmPasswordError; } ?> ">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <p><?php if(!$passwordMatchError==""){ echo $passwordMatchError; } ?></p>
                    <button type="submit" name="btn-submit" id="btn-submit" class="btn btn-success">Register</button>
                </div>
            </div>
        </form>
        </ul>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/register.js"></script>
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
