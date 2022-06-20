<?php
include 'inc/header.php';
include_once 'lib/Database.php';

$db = Database::getInstance();
$pdo = $db->pdo;
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
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
                  <label for="mobile">Phone Number</label>
                  <input type="tel" name="mobile" pattern="\d*" title="Only numbers allowed" class="form-control" id="mobile">
                  <small>Format: 123-456-7890, don't type the hyphens!</small>
                <div class="form-group">
                  <div class="form-group">
                    <label for="sel1" class="required">Select User Role</label>
                    <select class="form-control" name="roleid" id="sel1" required>
                      <?php 
                          $sql = "SELECT id, role FROM tbl_roles WHERE id > 1;";
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
                          $sql = "SELECT id, name FROM Affiliation;";
                          $result = $pdo->query($sql);
                          while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
                              <option <?= Util::getValueFromPost('affiliationid', $_POST) == $row['id'] ? 'selected' : ''; ?> value='<?= $row['id'] ?>'><?= $row['name'] ?></option>
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
      
<?php
  include 'inc/footer.php';
?>
