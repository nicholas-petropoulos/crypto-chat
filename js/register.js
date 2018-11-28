$(document).ready(function() {
    /*
    var base_url = window.location.origin;
    var host = window.location.host;
    var pathArray = window.location.pathname.split( '/' );

    $(document).ready(function() {
        $("#btn-submit").on("click", function() {
            //e.preventDefault();
            var username = $("#username").val();
            var email = $("#email").val();
            var password = $("#password");
            var passwordConfirm = $("#password-confirm").val();
            // are passwords equal & fields not empty
            var fieldError = isEmpty([username, email, password, passwordConfirm]);
            if(username != "" && email != "" && password != "" && passwordConfirm != "" ) {

            } else {
                alert("Fields empty");
            }
            if (password === passwordConfirm) {
                alert("Pass good.");
                $.ajax({
                    method: "POST",
                    url: "register.php",
                    data: {
                        option: "register",
                        username: username,
                        email: email,
                        password: password
                    },
                    async: false
                }).done(function (msg) {
                    var regArea = $("#register-area");
                    regArea.empty();
                    const text = "<h3>Thank you for registering</h3>";
                    regArea.append(text);
                });
            } else {
                alert(" Field Err: " + fieldError);

            }
        });

    });

    function isEmpty(fields){
        var error = "Please fill out fields: ";
        var errorFields = "";
        for(var i = 0; i < fields.size; i++) {
            if(fields[i] == "" || fields[i] == null) {
                errorFields += (" Test:" + fields[i]);
                console.log(error)
            }
        }
        if(errorFields == "") {
            return "";
        } else {
            return error+errorFields;
        }
    }
*/
});

