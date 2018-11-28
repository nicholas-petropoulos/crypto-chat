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

    function getUserMessages($username) {
        global $con;
        // messages ordered oldest to newest (top to bottom) and users grouped together
        $sql = $con->prepare("SELECT * FROM messages WHERE sender_username=? ORDER BY TIMESTAMP(message_date) DESC, recipient_username ASC");
        if ($sql) {
            $sql->bind_param("s", $username);
            $sql->execute();
            $result = $sql->get_result();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $messageText = $row["message_text"];
                    $timeExpire = $row["time_expire"];
                    $recipientUsername = $row["recipient_username"];
                    echo "Message text: ". $messageText . " - Expire: " . $timeExpire . " - Rec: " . $recipientUsername;
                    // add to array - possibly 2d array
                }
                // return array of messages
                $usersMessaged = $result["recipient_username"];
            }
        }

    }
    function setRecipient($recipient) {
        $this->recipient = $recipient;
    }
}