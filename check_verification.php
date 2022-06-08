<?php
function checkVerification() {
    switch (Session::get("reg_stat")) {
        case 0:
            header("Location: verify_password");
            break;
        case 1:
            header("Location: pending_verification");
            break;
        case 2:
            break;
    }
}
?>