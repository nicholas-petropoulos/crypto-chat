<?php
/**
 * Created by IntelliJ IDEA.
 * user: Nicholas
 * Date: 11/23/2018
 * Time: 3:39 PM
 */

session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

include "config.php";
include "user.php";

$username = $_SESSION["username"];
// POST option
$option = trim($_REQUEST["option"]);
// user object
$userObj = new user();

// TODO: IMPLEMENT AUTHKEY??
//$authKey = $_REQUEST["authKey"];

// Potential options
// add message to database
// TODO: Sanitize Input more
if($option == "sendmessage") {
    $messageText = $_REQUEST["messageText"];
    // 1 = true, 0 = false
    $expires = $_REQUEST["expires"];
    // seconds message is alive
    $timeExpire = $_REQUEST["timeExpire"];
    // user to send message
    $reqUser = $_REQUEST["reqUser"];
    if($expires == "true") {
        $expires = 1;
    } else {
        $expires = 0;
    }
    $userObj->addMessage($timeExpire, $username, $reqUser, $messageText, $expires);

// to encrypt and send message
} else if($option == "reqkey") {
    if($_REQUEST["type"] == "public_key") {
        $reqUser = $_REQUEST["reqUser"];
        echo $userObj->getKey("public_key", $reqUser);
    }
// need to send update that message has been read
} else if($option == "getmessages") {
    header('Content-type: application/json');
    // requested user messages
    $reqUser = $_REQUEST["reqUser"];
    $messages = array_values($userObj->getUserMessages($username, $reqUser));
    echo json_encode($messages);
} else if($option == "newchat") {

} else if($option == "countdowndel") {

}
// SEND MESSAGE
// GET USER CHATS (from left menu)
//


?>