<?php

// DATABASE CONFIGURATION
$con = mysqli_connect("localhost", "root", "", "cryptochat");

// enable extension - extension=openssl in php.ini
$openSSLConfigPath = "C:/xampp/apache/conf/openssl.cnf";
//$openSSLConfigPath = "C:/Development/xampp/php/extras/ssl/openssl.cnf";
// for privatet key export
$openSSLConfigPathArr = array($openSSLConfigPath);
// key config
// enable extension - extension=openssl in php.ini
$openSSLConfig = array(
    "digest_alg" => "sha512",
    // Note: Can be increased to 4096 bits but registration takes longer during key generation and 2048 bits is enough security
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
    "config" => $openSSLConfigPath
);
