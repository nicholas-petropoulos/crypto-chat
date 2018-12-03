const msgField = $("#msg-field");
const btnMsgSend = $(".btn-msg-send");
const dropdownExpireTime = $(".msg-timer-dropdown");
const usernameLabel = $("#username-label");
// used to differentiate timer elements created
var msgCounter = 0;
var largestMsgID = 0;
var userPublicKey = "";
var userPrivateKey = "";

$(document).ready(function () {
    // function only executes if we are on chat page where class=chat
    $(function(){
        if($('body').is('.chat')){
            // get keys on chat page
            const usernameCookie = getCookie("username");
            const pinCookie = getCookie("pin");
            // if public key and private keys not stored, then grab them
            if(sessionStorage.getItem("public_key_"+usernameCookie) === null ||
                (sessionStorage.getItem("public_key_"+usernameCookie) === "")) {
                getKey("public_key", usernameCookie, pinCookie);
                userPublicKey = sessionStorage.getItem("public_key_"+usernameCookie);
            }
            if(sessionStorage.getItem("private_key_"+usernameCookie) === null ||
                (sessionStorage.getItem("private_key_"+usernameCookie) === "")) {
                getKey("private_key", usernameCookie, pinCookie);
                userPrivateKey = sessionStorage.getItem("public_key_"+usernameCookie);
            }
            // only get messages on chat page
            getUserMessages(encodeHTML(usernameLabel.text()));
            // getUserMessages($("#username-label").text()).then(() => {
            setInterval(function () {
                getUserMessages(encodeHTML(usernameLabel.text()));
            }, 10000);
            //   });
            //reset msg counter
            msgCounter = 0;
            // applies auto-resizing functionality to message field
            autosize(document.querySelector("#msg-field"));
        }
    });

    btnMsgSend.on("click", function () {
        addMessage("self");
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
            getUserMessages(encodeHTML(usernameLabel.text()));
        }
    });
});


/**
 *
 * @param party - Sender or recipient
 */
function addMessage(party) {
    // get text and trim whitespace
    let msgText = encodeHTML(msgField.val().replace(/^[ ]+|[ ]+$/g, ''));
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
        const recipientUsername = encodeHTML(usernameLabel.text());
        // retrieve key
        //const encryptedMessage = getKeyAndEncrypt("public_key", recipientUsername);
        if(sessionStorage.getItem("public_key_" + recipientUsername) === null) {
            // get key and add to storage
            getKey("public_key", recipientUsername, "");
        }
        const recipientPublicKey = sessionStorage.getItem("public_key_" + recipientUsername);
        const encryptedText = encryptMessage(recipientPublicKey, msgText);
        sendMessageDB(encryptedText, recipientUsername, dropdownTimeSeconds, doesMessageExpire);
        // generate link
        if ($("#generate-link-checkbox").checked === true) {
            // do stuff
        }

    }
}

/**
 * Sanitize input - simple xss prevention
 * @param s
 * @returns {string}
 */
function encodeHTML(s) {
    //return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(s));
    return div.innerHTML;
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

            // check if desired key exists in session
            if(sessionStorage.getItem("public_key_" + recipient) === null) {
                // get key and add to storage
                getKey("public_key", recipient, "");
            }
            const msgTextDecrypted = decryptMessage(sessionStorage.getItem("public_key_"+recipient), msgText);

           // these are messages LOGGED in user sent
            if(recipient === user) {
                 // if doesMsgExpire = 1, then begin countdown based on time expire
                if(doesMsgExpire === 1 && isMsgRead === 0) {
                    addChatBubble(msgID, msgTextDecrypted, "self", msgDate, msgExpire, false);
                // the timer will need to continue from whatever point the message expires if the user leads the page
                } else if(isMsgRead === "1") {
                    // TODO: GET TIME DIFFERENCE TO CONTINUE TIMER
                } else {
                    // messageOpened = false because message won't expire
                    addChatBubble(msgID, msgTextDecrypted, "self", msgDate, -1, false);
                }
            // this is on the sender's site - we do not update message because it only occurs when a recipient opens a message
            } else {
                if(doesMsgExpire === 1 && isMsgRead === 0) {
                    // send update to server now
                    // message had been read - message deleting handled by backend
                    updateMessage(msgID, 1, recipient);
                    // message will start counting down on user's side
                    addChatBubble(msgID, msgTextDecrypted, "sender", msgDate, msgExpire, true);
                } else if(isMsgRead === 1) {
                    // TODO: GET TIME DIFFERENCE TO CONTINUE TIMER
                    // get timestamp
                    // get message read date + expire time, get difference which = time remaining
                    const timestamp = getTime(true);

                } else {
                    // OWN messages do not expire on client's side
                    addChatBubble(msgID, msgTextDecrypted, "sender", msgDate, 0, false);
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
    })
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

/**
 * Get a public or private key and store in user's browser session
 * @param keytype: public_key or private_key
 * @param user: username
 * @param usrpin: user's pin
 */
function getKey(keytype, user, usrpin) {
    $.ajax({
        method: "GET",
        url: "includes/request.php",
        data: {
            option: "getkey",
            type: keytype,
            reqUser: user,
            pin: usrpin
        },
        async: false
    }).done(function (success) {
        var keyString = keytype + "_" + user;
        sessionStorage.setItem(keyString, success.toString());
        alert(sessionStorage.getItem(keyString));
    });
}

/**
 * Encrypts messages
 * @param key
 * @param text
 * @returns encrypted string
 */
function encryptMessage(key, text) {
    const e = new JSEncrypt();
    if(key !== "") {
        e.setPublicKey(key);
    }
    return e.encrypt(text);
}

/**
 * Encrypts messages
 * @param key
 * @param text
 * @returns encrypted string
 */
function decryptMessage(key, text) {
    const e = new JSEncrypt();
    if(key !== "") {
        e.setPrivateKey(key);
    }
    return e.decrypt(text);
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

// https://www.w3schools.com/js/js_cookies.asp
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}