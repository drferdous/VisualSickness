<?php
    include 'inc/header.php';
    Session::CheckSession();
?>
<?php

    $userid = intval($_POST["user_ID"]);

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changepass'])) {
        $changePass = $users->changePasswordBysingelUserId($userid, $_POST);
    }



    if (isset( $changePass)) {
        echo  $changePass;
    }
?>


 <div class="card">
    <div class="card-header">
        <h3>Change your password 
          <a href="profile" class="btn btn-primary float-right">Back</a> 
        </h3>
    </div>
        <div class="card-body">



          <div style="width:600px; margin:0px auto">

          <form class="" action="" method="POST">
              <div class="form-group">
                <label for="old_password" class="required">Old Password</label>
                <input type="password" name="old_password"  class="form-control">
              </div>
              <div class="form-group">
                <label for="new_password" class="required">New Password</label>
                <input type="password" name="new_password"  class="form-control">
              </div>
              <input type="hidden" name="user_ID" value="<?php echo $userid; ?>">    

              <div class="form-group">
                <button type="submit" name="changepass" class="btn btn-success">Change password</button>
              </div>


          </form>
        </div>


      </div>
    </div>

<script type="text/javascript">
    $(document).ready(function(){
        $(".card a").on("click", redirectUser);
    });
    
    function redirectUser(){
        let form = document.createElement("form");
        let hiddenInput;
        
        form.setAttribute("method", "POST");
        form.setAttribute("action", $(this).attr("href"));
        form.setAttribute("style", "display: none");
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "user_ID");
        hiddenInput.setAttribute("value", "<?php echo $userid; ?>");
        form.appendChild(hiddenInput);
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "purpose");
        hiddenInput.setAttribute("value", "edit");
        form.appendChild(hiddenInput);
        
        document.body.appendChild(form);
        form.submit();
        
        return false;
    }
</script>


<?php
  include 'inc/footer.php';
?>