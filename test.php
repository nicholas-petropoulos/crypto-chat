<?php

include "includes/config.php";


$username = "test";
$config = array(
    "digest_alg" => "sha512",
    "private_key_bits" => 4096,
   "private_key_type" => OPENSSL_KEYTYPE_RSA,
    "config" => $openSSLConfigPath
);
// generate public and private key
$keyp = openssl_pkey_new($config);

openssl_pkey_export($keyp, $privateKey, "123", $config);
echo $privateKey;

$pubKey = openssl_pkey_get_details($keyp);
$pubKey = $pubKey["key"];
echo "<br>";
$p = var_dump($pubKey);

$key = openssl_pkey_get_private("keys/" . $username . ".key");
var_dump($key);
echo $key;