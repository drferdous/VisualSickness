<?php
    include 'inc/header.php';
    Session::CheckSession();
?>
<?php

    $userid = Session::get("id");

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changepass']) && Session::CheckPostID($_POST)) {
        $changePass = $users->changePasswordBysingleUserId($userid, $_POST);
    }



if (isset($changePass)) {
    echo  $changePass;?>
    <script type="text/javascript">
        const divMsg = document.getElementById("flash-msg");
        if (divMsg.classList.contains("alert-success")){
            setTimeout(function(){
                redirect('profile', {purpose: 'edit'})
            }, 1000);
        }
    </script>
<?php } ?>


<div class="card">
    <div class="card-header">
        <h3 class="float-left">Change your password</h3>
        <a href="profile" class="btn btn-primary float-right">Back</a> 
    </div>
        <div class="card-body">
            <div style="max-width:600px; margin:0px auto">

            <?php 
                $rand = bin2hex(openssl_random_pseudo_bytes(16));
                Session::set("post_ID", $rand);
            ?>
            <form class="" action="" method="POST">
                <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
                <div style="margin-block: 6px;">
                    <small style='color: red'>
                        * Required Field
                    </small>
                </div>
                <div class="form-group">
                    <label for="old_password" class="required">Old Password</label>
                    <div class="input-group">
                        <input type="password" name="old_password" value="<?= Util::getValueFromPost('old_password', $_POST); ?>" class="form-control" data-toggle="password" id="old_password" required>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="new_password" class="required">New Password</label>
                    <div class="input-group">
                        <input type="password" name="new_password" value="<?= Util::getValueFromPost('new_password', $_POST); ?>" class="form-control" data-toggle="password" id="new_password" required>
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
                        <input type="password" name="confirm_password" value="<?= Util::getValueFromPost('confirm_password', $_POST); ?>" class="form-control" data-toggle="password" id="confirm_password" required>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-danger error-text"></small>
                </div>
                <div class="form-group">
                    <button type="submit" name="changepass" class="btn btn-success">Change password</button>
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
    $('#new_password').on('keyup', function () {
        const val = $(this).val();
        if (!val.length) {
            $(this).parent().next('.error-text').text('');
            passwordValid = false;
            return;
        }
        if (!lengthRegex.test(val)) {
            $(this).parent().next('.error-text').text('Password must be between 7 and 20 characters long.');
            passwordValid = false;
            return;
        }
        if (!lowercaseRegex.test(val)) {
            $(this).parent().next('.error-text').text('Password must contain at least one lowercase letter.');
            passwordValid = false;
            return;
        }
        if (!uppercaseRegex.test(val)) {
            $(this).parent().next('.error-text').text('Password must contain at least one uppercase letter.');
            passwordValid = false;
            return;
        }
        if (!digitRegex.test(val)) {
            $(this).parent().next('.error-text').text('Password must contain at least one digit.');
            passwordValid = false;
            return;
        }
        if (!symbolRegex.test(val)) {
            $(this).parent().next('.error-text').text('Password must contain at least one non-alphanumeric symbol.');
            passwordValid = false;
            return;
        }
        if (!validCharsRegex.test(val)) {
            $(this).parent().next('.error-text').text('Password contains invalid character(s)! All characters must be alphanumeric or an ASCII symbol besides space.');
            passwordValid = false;
            return;
        }
        $(this).parent().next('.error-text').text('');
        passwordValid = true;
    });
    $('#confirm_password').on('keyup', function () {
        const val = $(this).val();
        if (!passwordValid || !val.length) {
            $(this).parent().next('.error-text').text('');
            passwordConfirm = false;
            return;
        }
        if (val !== $('#new_password').val()) {
            $(this).parent().next('.error-text').text('Passwords must match!');
            passwordConfirm = false;
            return;
        }
        $(this).parent().next('.error-text').text('');
        passwordConfirm = true;
    });
    $('form').submit(() => {
        // if (!passwordValid) return false;
        // const pass = $('#new_password');
        // const confPass = $('#confirm_password');
        // if (pass.val() !== confPass.val()) {
        //     alert(`'New Password' and 'Confirm Password' fields must match!`);
        //     return false;
        // }
        // return true;
        return passwordValid && passwordConfirm;
    });
</script>


<?php
  include 'inc/footer.php';
?>