<?php
/**
 * Created by IntelliJ IDEA.
 * user: Nicholas
 * Date: 11/21/2018
 * Time: 8:31 PM
 */
session_start();
include "includes/user.php";
$username = $_SESSION["username"];
if (isset($username)) {


// change getUserMessages to logged in user - currently testing
    ?>

    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>CryptoChat</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/style.css" rel="stylesheet"/>
        <link href="css/bootstrap.css" rel="stylesheet"/>
        <link href="css/bootstrap-theme.css" rel="stylesheet"/>
    </head>

    <body>
    <div class="container container-top">
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#bs-example-navbar-collapse-1"
                            aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.html">CryptoChat</a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <!--<li class="active"><a href="#">Learn More <span class="sr-only">(current)</span></a></li> -->
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li id="logged-in-user"><a href="#">Welcome <?php echo $username ?></a></li>
                        <li class="nav-log-out"><a href="logout.php">Log Out</a></li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
    </div>
    <div class="container-fluid container-article remove-padding">
        <div class="jumbotron jumbotron-chat-area">
            <div class="row chat-row">
                <div class="col-xs-12">
                </div>
                <!--
                <div class="col-xs-4 hidden-md hidden-xs hidden-s">
                    <div class="panel panel-default panel-conv">
                        <div class="panel-heading">
                            <a href="#">New Chat
                                <i class="glyphicon glyphicon-plus"></i></a>
                        </div>
                        <div class="panel-body">
                            <h4>USER1</h4>
                            <a href="#" class="msg-sidebar">Last message | Time</a>
                            <hr/>
                            <h4>USER2</h4>
                            <a href="#" class="msg-sidebar">Last message)</a>
                            <hr/>
                            <h4>USER3</h4>
                            <p>Last message</p>
                            <hr/>
                            <h4>USER4</h4>
                            <p>Last message</p>
                        </div>
                    </div>
                </div>-->

                <div class="col-xs-12">
                    <div class="panel panel-default panel-chat">
                        <div class="panel-heading panel-chat-heading">
                            <span id="new-chat"> <a href="#">New Chat
                                <i class="glyphicon glyphicon-plus"></i></a></span>
                            &nbsp;&nbsp;
                            <span id="username-input-area"><span id="username-label"></span>


</span>
                            </span>

                            <!--<i class="glyphicon glyphicon-arrow-left ">-->


                        </div>
                        <div class="panel-body panel-chat-body">
                            <div class="msg-detail">Your messages will show up here - start a new conversation above by clicking "New Chat"</div>
                            <br>
                            <!--<div class="msg-detail"><span id="msg-date">XX/XX/XXXX</span>&nbsp-&nbsp;<span id="msg-expire">XX:XX:XX</span></div>-->
                            <!--<div class="chat-bubble chat-sender">Hello there test</div>-->
                            <!--<div class="chat-bubble chat-self">Hey!</div>-->
                        </div>
                    </div>

                    <div class="chat-input-group">
                        <div class="input-group">
                            <textarea class="form-control" id="msg-field" rows="2" maxlength="495"></textarea>
                            <span class="input-group-addon"><button type="submit"
                                                                    class="btn btn-success btn-msg-send">Send</button></span>
                        </div>
                        <span class="input-group-addon input-group-addon-msgtimer">
                                <select class="form-control form-control-sm msg-timer-dropdown">
                                    <option>10 seconds</option>
                                    <option>1 minute</option>
                                    <option>1 hour</option>
                                    <option>24 hours</option>
                                    <option>Never</option>
                                </select>
                         <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="generate-link-checkbox">
                            <label class="form-check-label">Generate link?</label>
                          </div>
                    </span>

                    </div>

                </div>
            </div>
        </div>
    </div>
    <!--<div hidden class="session-name"><?php //echo $_SESSION["username"]; ?></div>-->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/moment.js"></script>
    <script src="js/jsencrypt.min.js"></script>
    <script src="js/cryptochat.js"></script>
    <script src="js/autosize.js"></script>
    </body>
    <footer>
        <div class="container">
            <p>&copy; CryptoChat 2018. All Rights Reserved.</p>
            <ul class="list-inline">
                <li class="list-inline-item">
                    <a href="#">Privacy</a>
                </li>
                <li class="list-inline-item">
                    <a href="#">Terms</a>
                </li>
                <li class="list-inline-item">
                    <a href="#">FAQ</a>
                </li>
            </ul>
        </div>
    </footer>

    </html>
    <?php
} else {
    header("Location: login.php");
}
?>