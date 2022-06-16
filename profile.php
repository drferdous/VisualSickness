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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $updateUser = $users->updateUserByIdInfo($userid, $_POST);
    if (isset($updateUser)) {
        echo $updateUser;
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3>User Profile <span class="float-right"><a href="index" class="btn btn-primary" data-user_ID="0">Back</a></span></h3>
    </div>
    <div class="card-body">
        <?php
            $getUinfo = $users->getUserInfoById($userid);
            if ($getUinfo){ ?>
                <div style="width:600px; margin:0px auto">
                  <form class="" action="profile" method="POST">
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
                      <label for="mobile">Mobile Number</label>
                      <input type="text" id="mobile" name="mobile" value="<?php echo $getUinfo->mobile; ?>" <?= $purpose === "edit" ? "" : "disabled"?> class="form-control">
                    </div>
                    <?php $sql = "SELECT Name
                                  FROM Affiliation
                                  WHERE id = " . $getUinfo->affiliationid . "  
                                  LIMIT 1;";
                          $result = $pdo->query($sql);
                          if ($result){
                            $row = $result->fetch(PDO::FETCH_ASSOC);
                          }
                          else{
                            $row = array("Name" => "-");
                          } ?>
                    <div class="form-group">
                      <label for="affilation">Affilation: <?php echo $row["Name"]; ?></label>
                    </div>
                    <?php if (Session::get("roleid") == '1' && $purpose === "edit") { ?>

                      <div class="form-group
                      <?php if (Session::get("roleid") == '1' && Session::get("id") == $getUinfo->id) {
                        echo "d-none";
                            } ?>
                      ">
                        <div class="form-group">
                          <label for="roleid">Select user Role</label>
                          <select class="form-control" name="roleid" id="roleid">

                          <?php 
                            $sql = "SELECT role FROM tbl_roles ORDER BY id ASC;";
                            $result = $pdo->query($sql);
                            for ($i = 1; $i <= $result->rowCount(); ++$i){
                              $row = $result->fetch(PDO::FETCH_ASSOC);
                              if (Session::get("roleid") == $i){ ?>
                                <option value="<?php echo $i; ?>" selected><?php echo $row["role"]; ?></option>
                        <?php } 
                              else{ ?>  
                                <option value="<?php echo $i; ?>"><?php echo $row["role"]; ?></option>
                        <?php } ?>
                     <?php } ?>
                        </select>
                        </div>
                      </div>
                     <?php } ?>
                        <!-- </div>
                      </div> -->
                      
                    <div class="form-group">
                      <button type="submit" name="update" class="btn btn-success">Update</button>
                      <a class="btn btn-primary" href="changepass" data-user-ID="<?php echo $getUinfo->id; ?>">Password change</a>
                    </div>
                  </form>
</div>
      <?php } ?>
      </div>
    </div>
    
<script type="text/javascript">
    $(document).ready(function() {
        $(".card").on("click", "a", redirectUser);
    });
    
    function redirectUser(){
        let form = document.createElement("form");
        let hiddenInput = document.createElement("input");
        
        form.setAttribute("method", "POST");
        form.setAttribute("action", $(this).attr("href"));
        form.setAttribute("style", "display: none");
        
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "user_ID");
        hiddenInput.setAttribute("value", $(this).attr("data-user_ID"));
        
        form.appendChild(hiddenInput);
        document.body.appendChild(form);
        form.submit();
        
        return false;
    };
</script>

  <?php
  include 'inc/footer.php';

  ?>
