<?php
include 'inc/header.php';

Session::CheckSession();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resetTempPass'])) {
    $resetTempPass = $users->resetTempPass($_POST);
    if (isset($resetTempPass)){
        echo $resetTempPass; ?>
<?php }
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="text-center">Reset Temporary Password
        </h3>
    </div>
    
    <div class="card-body pr-2 pl-2">
        <form class="" action="" method="POST">
            <div style="margin-block: 6px;">
                <small style='color: red'>
                    * Required Field
                </small>
            </div>
            <div class="form-group">
                <label for="old_password" class="required">Old Password</label>
                <input type="password" name="old_password" id="old_password" value="<?= Util::getValueFromPost('old_password', $_POST); ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="new_password" class="required">New Password</label>
                <input type="password" name="new_password" id="new_password" value="<?= Util::getValueFromPost('new_password', $_POST); ?>" class="form-control" required>
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