<?php
include 'inc/header.php';
include 'database.php';

Session::CheckSession();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resetTempPass'])) {
    $resetTempPass = $users->resetTempPass($_POST);
    if (isset($resetTempPass)){
        echo $resetTempPass; ?>
<?php }
}
?>

<div class="card ">
    <div class="card-header">
        <h3 class="text-center">Reset Temporary Password
        </h3>
    </div>
    
    <div class="card-body pr-2 pl-2">
          <form class="" action="" method="POST">
              <div class="form-group">
                <label for="new_password">Old Password</label>
                <input type="password" name="old_password"  class="form-control">
              </div>
              <div class="form-group">
                <label for="new_passwordconfirm">New Password</label>
                <input type="password" name="new_password"  class="form-control">
              </div>
              <input type="hidden" name="user_ID" value="<?php echo $userid; ?>">    

              <div class="form-group">
                <button type="submit" name="resettemppassword" class="btn btn-success">Change password</button>
              </div>
        </form>
    </div>
</div>

<?php
    include 'inc/footer.php';
?>