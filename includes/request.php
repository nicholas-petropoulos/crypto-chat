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

$username = $_SESSION["username"];
// POST option
$option = trim($_POST["option"]);

// Potential options
// add message to database
if($option == "sendmessage") {

// to encrypt and send message
} else if($option == "reqkey") {

} else if($option == "getchat") {

} else if($option == "newchat") {

}
// SEND MESSAGE
// GET USER CHATS (from left menu)
//


?>