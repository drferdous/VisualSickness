<?php
include 'inc/header.php';
Session::CheckLogin();
?>

<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $userLog = $users->userLoginAuthentication($_POST);
    if (isset($userLog)){
        echo $userLog;
    }
    if (Session::get('login') === TRUE){
        header("Location: view_study");
        exit();
    }
}

$logout = Session::get('logout');
if (isset($logout)) {
  echo $logout;
}
?>

<div class="card ">
   <div class="card-header">
          <h3 class="text-center font-weight-bold">VisualSickness</h3>
    </div>
    <div class="card-body">
        <div style="width:450px; margin:0px auto; border-radius: 25px;" class="shadow">
            <form class="" action="" method="post" style="margin:10px 10px 10px 10px; padding: 30px 0px 0px 0px;">
                <p class="d-flex justify-content-center"><i class="fas fa-sign-in-alt mr-2"></i>Login to VisualSickness</p>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" name="email" id="email" placeholder="Enter email..." class="form-control">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter password..." class="form-control">
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