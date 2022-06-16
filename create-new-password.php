<?php
include "inc/header.php";
include_once "lib/Database.php";
$pdo = Database::getInstance()->pdo;
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (!isset($_GET["selector"]) || !isset($_GET["validator"])) {
        header("Location: 404.php");
        exit();
    }
    
    $selector = $_GET["selector"];
    $validator = $_GET["validator"];
    
    if (ctype_xdigit($validator) && strlen($validator) % 2 === 0) {
        $validator = hex2bin($validator);
    } else {
        echo "Bad validator parameter.";
        exit();
    }
        
    $sql = 'SELECT pwdResetEmail, pwdResetToken, pwdResetExpires FROM pwdReset WHERE pwdResetSelector = :selector;';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':selector', $selector, PDO::PARAM_STR);
    
    $stmt->execute();
    $res = $stmt->fetch();
    if ($res) {
        if (!password_verify($validator, $res["pwdResetToken"])) {
            echo "Bad validation.";
            exit();
        }
        if (Date("U") > $res["pwdResetExpires"]) {
            echo "Token expired.";
            exit();
        }
        $sql = "UPDATE pwdReset SET pwdResetExpires = 0 WHERE pwdResetEmail=?;";
        $stmt = $pdo->prepare($sql);
        if (!$stmt) {
            echo "There was an error in resetting your password.";
            exit();
        } else {
            $stmt->bindParam(1, $res["pwdResetEmail"], PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
        }
    } else {
        echo "Bad token.";
        exit();
    }
}?>
<script>
    $(document).ready(() => {
        let form = document.createElement("form");
        let hiddenInput;
        
        form.setAttribute("method", "POST");
        form.setAttribute("action", 'create-password');
        form.setAttribute("style", "display: none");
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "email");
        hiddenInput.setAttribute("value", "<?= $res["pwdResetEmail"];?>");
        form.appendChild(hiddenInput);
        
        document.body.appendChild(form);
        form.submit();
    });
</script>