<?php
    include "classes/Crypto.php";
    
    $message = "";
    $iv = "";
    $encryptedMessage = Crypto::encrypt($message, $iv);
    $decryptedMessage = Crypto::decrypt($encryptedMessage, $iv);
    $ivarray = unpack("C*", $iv);
    $unpackediv = implode(",", $ivarray);
    
    echo "<p> Original Message: " . $message . "</p>";
    echo "<p> IV: " . $iv . "</p>";
    echo "<p>" . print_r($ivarray) . "</p>";
    echo "<p> Imploded Array: " . $unpackediv . "</p>";
    echo "<p> Encrypted Message: " . $encryptedMessage . "</p>";
    echo "<p> Decrypted Message: " . $decryptedMessage . "</p>";
?>