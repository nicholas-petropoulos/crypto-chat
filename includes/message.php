<?php
/**
 * Created by IntelliJ IDEA.
 * message: Nicholas
 * Date: 11/23/2018
 * Time: 5:13 PM
 */
include "config.php";

class message {
    // $type = public_key or private_key
    /**
     * Get a user's public or private key
     * @param $type
     * @param $username
     * @return string
     */
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

    /**
     * Get user's private key and decrypt using pin
     * @param $pin
     * @param $username
     * @return mixed
     */
    function getDecryptedPrivateKey($pin, $username) {
        global $openSSLConfig;
        $encryptedKey = $this->getKey("private_key", $username);
        $pkeyTest = openssl_pkey_get_private($encryptedKey, $pin);
        if($pkeyTest == false){
            echo "Unable to retrieve key";
        }
        // get unencrypted
        openssl_pkey_export($pkeyTest,$decryptedKey,null, $openSSLConfig);
        return $decryptedKey;
    }

    // array format - $arr = [[$msg],[$msg],[$msg],[$msg]...]
    // return array [$recipientUsername, $messageText, $messageDate, $timeExpire]
    /**
     * Reuturn all messages to and from the user
     * @param $username
     * @param $recUsername
     * @return array|string
     */
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
                        // add a timestamp
                        $timestamp = $this->convertReadDateToTimestamp($messageReadDate);
                        $msgDeleted = $this->checkAndDeleteMessage($timeExpire,$messageID, $timestamp);
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
     * Returns the last message from a specified user
     * used in link generation
     * @param $username
     * @return string|null|string
     */
    function getLastMessageID($username) {
        global $con;
        $sql = $con->prepare("SELECT message_id FROM messages WHERE sender_username=? ORDER BY message_id DESC LIMIT 1");
        if ($sql) {
            $sql->bind_param("s", $username);
            $sql->execute();
            $result = $sql->get_result();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    return $row["message_id"];
                }
            }
            $sql->close();
            return "";
        }
        return "";
    }

    /**
     * Returns expiration details for link generation
     * @param $messageID
     * @return array|string
     */
    function getMessageExpireDetailsByID($messageID) {
        global $con;
        $sql = $con->prepare("SELECT * FROM messages WHERE message_id=?");
        if ($sql) {
            $sql->bind_param("s", $messageID);
            $sql->execute();
            $result = $sql->get_result();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    return [$row["message_read_date"], $row["time_expire"], $row["expires"], $row["message_read"]];
                }
            }
            $sql->close();
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
     * @return bool
     */
    function addMessage($timeExpire, $senderUsername, $recipientUsername, $messageText, $expires) {
        global $con;
        if($sql = $con->prepare("INSERT INTO messages (time_expire, sender_username, recipient_username, message_text, expires) VALUES(?, ?, ?, ?, ?)")) {
            // if statement only if parameters bind succesful
            if ($sql->bind_param("sssss", $timeExpire, $senderUsername, $recipientUsername, $messageText, $expires)) {
                $sql->execute();
                $sql->close();
                return true;
            } else {
                echo "Unable to process message";
                return false;
            }
        }
        return false;
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

    /**
     * Add generated link and unecrypted message into DB
     * @param $messageID
     * @param $messageText
     * @param $linkLocation
     * @return bool
     */
    function addGeneratedLink($messageID, $linkLocation, $messageText) {
        global $con;
        if($sql = $con->prepare("INSERT INTO generated_links (message_id, link_location, message_text) VALUES(?, ?, ?)")) {
            if($sql->bind_param("sss", $messageID, $linkLocation, $messageText)) {
                $sql->execute();
                $sql->close();
                return true;
            } else {
                echo "Unable to add generated link";
                return false;
            }
        } else {
            echo "Unable to generate link";
            return false;
        }
    }

    /**
     * @param $generatedLinkID
     * @return bool
     */
    function getGeneratedLinkMessage($generatedLinkID) {
        global $con;
        $messageID = "";
        if($sql = $con->prepare("SELECT * FROM generated_links WHERE link_location=?")) {
            if($sql->bind_param("s", $generatedLinkID)) {
                $sql->execute();
                $result = $sql->get_result();
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        return $row["message_text"];
                    }
                    return false;
                }
                $sql->close();
                return false;
            } else {
                echo "Unable to get generated link";
                return false;
            }
        } else {
            echo "Unable to get link";
            return false;
        }
    }

    /**
     * Get message ID from generated links
     * @param $generatedLinkLocation
     * @return string
     */
    function getGeneratedLinkMessageID($generatedLinkLocation) {
        global $con;
        if($sql = $con->prepare("SELECT * FROM generated_links WHERE link_location=?")) {
            if($sql->bind_param("s", $generatedLinkLocation)) {
                $sql->execute();
                $result = $sql->get_result();
                if($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        return $row["message_id"];
                    }
                }
                $sql->close();
            } else {
                echo "Unable to add generated link";
                return "";
            }
        } else {
            echo "Unable to generate link";
            return "";
        }
        return "";
    }

    /**
     * Adds a read timestamp and returns value
     * @param $con
     * @param $messageReadDate
     * @param $timeExpire
     * @param $messageID
     * @return bool
     */
    public function convertReadDateToTimestamp($messageReadDate) {
        global $con;
        if($messageReadDate != NULL) {
            $sqlTime = $con->query("SELECT UNIX_TIMESTAMP('" . $messageReadDate . "');") or die($con->error);
            $timestampArr = $sqlTime->fetch_array();
            $timestamp = $timestampArr[0];
            //$msgDeleted = $this->checkIfMessageShouldDelete($timeExpire, $messageID, $timestamp);
            return $timestamp;
        }
        return "";
    }

    /**
     * Checks if a message has expired and proceeds to delete it
     * @param $timeExpire
     * @param $messageID
     * @param $timestamp
     * @return bool
     */
    public function checkAndDeleteMessage($timeExpire, $messageID, $timestamp)
    {
        $msgDeleted = false;
        if (time() >= $timestamp + $timeExpire) {
            $this->deleteMessage($messageID);
            $msgDeleted = true;
        }
        return $msgDeleted;
    }


}