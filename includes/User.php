<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nicholas
 * Date: 11/23/2018
 * Time: 5:13 PM
 */
include "config.php";

class User {
    var $sender;
    var $recipient;
    var $recipientPubKey;
    var $senderPrivKey;

    // $type = public_key or private_key
    function getKey($type, $username) {
        global $con;
        $sql = $con->prepare("SELECT $type FROM users WHERE username=?");
        if ($sql) {
            $sql->bind_param("s", $username);
            // execute query
            $sql->execute();
            // store result
            $result = $sql->get_result();
            $val = $result->fetch_assoc();
            if ($val[$type] != "") {
                return $val[$type];
            }
            return "";
        }
        return "";
    }
    // array format - $arr = [[$msg],[$msg],[$msg],[$msg]...]
    // return array [$recipientUsername, $messageText, $messageDate, $timeExpire]
    function getUserMessages($username, $recUsername) {
        global $con;
        // messages ordered oldest to newest (top to bottom) and users grouped together
        $sql = $con->prepare("SELECT * FROM messages WHERE sender_username=? ORDER BY TIMESTAMP(message_date) DESC, recipient_username ASC");
        if ($sql) {
            $sql->bind_param("s", $username);
            $sql->execute();
            $result = $sql->get_result();
            $messages = [];
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $messageText = $row["message_text"];
                    // TODO: DECRYPT
                    $messageDate = $row["message_date"];
                    $timeExpire = $row["time_expire"];
                    $recipientUsername = $row["recipient_username"];
                    // if a recipient username is passed through, filter and return only those messages
                    if($recipientUsername != "" && ($recUsername == $recipientUsername)) {
                        $messages[] = [$recipientUsername, $messageText, $messageDate, $timeExpire];
                    // return all messages if no filter added
                    } else if($recipientUsername == "") {
                        $messages[] = [$recipientUsername, $messageText, $messageDate, $timeExpire];
                    }

                }

                    //echo "Message text: ". $messageText . " - Expire: " . $timeExpire . " - Rec: " . $recipientUsername;
                    // add to array - possibly 2d array
            }
            return $messages;
        }

    }
    function setRecipient($recipient) {
        $this->recipient = $recipient;
    }
}