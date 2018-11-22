<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nicholas
 * Date: 11/13/2018
 * Time: 2:18 PM
 */
include "config.php";

// returns an array of $userInput and an error value (if not blank
// arr[0] = $userInput, arr[1] = $error
function trimAndQuery($fieldNameParam) {
    // if field left blank
    if(trim($_POST[$fieldNameParam]) == "") {
        // return error
        return ["", "No " . $fieldNameParam. " entered!"];
    } else {
        // converts string to lowercase
        $userInput = strtolower(trim($_POST[$fieldNameParam]));
        // using $con variable from config.php as global so we do not need a parameter
        global $con;
        // prepare the SQL statement to prevent SQL injection
        $preparedSQL = $con->prepare("SELECT * FROM users WHERE $fieldNameParam=?");
        // if returns true
        if($preparedSQL) {
            // s = type string, $fieldNameParam is the name of the field (ie. username)
            // $fieldName is the user's input
            $preparedSQL->bind_param("s", $userInput);
            // execute query
            $preparedSQL->execute();
            // store result
            $result = $preparedSQL->get_result();
            //check if username exists
            if ($result->num_rows > 0){
                return [$userInput, $userInput . " exists"];
            }
        } else {
            return [$userInput, "SQL error"];
        }
        $preparedSQL->close();
    }
    // return user input and empty string if no error
    return [$userInput, ""];
}

?>