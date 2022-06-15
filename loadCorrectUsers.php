<?php 

    include_once "lib/Database.php";
    include "lib/Session.php";
    include "classes/Users.php";
    include "classes/Crypto.php";
    
    $pdo = Database::getInstance()->pdo;
    
    Session::init();

    if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST)){
        // header("Location: 404");
        var_dump($_POST);
        exit();
    }
    
    
    $showPendingUsers = filter_var($_POST['showPendingUsers'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    $showDeactivatedUsers = filter_var($_POST['showDeactivatedUsers'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    $users = new Users();
    
?>

            <thead>
                <tr class="text-center">
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email address</th>
                    <th>Mobile</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th width="25%">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $allUser = $users->selectAllUserData($showPendingUsers, $showDeactivatedUsers, Session::get("affiliationid"));
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
                        <td><?php echo $value->name; ?>
                        <br>
                        <?php if ($value->roleid  == '1'){ //admin ?>
                                <span class='badge badge-lg badge-info text-white'>Admin</span>
                        <?php } 
                              elseif ($value->roleid == '2') { // PI ?>
                                <span class='badge badge-lg badge-dark text-white'>Primary Investigator</span>
                        <?php }
                              elseif ($value->roleid == '3') { // RA ?>
                                <span class='badge badge-lg badge-dark text-white'>Research Assistant</span>
                        <?php }
                              elseif ($value->roleid == '4') { // only users! ?>
                                <span class='badge badge-lg badge-dark text-white'>User Only</span>
                        <?php } ?>
                        </td>
                        <td><?php echo $value->email; ?></td>
                        <td><span class="badge badge-lg badge-secondary text-white"><?php echo $value->mobile; ?></span></td>
                        <td>
                        <?php if ($value->isActive == 0) { ?>
                            <span class="badge badge-lg badge-danger text-white">Inactive</span>
                        <?php } else if ($value->reg_stat == 0) { ?>
                            <span class="badge badge-lg badge-secondary text-white">Pending Email</span>
                        <?php } else if ($value->reg_stat == 1) { ?>
                            <span class="badge badge-lg badge-warning text-black">Pending Validation</span>
                        <?php } else { ?>
                            <span class="badge badge-lg badge-info text-white">Active</span>
                        <?php } ?>

                        </td>
                        <td><span class="badge badge-lg badge-secondary text-white"><?php echo Util::formatDate($value->created_at);  ?></span></td>

                        <td data-user_ID="<?php echo Crypto::encrypt($value->id, $iv); ?>"
                            data-iv="<?php echo bin2hex($iv); ?>">
                            <div>
                          <?php if ( Session::get("roleid") == '1') {?>
                            <a class="btn btn-warning btn-sm profilePage" 
                                href="profile"
                                data-purpose="view" style="margin-bottom: 4px;">
                                View
                            </a>
                        <?php if ($value->roleid === '1' && $value->id !== Session::get('id')){ ?>
                                <a class="btn btn-info btn-sm disabled"
                                    href="javascript:void(0);" style="margin-bottom: 4px;">
                                    Edit
                                </a> 
                        <?php }
                              else{ ?>
                                <a class="btn btn-info btn-sm profilePage" 
                                    href="profile"
                                    data-purpose="edit" style="margin-bottom: 4px;">
                                    Edit
                                </a>
                        <?php } ?></div><div><?php
                              if ($value->roleid === '1' || Session::get("id") == $value->id){ ?>
                                <a class="btn btn-danger btn-sm disabled"
                                   href="javascript:void(0);" style="margin-bottom: 4px;">
                                Remove
                                </a>
                        <?php }
                              else{ ?>
                                <a class="btn btn-danger btn-sm userAction removeUser"
                                   href="userlist"
                                   style="margin-bottom: 4px;">
                                Remove
                                </a>
                        <?php }
                              if ($value->isActive == '1'){ ?> 
                               <a class="btn btn-outline-dark userAction deactivateUser
                       <?php if ($value->roleid ==='1' || Session::get("id") == $value->id) {
                         echo "disabled";
                       } ?>
                                btn-sm " href="userlist" style="margin-bottom: 4px;" ?>Deactivate</a>
                        <?php }elseif ($value->isActive == '0'){?>
                            <a class="btn btn-success btn-sm userAction activateUser 
                       <?php if ($value->roleid === '1' || Session::get("id") == $value->id) {
                         echo "disabled ";
                       } ?>
                                btn-sm" href="userlist" style="margin-bottom: 4px;">Activate</a>
                             <?php } ?>
                            
                        <?php if ($value->reg_stat === "1"){ ?>
                            <a class="btn btn-sm btn-success"
                               id="validateUser"
                               href="validateUser" style="margin-bottom: 4px;">
                               Validate
                            </a>
                        <?php } ?>




                        <?php } ?>
                        <?php } ?>

                        </td>
                      </tr>
                    <?php } else { ?>
                            <td colspan="100%" class="text-center notFound">
                                No users found
                            </td>
                    <?php } ?>
                  </tbody>
