const msgField = $("#msg-field");
const btnMsgSend = $(".btn-msg-send");

$(document).ready(function () {
    // applies auto-resizing functionality to message field
    autosize(document.querySelector("#msg-field"));
    btnMsgSend.on("click", function () {
        addMessage("self")
    });
    msgField.keypress(function (e) {
        let key = e.which;
        if (key === 13) {
            // do not move down when pressing enter
            e.preventDefault();
            addMessage("self");
        }
    });

});


/**
 *
 * @param party - Sender or recipient
 */
function addMessage(party) {
    // get text and trim whitespace
    let msgText = msgField.val().replace(/^[ ]+|[ ]+$/g, '');
    // check if not empty
    if (msgText) {
        // add the chat bubble to chat window
        addChatBubble(msgText, party);
        // parameter if executed by server or not, isAuto = true for server so we do not do this
        // get dropdown time entered
        const dropdownTime = getDropdownTime();
        // get session name - stored on hidden element of page
        const sessionName = $(".session-name").text();
        // encrypt with recipient's public key

        // search page for recipient getting from
        // implement
        const recipientUsername = $(".username-label").val();
        // retrieve key
        const key = getKey("public_key", "ntest100", "");
        alert(key);
        // send message off
        sendMessageDB("", dropdownTime);
        // generate link
        if ($("#generate-link-checkbox").checked === true) {
            // do stuff
        }

    }
}

function addChatBubble(msgText, party) {
    // add chat bubblle
    $('<div class="chat-bubble chat-' + party + '">' + msgText + '</div>').appendTo(".panel-chat-body");
    // clear input
    msgField.val("");
    // auto scroll down when new messages added
    $(".panel-chat-body").animate({scrollTop: $(document).height()}, 50);
}

/**
 * Gets the dropdown time in seconds to determine how long the user wishes to keep the message
 * @returns {number} - seconds
 */
function getDropdownTime() {
    const dropdownVal = $(".msg-timer-dropdown").val();
    if (dropdownVal == "10 seconds") {
        return 10;
    } else if (dropdownVal == "1 minute") {
        return 60
    } else if (dropdownVal == "1 hour") {
        return 3600;
    } else if (dropdownVal == "24 hours") {
        return 86400;
    } else if (dropdownVal == "Never") {
        return -1;
    }
    return -1;
}

function encryptMessage(key, text) {
    var rsa = new RSAKey();
//TODO: finish method
    return encryptedText;
}

/**
 *
 * @param messageText
 * @param expireTime
 */
function sendMessageDB(messageText, expireTime) {
    const time = "";
    $.ajax({
        method: "POST",
        url: "includes/request.php",
        data: {
            option: "sendmessage",
            messageContent: messageText,
            messageExpire: expireTime
        },
        async: false
    }).done(function (e) {

    });
}

function getUserMessages(user) {
    $.ajax({
        method: "GET",
        url: "includes/request.php",
        data: {
            option: "getmessages",
            reqUser: user,
        },
        dataType: "JSON",
        async: false
    }).done(function (data) {
        const jsonResponse = JSON.parse(data);
        alert(data);
        for(const r in jsonResponse) {
            //addChatBubble();
        }
    });
}

function newChat(recipientUsername) {
    //TODO: implement method
}

function loeadConversations() {

}

// keytype can = public_key or private_key
function getKey(keytype, user, authkey) {
    $.ajax({
        method: "POST",
        url: "includes/request.php",
        data: {
            option: "reqkey",
            type: keytype,
            reqUser: user,
            auth: authkey,
        },
        async: false
    }).success(function (success) {
        return success;
    }).error(function (err) {
        return err;
    });
}

// should run every 20-30 sec
function getLatestMessage() {
} //TODO: implement method

function isGenerateLink() {
//TODO: implement method

}