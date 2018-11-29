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

// Potential options
// add message to database
if($option == "sendmessage") {
    $tme = new DateTimeZone(DateTimeZone::AMERICA);

    $timeNow = date("Y-m-d H:i:s");
    $time = new DateTime($timeNow);
// to encrypt and send message
} else if($option == "reqkey") {
    if($_REQUEST["type"] == "public_key") {
        $reqUser = $_REQUEST["reqUser"];
        echo $userObj->getKey("public_key", $reqUser);
    }
} else if($option == "getmessages") {
    header('Content-type: application/json');
    $reqUser = $_REQUEST["reqUser"];
    $messages = array_values($userObj->getUserMessages($username, $reqUser));
   /* foreach($messages as $msg) {
        echo json_encode($msg);
    }*/
    echo json_encode($messages);
    //echo json_encode($messages);
    //echo json_encode($messages, true);
    //echo "hello";
} else if($option == "newchat") {

} else if($option == "countdowndel") {

}
// SEND MESSAGE
// GET USER CHATS (from left menu)
//


?>