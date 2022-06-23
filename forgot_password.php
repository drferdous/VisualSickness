<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckLogin();

if(isset($_GET['success']) && $_GET['success'] === "true") {
    echo Util::generateSuccessMessage("Please check your email for a password reset link.");
}
if(isset($_GET['success']) && $_GET['success'] === "bad_email") {
    echo Util::generateErrorMessage('You do not have an account connected to this email!');
}
if(isset($_GET['success']) && $_GET['success'] === "false") {
    echo Util::generateErrorMessage("There was an error in resetting your password.");
}
if (isset($userLog)) {
  echo $userLog;
}

$logout = Session::get('logout');
if (isset($logout)) {
  echo $logout;
}



 ?>

<div class="card">
  <div class="card-header">
          <h3 class='text-center'><i class="fas fa-sign-in-alt mr-2"></i>Forgot Your Password?</h3>
          <p class='text-center'>Enter your email address here. You will receive a email containing details on how to reset it shortly.</p>
        </div>
        <div class="card-body">


            <div style="max-width:450px; margin:0px auto">

            <form class="" action="inc/reset_request" method="post">
                <div style="margin-block: 6px;">
                    <small style='color: red'>
                        * Required Field
                    </small>
                </div>
                <div class="form-group">
                  <label for="email" class="required">Email address</label>
                  <input type="email" name="email"  class="form-control">
                </div>
                <div class="form-group">
                  <button type="submit" name="reset-submit" class="btn btn-success">Reset</button>
                </div>
            </form>
          </div>


        </div>
      </div>



  <?php
  include 'inc/footer.php';

  ?>