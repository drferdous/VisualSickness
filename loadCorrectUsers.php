<?php 
    if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST)){
        // header("Location: 404");
        var_dump($_POST);
        exit();
    }
    
    include "database.php";
    include "lib/Session.php";
    include "classes/Users.php";
    
    Session::init();
    
    $showPendingUsers = filter_var(
                            $_POST["showPendingUsers"],
                            FILTER_VALIDATE_BOOLEAN,
                            FILTER_NULL_ON_FAILURE
                        );
    $users = new Users();
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
                    <span class="badge badge-lg badge-danger text-white">Deactive</span>
                        <?php } ?>

                        </td>
                        <td><span class="badge badge-lg badge-secondary text-white"><?php echo Util::formatDate($value->created_at);  ?></span></td>

                        <td>
                          <?php if ( Session::get("roleid") == '1') {?>
                            <a class="btn btn-success btn-sm" 
                               href="profile"
                               data-user_ID="<?php echo $value->id; ?>"
                               data-purpose="view">
                               View
                            </a>
                        <?php if ($value->roleid === '1' && $value->id !== Session::get('id')){ ?>
                                <a class="btn btn-info btn-sm disabled" 
                                   href="javascript:void(0);">
                                   Edit
                                </a>   
                        <?php }
                              else{ ?>
                                <a class="btn btn-info btn-sm" 
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
                               <a onclick="return confirm('Are you sure To Deactive ?')" class="btn btn-warning
                       <?php if ($value->roleid ==='1' || Session::get("id") == $value->id) {
                         echo "disabled";
                       } ?>
                                btn-sm " href="userlist" data-user_ID="<?php echo $value->id; ?>">Deactivate</a>
                        <?php }elseif ($value->isActive == '0'){?>
                            <a onclick="return confirm('Are you sure To Active ?')" class="btn btn-secondary
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