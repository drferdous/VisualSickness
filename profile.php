<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();

$db = Database::getInstance();
$pdo = $db->pdo;

if (isset($_POST["user_ID"]) && isset($_POST["iv"])){
    $iv = hex2bin($_POST["iv"]);
    $userid = Crypto::decrypt($_POST["user_ID"], $iv);
} else{
    $userid = Session::get("id");
}

if (isset($_POST["purpose"])){
    $purpose = $_POST["purpose"];
} else{
    $purpose = "edit";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update']) && Session::CheckPostID($_POST)) {
    $updateUser = $users->updateUserByIdInfo($_POST);
    if (isset($updateUser)) {
        echo $updateUser;
    }
    else{
        echo "updateUser is not set properly.";
    }
}

if (Session::get('roleid') == '1') {
    $homepage = "user_list";
} else {
    $homepage = "study_list";
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="float-left">User Profile</h3>
        <span class="float-right"><a href="<?php echo $homepage; ?>" class="backBtn btn btn-primary">Back</a></span>
    </div>
    <div class="card-body">
        <?php
            $getUinfo = $users->getUserInfoById($userid);
            if ($getUinfo->role_id == 1 && Session::get("id") != $getUinfo->user_id && $purpose === 'edit') {
                Session::CheckLogin();
            }
            if ($getUinfo){ ?>
                <div style="max-width:600px; margin:0px auto">
                  <form class="" action="" method="POST">
                        <?php 
                            $rand = bin2hex(openssl_random_pseudo_bytes(16));
                            Session::set("post_ID", $rand);
                        ?>
                        <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
                        <input type="hidden" name="user_ID" value="<?php echo Crypto::encrypt($userid, $iv);?>">
                        <input type="hidden" name="iv" value="<?php echo bin2hex($iv); ?>">
                  <?php if ($purpose === 'edit') { ?>
                      <div style="margin-block: 6px;">
                          <small style='color: red'>
                              * Required Field
                          </small>
                      </div>
                  <?php } ?>
                    <div class="form-group">
                      <label for="name" class="<?= $purpose === 'edit' ? 'required' : ''; ?>">Your Name</label>
                      <input type="text" name="name" value="<?php echo $getUinfo->name; ?>" <?= $purpose === "edit" ? "" : "disabled"?> class="form-control" required>
                    </div>
                    <div class="form-group">
                      <label for="phone_no">Mobile Number</label>
                      <input type="text" id="phone_no" name="mobile" value="<?php echo $getUinfo->mobile; ?>" <?= $purpose === "edit" ? "" : "disabled"?> class="form-control">
                    </div>
                    <?php $sql = "SELECT name
                                  FROM affiliation
                                  WHERE affiliation_id = " . $getUinfo->affiliation_id . "  
                                  LIMIT 1;";
                          $result = $pdo->query($sql);
                          if ($result){
                            $row = $result->fetch(PDO::FETCH_ASSOC);
                          }
                          else{
                            $row = array("Name" => "-");
                          } ?>
                    <div class="form-group">
                      <label for="affilation">Affilation: <?php echo $row["name"]; ?></label>
                    </div>
                    <?php if (Session::get("roleid") == '1' && $purpose === "edit" && Session::get("id") != $getUinfo->user_id) { ?>
                      <div class="form-group">
                        <div class="form-group">
                          <label for="roleid">Select user Role</label>
                          <select class="form-control" name="roleid" id="roleid">

                          <?php 
                            $sql = "SELECT id, role FROM user_roles WHERE id > 1 ORDER BY id ASC;";
                            $result = $pdo->query($sql);
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                                if ($getUinfo->role_id == $row["id"]){ ?>
                                <option value="<?php echo $row["id"]; ?>" selected><?php echo $row["role"]; ?></option>
                          <?php } 
                              else{ ?>  
                                <option value="<?php echo $row["id"]; ?>"><?php echo $row["role"]; ?></option>
                          <?php }
                            } ?>
                        </select>
                        </div>
                      </div>
                     <?php } ?>
                    <?php if ((Session::get("roleid") == '1' && $purpose === "edit") || (Session::get("id") == $getUinfo->user_id && $purpose === "edit")) { ?>
                    <div class="form-group">
                      <button type="submit" name="update" class="my-1 btn btn-success">Update</button>
                      <?php if (Session::get("id") == $getUinfo->user_id) { ?>
                        <a class="my-1 btn btn-primary" href="change_password">Password Change</a>
                        <?php } ?>
                    </div>
                    <?php } ?>
                  </form>
</div>
    <?php } ?>
    </div>
</div>
<script>
    $.fn.setCursorPosition = function(position){
        if(this.length == 0) return this;
        return $(this).setSelection(position, position);
    }
    
    $.fn.setSelection = function(selectionStart, selectionEnd) {
        if(this.length == 0) return this;
        var input = this[0];
    
        if (input.createTextRange) {
            var range = input.createTextRange();
            range.collapse(true);
            range.moveEnd('character', selectionEnd);
            range.moveStart('character', selectionStart);
            range.select();
        } else if (input.setSelectionRange) {
            input.focus();
            input.setSelectionRange(selectionStart, selectionEnd);
        }
    
        return this;
    }
    let lastInp = '';
    $(document).ready(() => {
        $('#phone_no').on('keyup', function () {
            const val_old = $(this).val();
            if (val_old === lastInp) return;
            const newString = new libphonenumber.AsYouType('US').input(val_old);
            const lastChar = val_old.charAt($(this)[0].selectionStart - 1);
            let newPos;
            if ([...'0123456789'].includes(lastChar)) {
                const count = (val_old.substring(0, $(this)[0].selectionStart).match(new RegExp(lastChar, 'g')) || []).length;
                newPos = -1;
                for (let i = 0; i < count; i++) {
                    newPos = newString.indexOf(lastChar, newPos + 1);
                }
                newPos++;
            } else {
                newPos = $(this)[0].selectionStart - Array.from(newString).reverse().findIndex(e => {
                    return Number.isInteger(+e);
                });
            }
            $(this).focus().val('').val(newString);
            $(this).setCursorPosition(newPos);
            lastInp = newString;
        });
    });
</script>
    

<?php
include 'inc/footer.php';

?>
