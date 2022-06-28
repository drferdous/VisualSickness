<?php
include 'inc/header.php';
Session::CheckLogin();
include_once 'lib/Database.php';

$db = Database::getInstance();
$pdo = $db->pdo;
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register']) && Session::CheckPostID($_POST)) {
    $userRegistration = $users->userRegistration($_POST);
}
if (isset($userRegistration)) {
    echo $userRegistration;?>
    <script type="text/javascript">
        const divMsg = document.getElementById("flash-msg");
        if (divMsg.classList.contains("alert-success")){
            setTimeout(function(){
                redirect('login', {})
            }, 1000);
        }
    </script>
<?php } ?>


 <div class="card">
   <div class="card-header">
          <h3 class='text-center'>User Registration (All Fields Required)</h3>
          <p class='text-center'>After registering, check your email for a link to create a password.</p>
        </div>
        <div class="card-body">

            <div style="max-width:600px; margin:0px auto">

            <form class="" action="" method="post">
                <?php 
                    $rand = bin2hex(openssl_random_pseudo_bytes(16));
                    Session::set("post_ID", $rand);
                ?>
                <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
                <br>
                <div style="margin-block: 6px;">
                    <small style='color: red'>
                        * Required Field
                    </small>
                </div>
                <div class="form-group pt-3">
                  <label for="name" class="required">Your name</label>
                  <input type="text" name="name" value="<?= Util::getValueFromPost('name', $_POST); ?>" class="form-control" id="name" required>
                </div>
                <div class="form-group">
                  <label for="email" class="required">Email address</label>
                  <input type="email" name="email" value="<?= Util::getValueFromPost('email', $_POST); ?>" class="form-control" id="email" required>
                </div>
                <div class="form-group">
                  <label for="phone_no">Phone Number</label>
                  <input type="tel" name="mobile" class="form-control" id="phone_no">
                </div>
                <div class="form-group">
                  <div class="form-group">
                    <label for="sel1" class="required">Select User Role</label>
                    <select class="form-control" name="roleid" id="sel1" required>
                      <?php 
                          $sql = "SELECT id, role FROM user_roles WHERE id > 1;";
                          $result = $pdo->query($sql);
                          while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
                            <option <?= Util::getValueFromPost('roleid', $_POST) == $row['id'] ? 'selected' : ''; ?> value='<?= $row['id'] ?>'><?= $row['role'] ?></option>
                          <?php }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-group">
                    <label for="selAffil" class="required">Select Affiliation</label>
                    <select class="form-control" name="affiliationid" id="selAffil" required>
                      <?php 
                          $sql = "SELECT affiliation_id, name FROM affiliation;";
                          $result = $pdo->query($sql);
                          while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
                              <option <?= Util::getValueFromPost('affiliationid', $_POST) == $row['affiliation_id'] ? 'selected' : ''; ?> value='<?= $row['affiliation_id'] ?>'><?= $row['name'] ?></option>
                          <?php }
                      ?>
                    </select>
                  </div>
                </div> 
                <div class="form-group">
                  <button type="submit" name="register" class="btn btn-success">Register</button>
                </div>
            </form>
          </div>

        </div>
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
                console.log(lastChar, count, newPos);
            } else {
                newPos = $(this)[0].selectionStart - Array.from(newString).reverse().findIndex(e => {
                    console.log(+e);
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
