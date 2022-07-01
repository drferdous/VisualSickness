<?php
    if (isset($_POST["reset-submit"]) && isset($_POST["email"])) {
        $selector = bin2hex(random_bytes(8));
        $token = random_bytes(32);
        $host_url = $_SERVER['HTTP_REFERER'];
        $url = parse_url($host_url, PHP_URL_SCHEME) . "://" . parse_url($host_url, PHP_URL_HOST) . "/create_new_password.php?selector=" . $selector . "&validator=" . bin2hex($token);
        
        $expires = mktime(date("G") + 1, date("i"), date("s"), date("m"), date("d"), date("Y")); // G = hours
        
        include_once '../lib/Database.php';
        include "../mailer.php";
        include_once "../classes/Util.php";
        
        $db = Database::getInstance();
        $pdo = $db->pdo;
        
        $userEmail = $_POST["email"];
        $user_sql = "SELECT user_id FROM users
                     WHERE email = ?
                     AND status > 0;";
        $stmt = $pdo->prepare($user_sql);
        if (!$stmt) {
            header('Location: ../forgot_password.php?success=false');
            exit();
        } else {
            $stmt->bindValue(1, $userEmail, PDO::PARAM_STR);
            $result = $stmt->execute();
            if ($result && !$stmt->fetch(PDO::FETCH_ASSOC)['user_id']) {
                header('Location: ../forgot_password.php?success=bad_email');
                exit();
            }
        }
                     
        $sql = "DELETE FROM password_reset WHERE password_reset_email=?;";
        $stmt = $pdo->prepare($sql);
        if (!$stmt) {
            header('Location: ../forgot_password.php?success=false');
            exit();
        } else {
            $stmt->bindValue(1, $userEmail, PDO::PARAM_STR);
            $stmt->execute();
        }
                
        $sql = "INSERT INTO password_reset (password_reset_email, password_reset_selector, password_reset_token, password_reset_expires) VALUES (?, ?, ?, ?);";
        $stmt = $pdo->prepare($sql);
        if (!$stmt) {
            header('Location: ../forgot_password.php?success=false');
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
        header("Location: ../forgot_password.php?success=false");
    }