<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nicholas
 * Date: 11/23/2018
 * Time: 3:39 PM
 */

session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

include "config.php";
include "User.php";

$username = $_SESSION["username"];
// POST option
$option = trim($_REQUEST["option"]);
// user object
$userObj = new User();

// Potential options
// add message to database
if($option == "sendmessage") {

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

}
// SEND MESSAGE
// GET USER CHATS (from left menu)
//


?>