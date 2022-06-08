<?php 

    /*
    include 'inc/header.php';
    include_once 'lib/Database.php';
    Session::CheckSession();
    
    $db = Database::getInstance();
    $pdo = $db->pdo;*/

    if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST)){
        // header("Location: 404");
        var_dump($_POST);
        exit();
    }
    
    include "database.php";
    include "lib/Session.php";
    include "classes/Users.php";
    
    Session::init();
    
    $showPendingUsers = filter_var($_POST['showPendingUsers'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    $users = new Users();
    
    // my edit
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
    // ends here
?>

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
                    $allUser = $users->selectAllUserData($showPendingUsers, Session::get("affiliationid"));
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
                            <a class="btn btn-warning btn-sm" 
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
                            <!--
                                <a class="btn btn-info btn-sm" 
                                   href="profile"
                                   data-user_ID="<?//php echo $value->id;?>"
                                   data-purpose="edit">
                                   Edit
                                </a>
                            -->
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
                                <a class="btn btn-danger btn-sm"
                                   href="userlist"
                                   onclick="return confirm('Are you sure To Deactive ?')"
                                   data-user_ID="<?php echo $value->id; ?>">
                                Remove    
                                </a>
                        <?php }
                              if ($value->isActive == '1'){ ?> 
                               <a onclick="return confirm('Are you sure To Deactive ?')" class="btn btn-outline-dark
                       <?php if ($value->roleid ==='1' || Session::get("id") == $value->id) {
                         echo "disabled";
                       } ?>
                                btn-sm " href="userlist" data-user_ID="<?php echo $value->id; ?>">Deactivate</a>
                        <?php }elseif ($value->isActive == '0'){?>
                            <a onclick="return confirm('Are you sure To Active ?')" class="btn btn-success
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




                        <?php } ?>
                        <?php } ?>

                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
              </table>

          
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
</script>
<?php
    include 'inc/footer.php';
?>