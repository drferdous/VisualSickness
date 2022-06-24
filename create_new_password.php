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
        
    $sql = 'SELECT password_reset_email, password_reset_token, password_reset_expires FROM password_reset WHERE password_reset_selector = :selector;';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':selector', $selector, PDO::PARAM_STR);
    
    $stmt->execute();
    $res = $stmt->fetch();
    if ($res) {
        if (!password_verify($validator, $res["password_reset_token"])) {
            echo "Bad validation.";
            exit();
        }
        if (Date("U") > $res["password_reset_expires"]) {
            echo "Token expired.";
            exit();
        }
        $sql = "UPDATE password_reset SET password_reset_expires = 0 WHERE password_reset_email=?;";
        $stmt = $pdo->prepare($sql);
        if (!$stmt) {
            echo "There was an error in resetting your password.";
            exit();
        } else {
            $stmt->bindParam(1, $res["password_reset_email"], PDO::PARAM_STR);
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
        form.setAttribute("action", 'create_password');
        form.setAttribute("style", "display: none");
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "email");
        hiddenInput.setAttribute("value", "<?= $res["password_reset_email"];?>");
        form.appendChild(hiddenInput);
        
        document.body.appendChild(form);
        form.submit();
    });
</script>