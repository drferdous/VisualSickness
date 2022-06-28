<?php
include "inc/header.php";
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST["email"])) {
    header("Location: 404");
    exit();
}
$email = $_POST["email"];
if (isset($_POST["reset-submit"]) && Session::CheckPostID($_POST)) {
    // Reset password here, then log in user and redirect to homepage
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];
    $changePass = $users->resetPass($email, $new_password, $confirm_password);
    if ($changePass) {
        echo $changePass;
    } else {
        $userLog = $users->userLoginAuthentication($email, $new_password);
        if (isset($userLog)){
            echo $userLog;
        }
        
        Session::CheckLogin();
    }
}
?>
<div class="card">
    <div class="card-header">
        <h3 class='text-center'><i class="fas fa-sign-in-alt mr-2"></i>New Password</h3>
        <p class='text-center'>Enter a new password here. Make sure to remember this password for the future.</p>
    </div>
    <div class="card-body">
        <div style="width:450px; margin:0px auto">
            <form class="" action="" method="post">
                <?php 
                    $rand = bin2hex(openssl_random_pseudo_bytes(16));
                    Session::set("post_ID", $rand);
                ?>
                <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
                <div style="margin-block: 6px;">
                    <small style='color: red'>
                        * Required Field
                    </small>
                </div>
                <div class="form-group">
                    <label for="new_password" class="required">New Password</label>
                    <div class="input-group">
                        <input type="password" data-toggle="password" id="new_password" name="new_password"  class="form-control" required>
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
                        <input type="password" data-toggle="password" id="confirm_password" name="confirm_password"  class="form-control" required>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-danger error-text"></small>
                </div>
                <input type="hidden" name="email" value="<?=$email?>">
                <div class="form-group">
                    <button type="submit" name="reset-submit" class="btn btn-success">Set Password</button>
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
    $('#submitBtn').attr('disabled', 'true');
    $('#new_password').on('keyup', function () {
        validation();
    });
    $('#confirm_password').on('keyup', function () {
        validation();
    });
    const validation = () => {
        const val = $('#new_password').val();
        $('#submitBtn').attr('disabled', 'true');
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
include "inc/footer.php";
?>