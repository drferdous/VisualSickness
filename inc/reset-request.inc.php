<?php
    if (isset($_POST["reset-submit"])) {
        $selector = bin2hex(random_bytes(8));
        $token = random_bytes(32);
        
        $url = "https://visualsickness.000webhostapp.com/create-new-password.php?selector=" . $selector . "&validator=" . bin2hex($token); 
        
        $expires = mktime(date("G") + 1, date("i"), date("s"), date("m"), date("d"), date("Y")); // G = hours
        
        include '../database.php';
        include "../mailer.php";
        
        $userEmail = $_POST["email"];
        $sql = "DELETE FROM pwdReset WHERE pwdResetEmail=?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo "There was an error in resetting your password.";
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $userEmail);
            mysqli_stmt_execute($stmt);
        }
        
                
        $sql = "INSERT INTO pwdReset (pwdResetEmail, pwdResetSelector, pwdResetToken, pwdResetExpires) VALUES (?, ?, ?, ?);";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo "There was an error in resetting your password.";
            exit();
        } else {
            $hashedToken = password_hash($token, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "ssss", $userEmail, $selector, $hashedToken, $expires);
            mysqli_stmt_execute($stmt);
        }
        
        mysqli_stmt_close($stmt);
        // mysqli_close();
        
        $to = $userEmail;
        $subject = "Reset your Password | Visual Sickness";
        $message = '<p>We received a password reset request. The link to reset your password is below. If you did not make this request, you can ignore this email</p>';
        $message .= '<p>Here is your password reset link: </br>';
        $message .= '<a href="' . $url . '">' . $url . '</a></p>';
        
        sendEmail($to, $subject, $message);
        
        header("Location: ../forgot_password.php?success=true");
    } else {
        header("Location: ../view_study.php");
    }