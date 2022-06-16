<?php

$filepath = realpath(dirname(__FILE__));
include_once "$filepath/../config/config.php";

class Crypto {
    public static function encrypt($toEncrypt, &$iv) {
        $ivlen = openssl_cipher_iv_length(CIPHER_ALGO);
        $iv = openssl_random_pseudo_bytes($ivlen);
        return openssl_encrypt($toEncrypt, CIPHER_ALGO, OPENSSL_KEY, $options = 0, $iv);
    }
    public static function decrypt($encrypted, $iv) {
        return openssl_decrypt($encrypted, CIPHER_ALGO, OPENSSL_KEY, $options = 0, $iv);
    }
}