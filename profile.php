<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();

$db = Database::getInstance();
$pdo = $db->pdo;

$userid = $_POST['user_ID'];
$purpose = $_POST['purpose'];

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
            if ($getUinfo !== FALSE){ ?>
                <div style="width:600px; margin:0px auto">
                <form class="" action="" method="POST">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" name="name" value="<?php echo $getUinfo->name; ?>" <?= $purpose === "edit" ? "" : "disabled"?> class="form-control">
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
                  <label for="sel1">Select user Role</label>
                  <select class="form-control" name="roleid" id="roleid">

                  <?php

                if($getUinfo->roleid == '1'){?>
                  <option value="1" selected='selected'>Admin</option>
                  <option value="2">Primary Investigator</option>
                  <option value="3">Research Assistant</option>
                  <option value="3">User only</option>
                <?php }elseif($getUinfo->roleid == '2'){?>
                  <option value="1">Admin</option>
                  <option value="2" selected='selected'>Primary Investigator</option>
                  <option value="3">Research Assistant</option>
                  <option value="3">User only</option>
                <?php }elseif($getUinfo->roleid == '3'){?>
                  <option value="1">Admin</option>
                  <option value="2">Primary Investigator</option>
                  <option value="3" selected='selected'>Research Assistant</option>
                  <option value="3">User only</option>
                <?php }elseif($getUinfo->roleid == '4'){?>  
                  <option value="1">Admin</option>
                  <option value="2">Primary Investigator</option>
                  <option value="3">Research Assistant</option>
                  <option value="4" selected='selected'>User only</option>                
                <?php } ?>


                  </select>
                </div>
              </div>

          <?php }else{?>
            <input type="hidden" name="roleid" value="<?php echo $getUinfo->roleid; ?>">
          <?php } ?>

              <?php if (Session::get("id") == $getUinfo->id && $purpose === "edit") {?>


              <div class="form-group">
                <button type="submit" name="update" class="btn btn-success">Update</button>
                <a class="btn btn-primary" href="changepass" data-user_ID="<?php echo $getUinfo->id; ?>">Password change</a>
              </div>
            <?php } elseif(Session::get("roleid") == '1' && $purpose === "edit") {?>


              <div class="form-group">
                <button type="submit" name="update" class="btn btn-success">Update</button>
                <a class="btn btn-primary" href="changepass" data-user-ID="<?php echo $getUinfo->id; ?>">Password change</a>
              </div>
            <?php } elseif(Session::get("roleid") == '2' && $purpose === "edit") {?>


              <div class="form-group">
                <button type="submit" name="update" class="btn btn-success">Update</button>
              </div>

              <?php } ?>
          </form>
        </div>

      <?php }
       else{

        header('Location: index');
      } ?>



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
