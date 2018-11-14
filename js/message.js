//import autosize from "./autosize";

/*
const msgField = $("#msg-field");
const maxHeight = 60; //keydown
msgField.on('input', function() {
    const field = $(this);
    if (field.scrollTop()) {
        field.height(function (i, h) {
            // if (h < maxHeight) {
            return h + 20;
            //}
            //return maxHeight;
        });
    }
});
*/


$(document).ready(function() {
    // applies auto-resizing functionality to message field
    autosize(document.querySelector("#msg-field"));
    //$(".panel-chat").add("div").class("chat-bubble chat-sender").add("p");
    const panelChatBody = $(".panel-chat-body")
    const btnMsgSend = $(".btn-msg-send");
    btnMsgSend.on("click", function() {
        addMessage("self")
    });
    const msgField = $("#msg-field");
    msgField.keypress(function(e) {
        let key = e.which;
        if(key === 13) {
            // do not move down when pressing enter
            e.preventDefault();
            addMessage("self");
            window.scrollTo(0,document.querySelector(".panel-chat").scrollHeight);
            // panelChatBody.scrollTo(999, 999);
        }
    });

});

// recipient = user , sender = other party
function addMessage(party) {
    const msgField = $("#msg-field");
    let msgText = msgField.val();
    // check if not empty
    if (msgField.val()) {
        // add chat bubblle
        $('<div class="chat-bubble chat-' + party + '">' + msgText + '</div>').appendTo(".panel-chat-body");
        // clear input
        msgField.val("");
    }
}