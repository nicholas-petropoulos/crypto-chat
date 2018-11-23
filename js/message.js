
const msgField = $("#msg-field");
const btnMsgSend = $(".btn-msg-send");

$(document).ready(function() {
    // applies auto-resizing functionality to message field
    autosize(document.querySelector("#msg-field"));
    btnMsgSend.on("click", function() {
        addMessage("self")
    });
    msgField.keypress(function(e) {
        let key = e.which;
        if(key === 13) {
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
    let msgText = msgField.val().replace(/^[ ]+|[ ]+$/g,'');
    // check if not empty
    if (msgText) {
        // add chat bubblle
        $('<div class="chat-bubble chat-' + party + '">' + msgText + '</div>').appendTo(".panel-chat-body");
        // clear input
        msgField.val("");
        // auto scroll down when new messages added
        $(".panel-chat-body").animate({ scrollTop: $(document).height()}, 50);

    }
}

/**
 * Gets the dropdown time in seconds to determine how long the user wishes to keep the message
 * @returns {number} - seconds
 */
function getDropdownTime() {
    const dropdownVal = $(".msg-timer-dropdown").val();
    if(dropdownVal == "10 seconds") {
        return 10;
    } else if(dropdownVal == "1 minute") {
        return 60
    } else if(dropdownVal == "1 hour") {
        return 3600;
    } else if(dropdownVal == "24 hours") {
        return 86400;
    } else if(dropdownVal == "Never") {
        return -1;
    }

}

/**
 *
 * @param messageText
 * @param expireTime
 */
function sendMessageDB(messageText, expireTime) {
    const time = "";
    $.ajax({
        method:"POST",
        url:"includes/request.php",
        data:{
            option: "sendmessage",
            messageContent: messageText,
            messageExpire: expireTime
        },
        async: false
    }).done(function(e){

    });
}

function newChat(recipientUsername) {

}