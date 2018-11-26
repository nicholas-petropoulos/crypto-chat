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
    function getKey($type, $user) {
        global $con;
        $sql = $con->prepare("SELECT $type FROM user_keys WHERE username=?");
        if ($sql) {
            $sql->bind_param("s", $user);
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

    function setRecipient($recipient) {
        $this->recipient = $recipient;
    }
}