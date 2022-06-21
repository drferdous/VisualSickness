<?php
include 'inc/header.php';
Session::CheckLogin();
?>

<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login']) && Session::CheckPostID($_POST)) {
    $userLog = $users->userLoginAuthentication($_POST);
    if (isset($userLog)){
        echo $userLog;
    }
    
    if (Session::get('login') == TRUE){
        if (Session::get('roleid') == '1') {
            $homepage = "userlist";
        } else {
            $homepage = "view_study";
        }
        header("Location: " . $homepage);
    }
}

$logout = Session::get('logout');
if (isset($logout)) {
  echo $logout;
}
?>

<div class="card">
   <div class="card-header">
          <h3 class="text-center font-weight-bold">Visual Sickness</h3>
    </div>
    <div class="card-body">
        <div style="max-width:450px; margin:0px auto; border-radius: 25px;" class="shadow">
            <form class="" action="" method="post" style="margin: 10px; padding-top: 30px;">
                <p class="d-flex justify-content-center"><i class="fas fa-sign-in-alt mr-2"></i>Login to Visual Sickness</p>
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
                    <label for="email" class="required">Email address</label>
                    <input type="email" value="<?= Util::getValueFromPost('email', $_POST); ?>" name="email" id="email" placeholder="Enter email..." class="form-control" autofocus required>
                </div>
                <div class="form-group">
                    <label for="password" class="required">Password</label>
                    <input type="password" value="<?= Util::getValueFromPost('password', $_POST); ?>" name="password" id="password" placeholder="Enter password..." class="form-control" required>
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