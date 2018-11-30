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
    const messageCheck = setInterval(function () {
        getUserMessages("npet")
    }, 10000);

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
        const dropdownTimeSeconds = getMessageExpireTime();
        // add the chat bubble to chat window, -1 because the countdown should only begin with the recipient gets message
        if(dropdownTimeSeconds === -1) {
            addChatBubble(msgText, party, getTime(), -1);
        } else {

        }

        // parameter if executed by server or not, isAuto = true for server so we do not do this
        // get dropdown time entered
        // get session name - stored on hidden element of page
        const sessionName = $(".session-name").text();
        // encrypt with recipient's public key

        // search page for recipient getting from
        // implement
        const recipientUsername = $(".username-label").val();
        // retrieve key
        const encryptedMessage = getKeyAndEncrypt("public_key", recipientUsername, "");

        // send message off
        sendMessageDB(encryptedMessage, dropdownTimeSeconds);
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
    $('<span id="msg-expire-' + msgCounter + '"></span>').appendTo("#msg-detail-" + msgCounter);
    $('<div id="chat-bubble-' + msgCounter + '" class="chat-bubble chat-' + party + '">' + msgText + '</div>').appendTo(".panel-chat-body");
    // clear input
    msgField.val("");
    // auto scroll down when new messages added
    $(".panel-chat-body").animate({scrollTop: $(document).height()}, 50);
    // increment coutner
    if(msgTimeExpire > 0) {
        const msgExpireElement = $("#msg-expire-" + msgCounter);
        const msgDateElement = $("#msg-date-"+ msgCounter);
        const chatBubble = $("#chat-bubble-" + msgCounter);
        // this begins countdown and deletes elements
        initLocalCountdownDelete(msgTimeExpire, msgExpireElement, msgDateElement, chatBubble);
    // message does not expire
    } else if(msgTimeExpire === 0) {

    // message is waiting on recipient to open  before countdown starts
    } else if(msgTimeExpire === -1) {

    }
    msgCounter++;
}


function initLocalCountdownDelete(duration, mExpireElement, mDateElement, cBubble) {
    let timer = duration, minutes, seconds;
    const interval = setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        // formatting for two zeros
        if (minutes < 10) {
            minutes = "0" + minutes;
        }
        if (seconds < 10) {
            console.log(seconds);
            seconds = "0" + seconds;
        }

        mExpireElement.text(" - Time remaining: " + minutes + ":" + seconds);
        console.log(timer);
        if (timer <= 0) {
            //mExpireElement.remove();
            //mDateElement.remove();
            cBubble.text("[MESSAGE DELETED]");
            clearInterval(interval);
        }
        timer--;
    }, 1000);
}

/**
 * Gets the dropdown time in seconds to determine how long the user wishes to keep the message
 * @returns {number} - seconds
 */
function getMessageExpireTime() {
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

/**
 * Returns user's local time to display on messages
 * @returns {*|string}
 */
function getTime() {
    // get UTC
    const timeUTC = moment.utc().format();
    // convert to time
    const timeLocal = moment.utc(timeUTC).local().format();
    // format time
    return moment(timeLocal).local().format('YYYY-MM-DD HH:mm:ss');
}

/**
 * Encrypts messages
 * @param key
 * @param text
 * @returns encrypted string
 */
function encryptMessage(key, text) {
    const rsa = new RSAKey();
    rsa.setPublic(key);
    return rsa.encrypt(text);
}

/**
 *
 * @param messageText
 * @param date
 */
function sendMessageDB(messageText, date, doesExpire) {
    var timeNow = Date.now();
    // TODO: account for different timezones
    //const timeExpire = new Date(timeNow.getTime()+diff);
    $.ajax({
        method: "POST",
        url: "includes/request.php",
        data: {
            option: "sendmessage",
            messageContent: messageText,
            expire: doesExpire,
            expireSeconds: date, //SECONDS
            recipientSeen: false
        },
        async: false
    }).done(function (e) {

    });
}

/**
 * Sends request to DB once user reads message
 */
function sendReadMessageFieldDB() {

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
function getKeyAndEncrypt(keytype, user, authkey) {
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
        var response = success.responseText;
    });
}


// should run every 20-30 sec
function getLatestMessage() {
} //TODO: implement method - checks on interval

function isGenerateLink() {
//TODO: implement method

}