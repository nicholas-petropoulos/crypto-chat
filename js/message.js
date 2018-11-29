const msgField = $("#msg-field");
const btnMsgSend = $(".btn-msg-send");
const dropdownVal = $(".msg-timer-dropdown").val();
// used to differentiate timer elements created
var msgCounter = 0;


$(document).ready(function () {
    //reset msg counter
    msgCounter = 0;
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
        // dropdown converted to seconds for easier manipulation
        const dropdownTimeSeconds = getDropdownTime();
        // add the chat bubble to chat window
        addChatBubble(msgText, party, "Now", dropdownTimeSeconds);
        // parameter if executed by server or not, isAuto = true for server so we do not do this
        // get dropdown time entered
        // get session name - stored on hidden element of page
        const sessionName = $(".session-name").text();
        // encrypt with recipient's public key

        // search page for recipient getting from
        // implement
        const recipientUsername = $(".username-label").val();
        // retrieve key
        const key = getKey("public_key", recipientUsername, "").responseText;

        // send message off
        sendMessageDB(msgText, dropdownTimeSeconds);
        // generate link
        if ($("#generate-link-checkbox").checked === true) {
            // do stuff
        }

    }
}

function addChatBubble(msgText, party, msgDate, msgTimeExpire) {
    // info element that holds  date and time to expire
    $('<div class="msg-detail" id="msg-detail-' + msgCounter + '"></div>').appendTo(".panel-chat-body");
    // date element appended to info element
    $('<span id="msg-date-' + msgCounter + '">' + msgDate + '</span>').appendTo("#msg-detail-" + msgCounter);
    // expire element appented to info element
    $('<span id="msg-expire-' + msgCounter + '">' + ' - Time remaining: ' +/*msgTimeExpire + */'</span>').appendTo("#msg-detail-" + msgCounter);
    $('<div id="chat-bubble-' + msgCounter + '" class="chat-bubble chat-' + party + '">' + msgText + '</div>').appendTo(".panel-chat-body");
    // clear input
    msgField.val("");
    // auto scroll down when new messages added
    $(".panel-chat-body").animate({scrollTop: $(document).height()}, 50);
    // increment coutner
    if(msgTimeExpire !== -1) {
        const msgExpireElement = $("#msg-expire-" + msgCounter);
        const msgDateElement = $("#msg-date-"+ msgCounter);
        const chatBubble = $("#chat-bubble-" + msgCounter);
        // this begins countdown and deletes elements
        initLocalCountdownDelete(msgTimeExpire, msgExpireElement, msgDateElement, chatBubble);
    }
    msgCounter++;
}


function initLocalCountdownDelete(duration, mExpireElement, mDateElement, cBubble) {
    var timer = duration, minutes, seconds;
    var interval = setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        if(minutes < 10) {
            minutes = "0" + minutes;
        } else if(seconds < 10) {
            seconds = "0" + seconds;
        }

        //minutes = minutes < 10 ? "0" + minutes : minutes;
        //seconds = seconds < 10 ? "0" + seconds : seconds;

        mExpireElement.text(" - Time remaining: " + minutes + ":" + seconds);
/*
        if (--timer < 0) {
            timer = duration;
        }*/
        console.log(timer);
        if(timer <= 0) {
            mExpireElement.remove();
            mDateElement.remove();
            cBubble.remove();
            clearInterval(interval);
        }
        timer--;
        //console.log(timerElement.val());
    }, 1000);
}

/**
 * Gets the dropdown time in seconds to determine how long the user wishes to keep the message
 * @returns {number} - seconds
 */
function getDropdownTime() {
    if (dropdownVal === "10 seconds") {
        return 10;
    } else if (dropdownVal === "1 minute") {
        return 60
    } else if (dropdownVal === "1 hour") {
        return 3600;
    } else if (dropdownVal === "24 hours") {
        return 86400;
    } else if (dropdownVal === "Never") {
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
 * @param date
 */
function sendMessageDB(messageText, date) {
    var timeNow = Date.now();
    const timeExpire = new Date(timenow.getTime()+diff)
    $.ajax({
        method: "POST",
        url: "includes/request.php",
        data: {
            option: "sendmessage",
            messageContent: messageText,
            expireDate: date //SECONDS
        },
        async: false
    }).done(function (e) {

    });
}

/**
 * Gets all available messages for current open message window
 * @param user
 */
function getUserMessages(user) {
    $.ajax({
        method: "GET",
        url: "includes/request.php",
        data: {
            option: "getmessages",
            reqUser: user,
        },
        async: false
    }).done(function (data) {
        for(var i=0; i < data.length; i++) {
            var msgText = data[i].msg_text;
            var party = data[i].recipient_user;
            var messageDate = data[i].msg_date;
            var messageExpire = data[i].time_expire;
            // to determine if it is send or received messages
            if(party !== user) {
                addChatBubble(msgText, "sender",messageDate, messageExpire);
            } else {
                addChatBubble(msgText, "self", messageDate, messageExpire);
            }
        }

    });
}

function newChat(recipientUsername) {
    //TODO: implement method
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
    }).done(function (success) {
        return success;
    });
}


// should run every 20-30 sec
function getLatestMessage() {
} //TODO: implement method - checks on interval

function isGenerateLink() {
//TODO: implement method

}