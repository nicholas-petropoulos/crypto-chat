<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nicholas
 * Date: 11/27/2018
 * Time: 11:11 AM
 */

session_start();

include "includes/config.php";
include "includes/message.php";

$username = $option = $messageID = $message = "";
// message does not have to be logged in ot view page
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
}
//
if (isset($_REQUEST["option"])) {
    $option = $_REQUEST["option"];
} else {
    echo "Option not set";
}

$uniqueID = 0;
$isViewingMsg = false;
$msgObj = new message();

// only can generate link if logged in
if ($option == "linkgen" && isset($_SESSION["username"])) {
    $messageText = $_REQUEST["msg_text"];
    // put message details on page
    $uniqueID = uniqid("msg");
    // associate with msg id
    $msgID = $msgObj->getLastMessageID($username);
    // add new link into DB
    if ($msgID != "") {
        if ($msgObj->addGeneratedLink($msgID, $uniqueID, $messageText)) {
            // send link to share back
            echo "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . '?option=viewlink&id=' . $uniqueID;
        } else {
            echo "Error adding message";
        }
    } else {
        echo "Could not get message ID";
    }

// dont have to be logged in to view link
} else if ($option == "viewlink") {
    // if the request string matches
    if (isset($_REQUEST["id"])) {
        $genLinkID = $_REQUEST["id"];
        $isViewingMsg = true;
        // message ID to look up to see if expired
        $messageID = $msgObj->getGeneratedLinkMessageID($genLinkID);
        // get details returned in array
        $messageDetails = $msgObj->getMessageExpireDetailsByID($messageID);
        //var_dump($messageDetails);
        // convert message read date to timestamp, returns empty string if not read
        $messageReadTimestamp = $msgObj->convertReadDateToTimestamp($messageDetails[0]);
        // get time expire in seconds
        $messageTimeExpire = $messageDetails[1];
        // check message if delete, if deleted do not display page
        // message not read
        if (($messageReadTimestamp == "") || ($messageReadTimestamp < $messageTimeExpire)) {
            // get message to display
            if ($message = $msgObj->getGeneratedLinkMessage($genLinkID)) {
                // Start countdown by updating message
                // triggers so $messageReadTimestamp == timestamp NOT ""
                $msgObj->updateMessage($messageID, 1);
                ?>

                <!DOCTYPE html>
                <html>

                <head>
                    <meta charset="utf-8"/>
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <title>CryptoChat</title>
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <link href="css/style.css" rel="stylesheet"/>
                    <link href="css/bootstrap.css" rel="stylesheet"/>
                    <link href="css/bootstrap-theme.css" rel="stylesheet"/>
                </head>

                <body>
                <div class="container container-top">
                    <nav class="navbar navbar-inverse">
                        <div class="container-fluid">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                        data-target="#bs-example-navbar-collapse-1"
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
                                    <?php if (isset($username)) {
                                        echo '<li id="logged-in-user"><a href="#">Welcome ' . $username . '</a></li>';
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
                        echo '<p>Expires in: '.$messageTimeExpire. ' seconds</p>';
                        echo '<p>'.$message.'</p>';
                        ?>
                    </div>
                </div>


                <script src="https://code.jquery.com/jquery-3.3.1.min.js"
                        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
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

                <?php
            }
        } else {
            // delete message
            echo "Message deleted";
            $msgObj->checkAndDeleteMessage($messageTimeExpire, $messageID, $messageReadTimestamp);
        }

    }

}
?>