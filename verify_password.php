<?php
include "lib/Session.php";
include_once 'lib/Database.php';
include_once "classes/Users.php";
include "inc/header.php";
$users = new Users();
$pdo = Database::getInstance()->pdo;
Session::init();
Session::CheckSession();

$email = Session::get("email");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset-submit"])) {
    
    $changePass = $users->resetPass($email, $_POST["password"]);
    if ($changePass) {
        echo $changePass;
    } else if (Session::get('reg_stat') == 0) {
        // Get comma-separated list of admin email addresses for the user's affiliation
        $adminString = Util::getAdminsFromAffiliation($pdo, Session::get('affiliationid'));
        if (!$adminString) {
            echo 'An error has occurred. Please try again.';
            exit();
        }
        $body = '<p>A new user has signed up for Visual Sickness Study under your affiliation: ' . Util::getAffiliationNameById($pdo, Session::get('affiliationid'));
        $body .= "<br><br>The user has signed up with the name <strong>" . Session::get("name") . "</strong> and email <strong>" . Session::get('email') . '</strong>.<br>';
        // send email here
        sendEmail($adminString, 'Visual Sickness | New User Registration', $body);
        $sql = 'UPDATE tbl_users SET reg_stat = 1 where id = ' . Session::get('id') . ';';
        $result = Database::getInstance()->pdo->query($sql);
        Session::set("reg_stat", 1);
        if (!$result) {
            echo 'An error occurred. Please try again.';
            exit();
        }
        echo '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success!</strong> Password changed successfully! You will receive an email when your account is verified by an administrator.</div>';
    }
    echo "<script>setTimeout(() => location.href='index', 2000)</script>";
}
 ?>
<div class="card ">
    <div class="card-header">
        <h3 class='text-center'><i class="fas fa-sign-in-alt mr-2"></i>New Password</h3>
        <p class='text-center'>Enter your new password here. An admin will view your request & approve it. Then, you will gain full access to the site</p>
    </div>
    <div class="card-body">
        <div style="width:450px; margin:0px auto">
            <form class="" action="" method="post">
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" name="password"  class="form-control">
                </div>
                <div class="form-group">
                    <button type="submit" name="reset-submit" class="btn btn-success">Change</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include 'inc/footer.php';

?>