<?php

// DATABASE CONFIGURATION
$con = mysqli_connect("localhost", "root", "", "cryptochat");

// enable extension - extension=openssl in php.ini
$openSSLConfigPath = "C:/Development/xampp/php/extras/ssl/openssl.cnf";
// for privatet key export
$openSSLConfigPathArr = array($openSSLConfigPath);