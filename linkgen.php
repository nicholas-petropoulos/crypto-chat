<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nicholas
 * Date: 11/27/2018
 * Time: 11:11 AM
 */

session_start();

include "includes/config.php";

$username = $_SESSION["username"];
$option = trim($_POST["option"]);
$messageID = 0;

if($option == "linkgen") {
    // put message details on page
}


// SHOW PAGE HERE WITH HEADER + USERNAME SENT MESSAGE
// MESSAGE BELOW