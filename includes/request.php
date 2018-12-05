<?php
/**
 * Created by IntelliJ IDEA.
 * message: Nicholas
 * Date: 11/23/2018
 * Time: 3:39 PM
 */

session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

include "config.php";
include "message.php";

if(isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
}

// POST option
$option = trim($_REQUEST["option"]);
// message object
$msgObj = new message();

// TODO: IMPLEMENT AUTHKEY
//$authKey = $_REQUEST["authKey"];

// Potential options
// add message to database
// TODO: Sanitize Input more
if($username != "") {
    if ($option == "sendmessage") {
        $messageText = $_REQUEST["messageText"];
        // 1 = true, 0 = false
        $expires = $_REQUEST["expires"];
        // seconds message is alive
        $timeExpire = $_REQUEST["timeExpire"];
        // message to send message
        $reqUser = "";
        // reqUser does not need to be set 
        if(isset($_REQUEST["recipient"])) {
            $reqUser = $_REQUEST["recipient"];
        }
        if ($expires == "true") {
            $expires = 1;
        } else {
            $expires = 0;
        }
        if($msgObj->addMessage($timeExpire, $username, $reqUser, $messageText, $expires)) {
            echo "Added message";
        } else {
            echo "Error adding message";
        }

// to encrypt and send message
    } else if ($option == "getkey") {
        if ($_REQUEST["type"] == "public_key") {
            $reqUser = $_REQUEST["reqUser"];
            echo $msgObj->getKey("public_key", $reqUser);
            // Only can request own private key
        } else if (($_REQUEST["type"] == "private_key") && $_REQUEST["reqUser"] == $username) {
            $pin = trim($_REQUEST["pin"]);
            $reqUser = $_REQUEST["reqUser"];
            echo $msgObj->getDecryptedPrivateKey($pin, $reqUser);
        }
// need to send update that message has been read
    } else if ($option == "getmessages") {
        header('Content-type: application/json');
        // requested message messages
        $reqUser = $_REQUEST["reqUser"];
        $messages = $msgObj->getUserMessages($username, $reqUser);
        if ($messages != null && $messages != "") {
            $msgArray = array_values($messages);
            echo json_encode($msgArray);
        } else {
            echo "No messages returned";
        }

    } else if ($option == "updatemessage") {
        // check if message session matches request message
        $msgID = $_REQUEST["msg_ID"];
        $msgRead = $_REQUEST["msg_read"];
        if ($username == $_REQUEST["username"]) {
            echo "Username match";
            $msgObj->updateMessage($msgID, $msgRead);
        } else {
            echo "Username no match";
        }
    }
// return last message id for using link generation
}
// SEND MESSAGE
// GET USER CHATS (from left menu)
//


?>