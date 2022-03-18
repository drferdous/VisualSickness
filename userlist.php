<?php
    include 'inc/header.php';
    Session::CheckSession();
    
    if (strcmp(Session::get("roleid"), "1") !== 0){
        header("Location: 404");
        exit();
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
 ?></span>

          </strong></span></h3>
        </div>
        <div class="card-body pr-2 pl-2">

          <table id="example" class="table table-striped table-bordered" style="width:100%">
                  <thead>
                    <tr>
                      <th  class="text-center">SL</th>
                      <th  class="text-center">Name</th>
                      <th  class="text-center">Username</th>
                      <th  class="text-center">Email address</th>
                      <th  class="text-center">Mobile</th>
                      <th  class="text-center">Status</th>
                      <th  class="text-center">Created</th>
                      <th  width='25%' class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php

                      $allUser = $users->selectAllUserData();

                      if ($allUser) {
                        $i = 0;
                        foreach ($allUser as  $value) {
                          $i++;

                     ?>

                      <tr class="text-center"
                      <?php if (Session::get("id") == $value->id) {
                        echo "style='background:#d9edf7' ";
                      } ?>
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
                        <td><span class="badge badge-lg badge-secondary text-white"><?php echo $users->formatDate($value->created_at);  ?></span></td>

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
                            
                        <?php if (strcmp($value->reg_stat, "0") === 0){ ?>
                            <a class="btn btn-sm btn-success"
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









        </div>
      </div>


<script type="text/javascript">
    $(document).ready(function() {
       $("#example a[data-user_ID]").on("click", goToProfilePage);
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
