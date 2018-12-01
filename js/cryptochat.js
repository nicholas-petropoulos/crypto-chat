const msgField = $("#msg-field");
const btnMsgSend = $(".btn-msg-send");
const dropdownExpireTime = $(".msg-timer-dropdown");
const usernameLabel = $("#username-label");
// used to differentiate timer elements created
var msgCounter = 0;
var largestMsgID = 0;

$(document).ready(function () {
    // message updating should pause when sending a message to prevent lag
    //var isMessageCheckPaused = false;
    // get messages
    getUserMessages(usernameLabel.text());
   // getUserMessages($("#username-label").text()).then(() => {
        setInterval(function () {
            getUserMessages(usernameLabel.text());
        }, 10000);
 //   });
    //reset msg counter
    msgCounter = 0;
    // applies auto-resizing functionality to message field
    autosize(document.querySelector("#msg-field"));
    btnMsgSend.on("click", function () {
        //addMessage("self")
    });
    msgField.keypress(function (e) {
        let key = e.which;
        if (key === 13) {
            // do not move down when pressing enter
            e.preventDefault();
            addMessage("self");
        }
    });
    $("#new-chat").on("click", function() {
       displayUsernameInput();
    });
    $("#username-input-area").keypress(function (e) {
        let key = e.which;
        if (key === 13) {
            const usernameInputField = $("#username-field");
            // do not move down when pressing enter
            e.preventDefault();
            // set username label to input text
            $("#username-label").text(usernameInputField.val());
            // clear previous chat
            clearChatMessages();
            // show from hidden
            usernameLabel.show();
            // hide
            usernameInputField.remove();
            // get new messages
            getUserMessages(usernameLabel.text());
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
        var doesMessageExpire;
        // fin all chat bubbles and filter out the largest ID to begin adding to
        const dropdownTimeSeconds = getMessageExpireTime();
        $('div[id^="chat-bubble-"]').each(function() {
            const currentNum = parseInt($(this).attr('id').replace('chat-bubble-', ''), 10);
            if (currentNum > largestMsgID) {
                largestMsgID = currentNum;
            }

        });
        largestMsgID++;
        // -1 means the message never deletes
        if(dropdownTimeSeconds === -1) {
            addChatBubble(largestMsgID, msgText, party, getTime(false), -1, false);
            doesMessageExpire = false;
        // add the chat bubble to chat window, 0 because the countdown should only begin with the recipient gets message
        } else {
            addChatBubble(largestMsgID, msgText, party, getTime(false), 0, false);
            doesMessageExpire = true;
        }

        // parameter if executed by server or not, isAuto = true for server so we do not do this
        // get dropdown time entered
        // encrypt with recipient's public key

        // search page for recipient getting from
        // implement
        const recipientUsername = usernameLabel.text();
        // retrieve key
        //const encryptedMessage = getKeyAndEncrypt("public_key", recipientUsername);
        //alert(encryptedMessage);
        encryptTest();

        // send message off // TODO change to encrypted later for sendMessageDB
        sendMessageDB(msgText, recipientUsername, dropdownTimeSeconds, doesMessageExpire);
        // generate link
        if ($("#generate-link-checkbox").checked === true) {
            // do stuff
        }

    }
}

/**
 *
 * @param msgID
 * @param msgText
 * @param party
 * @param msgDate
 * @param msgTimeExpire
 * @param messageOpened: flag from server if other party has read the message - in this case, countdown begins
 */
function addChatBubble(msgID, msgText, party, msgDate, msgTimeExpire, messageOpened) {
    // if chat bubble does not exist, create it
    if(!$('#chat-bubble-' + msgID).length) {
        console.log("Adding ID: " +msgID);
        // info element that holds  date and time to expire
        $('<div class="msg-detail" id="msg-detail-' + msgID + '"></div>').appendTo(".panel-chat-body");
        var msgDetail = $('#msg-detail-'+msgID);
        // date element appended to info element
        $('<span id="msg-date-' + msgID + '">' + msgDate + '</span>').appendTo("#msg-detail-" + msgID);
        // expire element appended to info element
        $('<span id="msg-expire-' + msgID + '"></span>').appendTo("#msg-detail-" + msgID);
        $('<div id="chat-bubble-' + msgID + '" class="chat-bubble chat-' + party + '">' + msgText + '</div>').appendTo(".panel-chat-body");
        // clear input
        msgField.val("");
        // auto scroll down when new messages added
        // big number because at some point when enough messages are loaded it won't scroll all the way down with just height()
        $(".panel-chat-body").animate({scrollTop: $(document).height()+50000}, 5);
    }
    //console.log("Created element");
    // These can update the message
    // this code is not causing lag
    const msgExpireElement = $("#msg-expire-" + msgID);
    const msgDateElement = $("#msg-date-"+ msgID);
    const chatBubble = $("#chat-bubble-" + msgID);
    // if the message has been
    if(messageOpened && msgTimeExpire !== -1) {
        // this begins countdown and deletes elements
        initLocalCountdownDelete(msgTimeExpire, msgExpireElement, msgDateElement, chatBubble);
     // message DOES expire but was not opened by other party
    } else if(!messageOpened && msgTimeExpire !== -1) {
        msgExpireElement.text(" - Message unopened");
    // message never expires
    } else if(msgTimeExpire === -1) {
        msgExpireElement.text(" - Permanent Message");
    }
   // console.log("End execution");
}

/**
 * Clear chat messages, dates, and times when talking to someone else
 */
function clearChatMessages() {
    $('div[id^="chat-bubble-"]').each(function(i, value) {
        value.remove();
    });
    $('div[id^="msg-detail-"]').each(function(i, value) {
        value.remove();
    });

}


/**
 *
 * @param message
 * @param recipient
 * @param seconds
 * @param doesExpire - string true or false
 */
function sendMessageDB(message, recipient, seconds, doesExpire) {
    $.ajax({
        method: "POST",
        url: "includes/request.php",
        data: {
            option: "sendmessage",
            messageText: message,
            reqUser: recipient, // user to send to
            expires: doesExpire, // expires
            timeExpire: seconds //SECONDS
        },
        async: true
    });
}


/**
 * Gets all available messages for current open message window
 * @param user - USER IS THE ONE WHO LOGGED IN IS HAVING CONVERSATION WITH
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
        //var messages = [];
        for(let i=0; i < data.length; i++) {
            const msgID = data[i].msg_id;
            const msgText = data[i].msg_text;
            const recipient = data[i].recipient_user;
            const msgDate = data[i].msg_sent_date;
            const msgExpire = data[i].time_expire;
            const doesMsgExpire = data[i].expires;
           // const timeExpire = data[i].time_expire;
            const isMsgRead =  data[i].is_msg_read;
            const msgReadDate = data[i].msg_read_date;
           // these are messages LOGGED in user sent
            if(recipient === user) {
                // if doesMsgExpire = 1, then begin countdown based on time expire
                if(doesMsgExpire === 1 && isMsgRead === 0) {
                    addChatBubble(msgID, msgText, "self", msgDate, msgExpire, false);
                // the timer will need to continue from whatever point the message expires if the user leads the page
                } else if(isMsgRead === "1") {
                    // TODO: GET TIME DIFFERENCE TO CONTINUE TIMER
                } else {
                    // messageOpened = false because message won't expire
                    addChatBubble(msgID, msgText, "self", msgDate, -1, false);
                }
            // this is on the sender's site - we do not update message because it only occurs when a recipient opens a message
            } else {
                if(doesMsgExpire === 1 && isMsgRead === 0) {
                    // send update to server now
                    // message had been read - message deleting handled by backend
                    updateMessage(msgID, 1, recipient);
                    // message will start counting down on user's side
                    addChatBubble(msgID, msgText, "sender", msgDate, msgExpire, true);
                } else if(isMsgRead === 1) {
                    // TODO: GET TIME DIFFERENCE TO CONTINUE TIMER
                    // get timestamp
                    // get message read date + expire time, get difference which = time remaining
                    const timestamp = getTime(true);

                } else {
                    // OWN messages do not expire on client's side
                    addChatBubble(msgID, msgText, "sender", msgDate, 0, false);
                }
            }
        }
        // to determine if it is send or received messages
    });
}

/**
 * Updates the status of message in DB to read when user gets it
 * @param msgID: message ID to find in DB to update
 * @param msgRead: 1 or 0, 1=read
 * @param user: MUST BE LOGGED IN USER
 */
function updateMessage(msgID, msgRead, user) {
    $.ajax({
        method: "POST",
        url: "includes/request.php",
        data: {
            option: "updatemessage",
            msg_ID: msgID,
            msg_read: 1,
            username: user
        },
        async: true
    }).done(function (success) {
        var response = success.responseText;
    });
}


function displayUsernameInput() {
    const usernameArea = $("#username-input-area");
    usernameLabel.hide();
    // only append username field if not already displayed
    const usernameField = $("#username-field");
    if(usernameField.length === 0) {
        $('<input id="username-field" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">').appendTo(usernameArea);
    }
    usernameField.focus();
}

// keytype can = public_key or private_key
function getKeyAndEncrypt(keytype, user, message, authkey) {
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
        alert(success.responseText);
         return encryptMessage(success.responseText, message);
    });
}

/**
 * Encrypts messages
 * @param key
 * @param text
 * @returns encrypted string
 */
function encryptMessage(key, text) {
    alert(key);
    alert(text);
    var e = new JSEncrypt();
    e.setPublicKey(key);
    e.encrypt(text);

    return e;
}

function encryptTest() {
    var pubKey = "-----BEGIN PUBLIC KEY----- MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAscPpWPeAdcLzDNUh5/Zb xAK5ZmEZkj75Z4paI/UcRd81MNaHcH3f9hWRmJs2ZXpZrD4ic5JhgpWfogpPku9c gL5Fjslg5QP+afxDymMrm/tRvc3pE11OdzwLygGEwXY8bEreUg+WK0hqcwt3abTX 4MgK7K/EOVDjwui2GSKlIpBGgG/zG1AlNmT2NFvgEjv+WqK1MB1k6tfdHzDwdquX qet1Gb2Rx7aVmbkHyrHOzKE0m78JxnEQw7WrFrmAdv1Iws+u/ZN3p5O5c5KXn2e7 ZDoIOaVI6CHeqsTUAd0zYeJY2GTMR6/8vsx3FgbZ0tcfnA0laPJh/FKx9xi8yNib UB060AQSwmlbwJXoeoxXEnctwLWy2Q/U+o6ZQK/dFV2gG32aV9xtOvWwnaA0deOI kF5YQ5ls3gr8l2QWaxkbZE7nLbZb/YYVkmKyXJbccMe2I9nW08ypN+UgkFc/iQFl KeYyASggZ4E6kmiUmLbHTgnU1Qjm5+lbdHl4v6C2cctFw82MPuYQcRaODfet4MDN MpEVp3Jj6ykxhte6NdYnwMrMN4uGZu5SGjI90Rz2g9/XPRN5l/ZAzh/Y3G8MPchR /1UkP1pkLThBaKAMYLd8a6WxJtbDTEtEoCALDJwlr0jhdHf0tfDy6/88EOErEKS7 0edmu5zQuzcnLPtkGGIt3dsCAwEAAQ== -----END PUBLIC KEY-----";
    var en = new JSEncrypt();
    var msg = "HELLO!!!";
    en.setPublicKey(pubKey);
    var msgEncrypt = en.encrypt(msg);
    alert(msgEncrypt);

    // decrypt
    var decrypt = en.setPrivateKey("-----BEGIN PRIVATE KEY-----\n" +
        "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\n" +
        "KthsPk/BGKdkjoaJucIQgr1H184gtg7k\n" +
        "-----END PRIVATE KEY-----\n")
    var decryptedText = en.decrypt(msgEncrypt);
    alert(decryptedText);
}


/**
 * Initiates a local countdown timer
 * @param duration
 * @param mExpireElement
 * @param mDateElement
 * @param cBubble
 */
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
            seconds = "0" + seconds;
        }

        mExpireElement.text(" - Time remaining: " + minutes + ":" + seconds);
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
    const dropdownExpireTimeVal = dropdownExpireTime.val();
    if (dropdownExpireTimeVal === "10 seconds") {
        return 10;
    } else if (dropdownExpireTimeVal === "1 minute") {
        return 60
    } else if (dropdownExpireTimeVal === "1 hour") {
        return 3600;
    } else if (dropdownExpireTimeVal === "24 hours") {
        return 86400;
    } else if (dropdownExpireTimeVal === "Never") {
        return -1;
    }
    return -1;
}

/**
 * Returns user's local time to display on messages
 * @returns {*|string}
 */
function getTime(asTimestamp) {
    // get UTC
    const timeUTC = moment.utc().format();
    // convert to time
    const timeLocal = moment.utc(timeUTC).local().format();
    // format time
    if(asTimestamp) {
        return moment().unix();
    }
    return moment(timeLocal).local().format('YYYY-MM-DD HH:mm:ss');
}

function isGenerateLink() {
//TODO: implement method

}