<?php
include "inc/header.php";
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST["email"])) {
    header("Location: 404");
    exit();
}
$email = $_POST["email"];
if (isset($_POST["reset-submit"])) {
    // Reset password here, then log in user and redirect to homepage
    $password = $_POST["password"];
    $changePass = $users->resetPass($email, $password);
    if ($changePass) {
        echo $changePass;
        exit();
    }
    $userLog = $users->userLoginAuthentication($_POST);
    if (isset($userLog)){
        echo $userLog;
        exit();
    }
    header("Location: index");
}
?>
<div class="card ">
    <div class="card-header">
        <h3 class='text-center'><i class="fas fa-sign-in-alt mr-2"></i>New Password</h3>
        <p class='text-center'>Enter a new password here. Make sure to remember this password for the future.</p>
    </div>
    <div class="card-body">
        <div style="width:450px; margin:0px auto">
            <form class="" action="" method="post">
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" name="password"  class="form-control">
                </div>
                <input type="hidden" name="email" value="<?=$email?>"
                <div class="form-group">
                    <button type="submit" name="reset-submit" class="btn btn-success">Set Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include "inc/footer.php";
?>