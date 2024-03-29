<?php
include 'inc/header.php';
Session::CheckLogin();
?>

<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login']) && Session::CheckPostID($_POST)) {
    if (isset($_POST["email"]) && isset($_POST["password"])){
        $userLog = $users->userLoginAuthentication($_POST['email'], $_POST['password']);
        if (isset($userLog)){
            echo $userLog;
        }
    
        Session::CheckLogin();
    }
    else{
        echo Util::generateErrorMessage("No email or password is given.");
    }
}

$logout = Session::get('logout');
if (isset($logout)) {
  echo $logout;
}
?>

<div class="card">
   <div class="card-header">
          <h1 class="text-center font-weight-bold">Visual Sickness</h1>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap w-100">
            <div style="flex: 1 1 50%" class="mb-4">
                <h2 class="text-center mb-3">
                    Researcher Login
                </h2>
                <div style="max-width:450px; margin:0px auto; border-radius: 25px;" class="shadow">
                    <form class="" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="post" style="margin: 10px; padding-top: 30px;">
                        <p class="d-flex justify-content-center"><i class="fas fa-sign-in-alt mr-2"></i>Login to Visual Sickness</p>
                        <?php 
                            $rand = bin2hex(openssl_random_pseudo_bytes(16));
                            Session::set("post_ID", $rand);
                        ?>
                        <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
                        <div style="margin-block: 6px;">
                            <small class='required-msg'>
                                * Required Field
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="email" class="required">Email address</label>
                            <input type="email" value="<?= Util::getValueFromPost('email', $_POST); ?>" name="email" id="email" placeholder="Enter email..." class="form-control" autofocus required>
                        </div>
                        <div class="form-group">
                            <label for="password" class="required">Password</label>
                            <div class="input-group">
                                <input type="password" value="<?= Util::getValueFromPost('password', $_POST); ?>" name="password" id="password" placeholder="Enter password..." data-toggle="password" class="form-control" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-center">
                            <button type="submit" name="login" class="btn btn-success">Login</button>
                        </div>
                        <a href="forgot_password" class="d-flex justify-content-center">Forgot your Password?</a>
                    </form>
                </div>
            </div>
            <div style="flex: 1 1 50%" class="mb-4">
                <h2 class="text-center mb-3">
                    Participant Registration
                </h2>
                <div style="max-width:450px; margin:0px auto; border-radius: 25px;" class="shadow px-4 pb-2 pt-4">
                    <form class="" method="post" id="codeForm" onsubmit="return Validate()">
                        <h4>Enter Your Registration Code</h4>
                        <div class="form-group">
                            <input type="text" class="form-control" pattern="^[a-zA-Z][B-DF-HJ-NP-TV-Z]\d[aeiou][235689]\d{3}$" placeholder="Registration Code" id="txtName"/>
                        </div>
                        <div class="form-group d-flex justify-content-center">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                        <p class="text-center">
                            Not registered for the study? Click <a href="https://docs.google.com/forms/d/e/1FAIpQLScSCdtMj7uQILJpLQPhyVRZa6S5bLZlaPA1ruJ-OV_1gzb8Mw/viewform" target="_blank">
                                here
                            </a>
                        </p>
                        </div>
                    </form>
                </div>
        </div>
    </div>
</div>
<script>
    function Validate() {
        const attr = $('#codeForm').attr('action');
        if (typeof attr !== 'undefined' && attr !== false) {
            return true;
        }
        const code = $('#txtName').val();

        
        $.ajax({
            url: 'check_code',
            type: 'POST',
            cache: false,
            data: {'userCode': code},
            success: function(data) {
                if(data=='exist') {
                  alert("Code has already been used! Contact visualsicknessstudy@gmail.com if this is an error.")
                  return false;
                }
                
                console.log("success");
		console.log("Code: " + code);
                $.ajax({
                    url: 'check_code_ss',
                    type: 'POST',
                    data: {'code': code},
                    success: function(data) {
			console.log("Data: " + data);
                        if(data!=='exist') {
                            alert("Code has not been assigned! Contact visualsicknessstudy@gmail.com if this is an error.")
                            return false;
                        }

                        const regexCheck = /^[a-zA-Z][B-DF-HJ-NP-TV-Z]\d[aeiou][235689]\d{3}$/;
                    
                        if (!regexCheck.test(code)) {
                            alert("Please enter a valid code");
                            return;
                        }
                        
                        const codeForm = $("#codeForm");
                        const codeInp = document.createElement('input');
                        codeInp.setAttribute('name', 'code');
                        codeInp.setAttribute('type', 'hidden');
                        codeInp.setAttribute('value', code);
                        codeForm.append(codeInp);
                        
                        if (/[A-Z]/.test(code.charAt(0))) {
                            codeForm.attr("action","adult_consent_form");
                        } else {
                            codeForm.attr("action","parental_permission_form");
                        }
                        codeForm.submit();
                    }
                });
            }
        });
        return false;
    }
</script>
<?php
  include 'inc/footer.php';