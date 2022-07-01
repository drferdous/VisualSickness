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
</div>
<?php
  include 'inc/footer.php';
?>