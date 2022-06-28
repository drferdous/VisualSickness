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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset-submit"]) && Session::CheckPostID($_POST)) {
    $changePass = $users->resetPass($email, $_POST["new_password"], $_POST["confirm_password"]);
    if ($changePass) {
        echo $changePass;
    } else if (Session::get('reg_stat') == 0) {
        // Get comma-separated list of admin email addresses for the user's affiliation
        $adminString = Util::getAdminsFromAffiliation($pdo, Session::get('affiliationid'));
        $body = '<p>A new user has signed up for Visual Sickness Study under your affiliation: ' . Util::getAffiliationNameById($pdo, Session::get('affiliationid'));
        if (!$adminString) {
            $adminString = "visualsicknessstudy@gmail.com";
            $body = '<p>A new user has signed up for Visual Sickness Study under an affiliation with no administrator: ' . Util::getAffiliationNameById($pdo, Session::get('affiliationid'));
        }
        
        $body .= "<br><br>The user has signed up with the name <strong>" . Session::get("name") . "</strong> and email <strong>" . Session::get('email') . '</strong>.<br>';
        // send email here
        sendEmail($adminString, 'Visual Sickness | New User Registration', $body);
        $sql = 'UPDATE users SET registration_status = 1 where user_id = ' . Session::get('id') . ';';
        $result = Database::getInstance()->pdo->query($sql);
        Session::set("reg_stat", 1);
        if (!$result) {
            echo 'An error occurred. Please try again.';
            exit();
        }
        echo Util::generateSuccessMessage("You will receive an email when your account is verfied by an administrator.");
    }
    ?>
    <script>
        setTimeout(() => location.href='pending_verify', 2000);
    </script>
<?php } ?>
<div class="card">
    <div class="card-header">
        <h3 class='text-center'><i class="fas fa-sign-in-alt mr-2"></i>New Password</h3>
        <p class='text-center'>Enter your new password here. An admin will view your request & approve it. Then, you will gain full access to the site</p>
    </div>
    <div class="card-body">
        <div style="width:450px; margin:0px auto">
            <form class="" action="" method="post">
                <div style="margin-block: 6px;">
                    <small style='color: red'>
                        * Required Field
                    </small>
                </div>
                <?php 
                    $rand = bin2hex(openssl_random_pseudo_bytes(16));
                    Session::set("post_ID", $rand);
                ?>
                <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
                <div class="form-group">
                    <label for="new_password" class="required">New Password</label>
                    <div class="input-group">
                        <input type="password" id="new_password" name="new_password" data-toggle="password" class="form-control" required>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-danger error-text"></small>
                </div>
                <div class="form-group">
                    <label for="confirm_password" class="required">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" id="confirm_password" name="confirm_password" data-toggle="password" class="form-control" required>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-danger error-text"></small>
                </div>
                <div class="form-group">
                    <button type="submit" name="reset-submit" class="btn btn-success">Change</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    let passwordValid = false;
    let passwordConfirmed = false;
    const lowercaseRegex = /^(?=.*[a-z]).*$/;
    const uppercaseRegex = /^(?=.*[A-Z]).*$/;
    const digitRegex = /^(?=.*[\d]).*$/;
    const symbolRegex = /^(?=.*[\x21-\x2F\x3A-\x40\x5B-\x60\x7B-\x7E]).*$/;
    const validCharsRegex = /^[a-zA-Z\d\x21-\x2F\x3A-\x40\x5B-\x60\x7B-\x7E]*$/;
    const lengthRegex = /^.{7,20}$/;
    $('#submitBtn').attr('disabled', true);
    $('#new_password').on('keyup', function () {
        validation();
    });
    $('#confirm_password').on('keyup', function () {
        validation();
    });
    const validation = () => {
        const val = $('#new_password').val();
        $('#submitBtn').attr('disabled', true);
        $('#new_password').parent().next('.error-text').text('');
        $('#confirm_password').parent().next('.error-text').text('');
        if (!val.length) {
            $('#new_password').parent().next('.error-text').text('');
            passwordValid = false;
            return;
        }
        if (!lengthRegex.test(val)) {
            $('#new_password').parent().next('.error-text').text('Password must be between 7 and 20 characters long.');
            passwordValid = false;
            return;
        }
        if (!lowercaseRegex.test(val)) {
            $('#new_password').parent().next('.error-text').text('Password must contain at least one lowercase letter.');
            passwordValid = false;
            return;
        }
        if (!uppercaseRegex.test(val)) {
            $('#new_password').parent().next('.error-text').text('Password must contain at least one uppercase letter.');
            passwordValid = false;
            return;
        }
        if (!digitRegex.test(val)) {
            $('#new_password').parent().next('.error-text').text('Password must contain at least one digit.');
            passwordValid = false;
            return;
        }
        if (!symbolRegex.test(val)) {
            $('#new_password').parent().next('.error-text').text('Password must contain at least one non-alphanumeric symbol.');
            passwordValid = false;
            return;
        }
        if (!validCharsRegex.test(val)) {
            $('#new_password').parent().next('.error-text').text('Password contains invalid character(s)! All characters must be alphanumeric or an ASCII symbol besides space.');
            passwordValid = false;
            return;
        }
        $('#new_password').parent().next('.error-text').text('');
        passwordValid = true;
        
        const confVal = $('#confirm_password').val();
        if (!confVal.length) {
            $('#confirm_password').parent().next('.error-text').text('');
            passwordConfirm = false;
            return;
        }
        if (confVal !== val) {
            $('#confirm_password').parent().next('.error-text').text('Passwords must match!');
            passwordConfirm = false;
            return;
        }
        $('#confirm_password').parent().next('.error-text').text('');
        passwordConfirm = true;
        $('#submitBtn').removeAttr('disabled');
    }
    $('form').submit(() => {
        return passwordValid && passwordConfirm;
    });
</script>

<?php
include 'inc/footer.php';

?>