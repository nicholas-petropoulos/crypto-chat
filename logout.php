<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nicholas
 * Date: 11/24/2018
 * Time: 8:10 PM
 */

session_unset();
session_destroy();
header("Location: index.html");