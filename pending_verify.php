<?php
include "inc/header.php";
if (Session::get("reg_stat") != 1){
    header("Location: 404");
    exit();
}
?>

<div class="card">
    <div class="card-header">
        <h1 class="mb-0"><i class="fas fa-clock mr-2"></i>Pending Admin Verification</h1>
        <p>An administrator of your group is currently reviewing and verifying your account. When you have been verified, you wil a receive an email. Please check back later.</p>
    </div>
</div>
<?php
include "inc/footer.php";
?>