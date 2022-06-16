<?php
    if (isset($_POST["reset-submit"])) {
        $selector = bin2hex(random_bytes(8));
        $token = random_bytes(32);
        
        $url = "https://visualsickness.000webhostapp.com/create-new-password.php?selector=" . $selector . "&validator=" . bin2hex($token); 
        echo $url;
        
        $expires = mktime(date("G") + 1, date("i"), date("s"), date("m"), date("d"), date("Y")); // G = hours
        
        include_once '../lib/Database.php';
        include "../mailer.php";
        
        $db = Database::getInstance();
        $pdo = $db->pdo;
        
        $userEmail = $_POST["email"];
        $sql = "DELETE FROM pwdReset WHERE pwdResetEmail=?;";
        $stmt = $pdo->prepare($sql);
        if (!$stmt) {
            echo "There was an error in resetting your password.";
            exit();
        } else {
            $stmt->bindValue(1, $userEmail, PDO::PARAM_STR);
            $stmt->execute();
        }
                
        $sql = "INSERT INTO pwdReset (pwdResetEmail, pwdResetSelector, pwdResetToken, pwdResetExpires) VALUES (?, ?, ?, ?);";
        $stmt = $pdo->prepare($sql);
        if (!$stmt) {
            echo "There was an error in resetting your password.";
            exit();
        } else {
            $hashedToken = password_hash($token, PASSWORD_DEFAULT);
            $stmt->execute([$userEmail, $selector, $hashedToken, $expires]);
        }
        $stmt->closeCursor();
        // mysqli_close();
        
        $to = $userEmail;
        $subject = "Reset your Password | Visual Sickness";
        $message = '<p>We received a password reset request. The link to reset your password is below. If you did not make this request, you can ignore this email</p>';
        $message .= '<p>Here is your password reset link: </br>';
        $message .= '<a href="' . $url . '">' . $url . '</a></p>';
        
        // sleep(5);
        sendEmail($to, $subject, $message);
        
        header("Location: ../forgot_password.php?success=true");
    } else {
        header("Location: ../forgot_password.php");
    }