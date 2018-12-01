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
    function getUserMessages($username, $recUsername)
    {
        global $con;
        //SELECT * FROM messages WHERE sender_username='ntest101' OR recipient_username='ntest101' ORDER BY message_id ASC, recipient_username ASC
        // messages ordered oldest to newest (top to bottom) and users grouped together
        $sql = $con->prepare("SELECT * FROM messages WHERE (sender_username=? AND recipient_username=?) OR (sender_username=? AND recipient_username=?) ORDER BY message_id ASC, recipient_username ASC");
        if ($sql) {
            $sql->bind_param("ssss", $username, $recUsername, $recUsername, $username);
            $sql->execute();
            $result = $sql->get_result();
            $messages = [];
            $msgDeleted = false;
            if ($result->num_rows > 0) {
                // MESSAGE READ FIELD = NULL
                // IF NULL && MSG EXPIRES, THEN GET TIMESTAMP AND STORE
                // if MESSAGE GETTING IS EXPIRED, DELETE
                while ($row = $result->fetch_assoc()) {
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
                    $recipientUsername = $row["recipient_username"];
                    // check if message should be deleted
                    if (($messageReadDate != null) && $isMessageRead == 1) {
                        // convert time to UNIX TIMESTAMP using mysql
                        $sqlTime = $con->query("SELECT UNIX_TIMESTAMP('" . $messageReadDate . "');") or die($con->error);
                        //die($con->error);
                        //$resultTime = reset($sqlTime->fetch_assoc());
                        //$str = "UNIX_TIMESTAMP(' " . $messageReadDate ." ')";
                        $timestampArr = $sqlTime->fetch_array();
                        $timestamp = $timestampArr[0];
                        //echo "Time: " .time();
                        //echo "<br>";
                        //echo "DB time: " .$timestamp;
                        //time in seconds > message read time + expire time
                        if(time() >= $timestamp+$timeExpire) {
                            $this->deleteMessage($messageID);
                            $msgDeleted = true;
                        }
                    }
                    if(!$msgDeleted) {
                        $messages[] = ["msg_id" => $messageID, "recipient_user" => $recipientUsername, "msg_text" => $messageText,
                            "msg_sent_date" => $messageDate, "expires" => $expires, "time_expire" => $timeExpire, "is_msg_read" => $isMessageRead,
                            "msg_read_date" => $messageReadDate];
                    } else {
                        // reset flag
                        $msgDeleted = false;
                    }
                }
                return $messages;
            }
            return "";
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
        } else {
            echo "Unable to update message";
        }
    }

    /**
     * Delete message from DB once expired
     * @param $messageID
     */
    function deleteMessage($messageID) {
        global $con;
        if($sql = $con->prepare("DELETE FROM messages WHERE message_id = ?")) {
            if($sql->bind_param("s", $messageID)) {
                $sql->execute();
                $sql->close();
            } else {
                echo "Unable to delete message";
            }
        } else {
            echo "Unable to delete message";
        }
    }

}