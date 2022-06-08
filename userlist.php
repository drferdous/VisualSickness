<?php
    include 'inc/header.php';
    include_once 'lib/Database.php';
    Session::CheckSession();
    
    $db = Database::getInstance();
    $pdo = $db->pdo;
    
    if (Session::get("roleid") !== "1"){
        header("Location: 404");
        exit();
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["removeUser"])){
        $sql = "DELETE FROM tbl_users
                WHERE id = " . $_POST["user_ID"] . ";";
        $result = $pdo->query($sql);
        if (!$result){
            echo $pdo->errorInfo();
            exit();
        }
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deactivateUser"])){
        $sql = "UPDATE tbl_users
                SET isActive = 0
                WHERE id = " . $_POST["user_ID"] . "
                LIMIT 1;";
        $result = $pdo->query($sql);
        if (!$result){
            echo $pdo->errorInfo();
            exit();
        }
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["activateUser"])){
        $sql = "UPDATE tbl_users
                SET isActive = 1
                WHERE id = " . $_POST["user_ID"] . "
                LIMIT 1;";
        $result = $pdo->query($sql);
        if (!$result){
            echo $pdo->errorInfo();
            exit();
        }
    }
?>

<div class="card ">
    <div class="card-header">
        <h3><i class="fas fa-users mr-2"></i>User list <span class="float-right">Welcome! <strong>
            <span class="badge badge-lg badge-secondary text-white">
            <?php
                $username = Session::get('username');
                if (isset($username)) {
                    echo $username;
                }
            ?>
            </span>
        </strong></span></h3>
    </div>
    <div class="card-body pr-2 pl-2">
        <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr class="text-center">
                    <th>SL</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email address</th>
                    <th>Mobile</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th width="25%">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $allUser = $users->selectAllUserData(false, Session::get("affiliationid"));
                    if ($allUser) {
                        $i = 0;
                        foreach ($allUser as $value) {
                            $i++;

                ?>
                            <tr class="text-center"
                            <?php if (Session::get("id") == $value->id) { ?>
                                style="background:#d9edf7"
                            <?php } ?>
                            >

                        <td><?php echo $i; ?></td>
                        <td><?php echo $value->name; ?></td>
                        <td><?php echo $value->username; ?> <br>
                          <?php if ($value->roleid  == '1'){ //admin
                          echo "<span class='badge badge-lg badge-info text-white'>Admin</span>";
                        } elseif ($value->roleid == '2') { // PI
                          echo "<span class='badge badge-lg badge-dark text-white'>Primary Investigator</span>";
                        }elseif ($value->roleid == '3') { // RA
                            echo "<span class='badge badge-lg badge-dark text-white'>Research Assistant</span>";
                        }elseif ($value->roleid == '4') { // only users!
                            echo "<span class='badge badge-lg badge-dark text-white'>User Only</span>";
                        } 
                        ?></td>
                        <td><?php echo $value->email; ?></td>
                        <td><span class="badge badge-lg badge-secondary text-white"><?php echo $value->mobile; ?></span></td>
                        <td>
                          <?php if ($value->isActive == '1') { ?>
                          <span class="badge badge-lg badge-info text-white">Active</span>
                        <?php }else{ ?>
                    <span class="badge badge-lg badge-danger text-white">Inactive</span>
                        <?php } ?>

                        </td>
                        <td><span class="badge badge-lg badge-secondary text-white"><?php echo Util::formatDate($value->created_at);  ?></span></td>

                        <td>
                          <?php if ( Session::get("roleid") == '1') {?>
                            <a class="btn btn-warning btn-sm profilePage" 
                               href="profile"
                               data-user_ID="<?php echo $value->id; ?>"
                               data-purpose="view">
                               View
                            </a>
                        <?php if ($value->roleid === '1' && $value->id !== Session::get('id')){ ?>
                            <!--
                                <a class="btn btn-info btn-sm disabled" 
                                   href="javascript:void(0);">
                                   Edit
                                </a>   
                            -->
                        <?php }
                              else{ ?>
                                <a class="btn btn-info btn-sm profilePage" 
                                   href="profile"
                                   data-user_ID="<?php echo $value->id;?>"
                                   data-purpose="edit">
                                   Edit
                                </a>
                        <?php } 
                              if ($value->roleid === '1' || Session::get("id") == $value->id){ ?>
                                <a class="btn btn-danger btn-sm disabled"
                                   href="javascript:void(0);">
                                Remove
                                </a>
                        <?php }
                              else{ ?>
                                <a class="btn btn-danger btn-sm userAction removeUser"
                                   href="userlist"
                                   data-user_ID="<?php echo $value->id; ?>">
                                Remove    
                                </a>
                        <?php }
                              if ($value->isActive == '1'){ ?> 
                               <a class="btn btn-outline-dark userAction deactivateUser
                       <?php if ($value->roleid ==='1' || Session::get("id") == $value->id) {
                         echo "disabled";
                       } ?>
                                btn-sm " href="userlist" data-user_ID="<?php echo $value->id; ?>">Deactivate</a>
                        <?php }elseif ($value->isActive == '0'){?>
                            <a class="btn btn-success userAction activateUser
                       <?php if ($value->roleid === '1' || Session::get("id") == $value->id) {
                         echo "disabled";
                       } ?>
                                btn-sm " href="userlist" data-user_ID="<?php echo $value->id; ?>">Activate</a>
                             <?php } ?>
                            
                        <?php if ($value->reg_stat === "1"){ ?>
                            <a class="btn btn-sm btn-success"
                               id="validateUser"
                               href="validateUser"
                               data-user_ID="<?php echo $value->id; ?>">
                               Validate
                            </a>
                        <?php } ?>




                        <?php  } ?>
                        <?php } ?>

                        </td>
                      </tr>
                    <?php }else{ ?>
                      <tr class="text-center">
                      <td>No user available now!</td>
                    </tr>
                    <?php } ?>

                  </tbody>
              </table>
            <div class="form-check form-switch float-right" style="margin-left: 2rem">
                <input class="form-check-input" type="checkbox" id="show-deactivated-users" unchecked>
                <label class="form-check-label" for="show-deactivated-users">Show Deactivated Users Only</label>
            </div>
            <div class="form-check form-switch float-right">
                <input class="form-check-input" type="checkbox" id="show-pending-users" unchecked>
                <label class="form-check-label" for="show-pending-users">Show Users Pending Admin Approval Only</label>
            </div>
        </div>
      </div>


<script type="text/javascript">
    $(document).ready(function() {
        $("#example a.profilePage[data-user_ID]").on("click", goToProfilePage);
        $("#example a.userAction[data-user_ID]").on("click", doUserAction);
        $("#validateUser").on("click", validateUser);
        $("#show-pending-users").on("click", showPendingUsers);
        $("#show-deactivated-users").on("click", showDeactivatedUsers);
    });
    
    function goToProfilePage(){
        let form = document.createElement("form");
        let hiddenInput;
        
        form.setAttribute("method", "POST");
        form.setAttribute("action", $(this).attr("href"));
        form.setAttribute("style", "display: none");
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "user_ID");
        hiddenInput.setAttribute("value", $(this).attr("data-user_ID"));
        form.appendChild(hiddenInput);
        
        if ($(this).get(0).hasAttribute("data-purpose")){
            hiddenInput = document.createElement("input");
            hiddenInput.setAttribute("type", "hidden");
            hiddenInput.setAttribute("name", "purpose");
            hiddenInput.setAttribute("value", $(this).attr("data-purpose"));
            form.appendChild(hiddenInput);
        }
             
        document.body.appendChild(form);
        form.submit(); 
        
        return false;
    };
    
    function validateUser(){
        let form = document.createElement("form");
        let hiddenInput;
        
        form.setAttribute("method", "POST");
        form.setAttribute("action", $(this).attr("href"));
        form.setAttribute("style", "display: none");
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "user_ID");
        hiddenInput.setAttribute("value", $(this).attr("data-user_ID"));
        form.appendChild(hiddenInput);
        
        document.body.appendChild(form);
        form.submit();
        
        return false;
    }
    
    function showPendingUsers(){
        $.ajax({
            url: "loadCorrectUsers",
            method: "POST",
            cache: false,
            data:{
                showPendingUsers: $(this).prop("checked")
            },
            success: function(data){
                $("#example").html(data);
                $("#validateUser").on("click", validateUser);
                $("#show-pending-users").on("click", showPendingUsers);
                $("#show-deactivated-users").on("click", showDeactivatedUsers);
                updateTable();
            }
        });
    }
    
    function showDeactivatedUsers(){
        $.ajax({
            url: "loadCorrectUsers",
            method: "POST",
            cache: false,
            data:{
                showDeactivatedUsers: $(this).prop("checked")
            },
            success: function(data){
                $("#example").html(data);
                $("#validateUser").on("click", validateUser);
                $("#show-pending-users").on("click", showPendingUsers);
                $("#show-deactivated-users").on("click", showActivatedUsers);
                updateTable();
            }
        });
    }
    
    function doUserAction(){
        let userAction;
        if ($(this).hasClass("removeUser")){
            userAction = "removeUser";
            if (!confirm("Are you sure you want to remove this user?")){
                return false;
            }
        }
        if ($(this).hasClass("deactivateUser")){
            userAction = "deactivateUser";
            if (!confirm("Are you sure you want to deactivate this user?")){
                return false;
            }
        }
        if ($(this).hasClass("activateUser")){
            userAction = "activateUser";
            if (!confirm("Are you sure you want to activate this user?")){
                return false;
            }
        }

        let form = document.createElement("form");
        let hiddenInput;
        
        form.setAttribute("method", "POST");
        form.setAttribute("action", $(this).attr("href"));
        form.setAttribute("style", "display: none");
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "user_ID");
        hiddenInput.setAttribute("value", $(this).attr("data-user_ID"));
        form.appendChild(hiddenInput);
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", userAction);
        form.appendChild(hiddenInput);
        
        document.body.appendChild(form);
        form.submit();
        
        return false;
    }
</script>
<?php
    include 'inc/footer.php';
?>
