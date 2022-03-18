<?php
include 'inc/header.php';
Session::CheckLogin();
?>


<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
   $userLog = $users->userLoginAuthentication($_POST);
}
if (isset($userLog)) {
  echo $userLog;
}

$logout = Session::get('logout');
if (isset($logout)) {
  echo $logout;
}



 ?>

<div class="card ">
  <div class="card-header">
          <h3 class='text-center'><i class="fas fa-sign-in-alt mr-2"></i>Forgot Your Password?</h3>
          <p class='text-center'>Enter your email address here. You will receive a email containing details on how to reset it shortly.</p>
        </div>
        <div class="card-body">


            <div style="width:450px; margin:0px auto">

            <form class="" action="inc/reset-request.inc" method="post">
                <div class="form-group">
                  <label for="email">Email address</label>
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