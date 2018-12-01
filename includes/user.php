<?php
/**
 * Created by IntelliJ IDEA.
 * user: Nicholas
 * Date: 11/23/2018
 * Time: 5:13 PM
 */
include "config.php";

class user {
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
        //SELECT * FROM messages WHERE sender_username='ntest101' OR recipient_username='ntest101' ORDER BY message_id ASC, recipient_username ASC
        // messages ordered oldest to newest (top to bottom) and users grouped together
        $sql = $con->prepare("SELECT * FROM messages WHERE (sender_username=? AND recipient_username=?) OR (sender_username=? AND recipient_username=?) ORDER BY message_id ASC, recipient_username ASC");
        if ($sql) {
            $sql->bind_param("ssss", $username,$recUsername, $recUsername, $username);
            $sql->execute();
            $result = $sql->get_result();
            $messages = [];
            if ($result->num_rows > 0) {
                // MESSAGE READ FIELD = NULL
                // IF NULL && MSG EXPIRES, THEN GET TIMESTAMP AND STORE
                // if MESSAGE GETTING IS EXPIRED, DELETE
                while($row = $result->fetch_assoc()) {
                    $messageID = $row["message_id"];
                    $messageText = $row["message_text"];
                    // TODO: DECRYPT
                    $messageDate = $row["message_date"];
                    // does the message expire or no
                    $expires = $row["expires"];
                    // if the message expires, how long does it last until deleting after opened
                    $timeExpire = $row["time_expire"];
                    // has the message been read?
                    $isMessageRead = $row["message_read"];
                    $messageReadDate = $row["message_read_date"];
                    // TODO: Delete message  if $messageReadDate >= $currDate then delete
                    //$timestamp = strtotime();
                    $recipientUsername = $row["recipient_username"];
                    // if a recipient username is passed through, filter and return only those messages OR no filter added
                    //if(($recipientUsername != "" && ($recUsername == $recipientUsername)) || $recipientUsername == "") {
                    $messages[] = ["msg_id" => $messageID, "recipient_user" => $recipientUsername, "msg_text" => $messageText,
                        "msg_sent_date" => $messageDate, "expires" => $expires, "time_expire" => $timeExpire, "is_msg_read" => $isMessageRead,
                        "msg_read_date" => $messageReadDate];
                    // return all messages if no filter added
                    //} /*else if($recipientUsername == "") {
                     //   $messages[] = ["rec_user" => $recipientUsername, "msg_text" => $messageText, "msg_date" => $messageDate, "time_expire" => $timeExpire];
                   // }*/

                }
            }
            return $messages;
        }
        return "";
    }

    /**
     * @param $timeExpire
     * @param $senderUsername
     * @param $recipientUsername
     * @param $messageText
     * @param $expires
     */
    function addMessage($timeExpire, $senderUsername, $recipientUsername, $messageText, $expires) {
        global $con;
        if($sql = $con->prepare("INSERT INTO messages (time_expire, sender_username, recipient_username, message_text, expires) VALUES(?, ?, ?, ?, ?)")) {
            // if statement only if parameters bind succesful
            if ($sql->bind_param("sssss", $timeExpire, $senderUsername, $recipientUsername, $messageText, $expires)) {
                $sql->execute();
                $sql->close();
            } else {
                echo "Unable to process message";
            }
        }
    }

    /**
     * @param $messageID: ID of messge to update status in DB
     * @param $messageRead: flag if the message has been read by the other party
     * automatically updates message_read_date in DB as current timestamp when updated in SQL
     */
    function updateMessage($messageID, $messageRead) {
        global $con;
        if($sql = $con->prepare("UPDATE messages SET message_read = ?, message_read_date = CURRENT_TIMESTAMP() WHERE message_id = ?")) {
            if($sql->bind_param("ss",$messageRead, $messageID)) {
                $sql->execute();
                $sql->close();
            } else {
                echo "Unable to update message";
            }
        }
    }

    function deleteMessage($messageID) {

    }

    function setRecipient($recipient) {
        $this->recipient = $recipient;
    }

    function createToken() {}
}