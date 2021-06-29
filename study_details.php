<?php
include 'inc/header.php';
include_once 'database.php';

Session::CheckSession();

$logMsg = Session::get('logMsg');
if (isset($logMsg)) {
  echo $logMsg;
}
$msg = Session::get('msg');
if (isset($msg)) {
  echo $msg;
}
Session::set("msg", NULL);
Session::set("logMsg", NULL);

if (isset($_GET['remove'])) {
  $remove = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['remove']);
  $removeUser = $users->deleteUserById($remove);
}

if (isset($removeUser)) {
  echo $removeUser;
}


if (isset($_GET['deactive'])) {
  $deactive = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['deactive']);
  $deactiveId = $users->userDeactiveByAdmin($deactive);
}

if (isset($deactiveId)) {
  echo $deactiveId;
}
if (isset($_GET['active'])) {
  $active = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['active']);
  $activeId = $users->userActiveByAdmin($active);
}

if (isset($activeId)) {
  echo $activeId;
}
?>

<div class="card">
    <div class="card-header">
        <h3><span class="float-right">Welcome!
            <strong><span class="badge badge-lg badge-secondary text-white">
                    <?php 
                        $username = Session::get('username');
                        if (isset($username)){
                            echo $username;
                        }
                    ?>
            </span></strong>
        </span></h3>
    </div>

    <div class="card-body pr-2 pl-2">
        <table class="table table-striped table-bordered">
            <thead class="text-center">
                <tr>
                    <th>Full Name</th>
                    <th>Short Name</th>
                    <th>IRB</th>
                    <th>Created By</th>
                    <th>Time Created</th>
                    <th>Time of Last Edit</th>
                    <th>Last Edited By</th>
                    <th>Action</th>
                </tr>
            </thead>
            
            <tbody>
                <tr>
                <?php
                    $sql_study = "SELECT * FROM Study WHERE study_ID = " . $_GET["id"] . " LIMIT 1;";
                    $result_study = mysqli_query($conn, $sql_study);
                    $row_study = mysqli_fetch_assoc($result_study);
                    
                    echo "<td>" . $row_study['full_name']      . "</td>";
                    echo "<td>" . $row_study['short_name']     . "</td>";
                    echo "<td>" . $row_study['IRB']            . "</td>";
                    
                    if (isset($row_study['created_by'])){
                        $sql_users = "SELECT username FROM tbl_users WHERE id = " . $row_study['created_by'] . " LIMIT 1;";
                        $result_users = mysqli_query($conn, $sql_users);
                        $row_users = mysqli_fetch_assoc($result_users);
                        echo "<td>" . $row_users['username'] . "</td>";
                    }
                    else{
                        echo "<td>-</td>";
                    }
                    
                    echo "<td>" . $row_study['created_at']     . "</td>";
                    echo "<td>" . $row_study['last_edited_at'] . "</td>";
                    
                    if (isset($row_study['last_edited_by'])){
                        $sql_users = "SELECT username FROM tbl_users WHERE id = " . $row_study['last_edited_by'] . " LIMIT 1;";
                        $result_users = mysqli_query($conn, $sql_users);
                        $row_users = mysqli_fetch_assoc($result_users);
                        echo "<td>" . $row_users['username'] . "</td>";
                    }
                    else{
                        echo "<td>-</td>";
                    }
                    
                    echo "<td>";
                    if (Session::get('roleid') === '1' || Session::get('roleid') === '2'){
                        echo "<ul>";
                        echo "<li><a href=\"delete_study.php?id=". $row_study['study_ID'] . "\">Deactivate</a></li>";
                        echo "<li><a href=\"edit_study.php?id=". $row_study['study_ID'] ."\">Edit</a></li>";
                        echo "</ul>";
                    }
                    else if (Session::get('roleid') === '3' || Session::get('roleid') === '4'){
                        echo "<a href=\leave_study.php?id=" . $row['study_ID'] . "\">Leave</a>";
                    }
                    echo "</td>";
                ?>
                </tr>
            </tbody>
            
        </table>
    </div>
</div>

<?php
  include 'inc/footer.php';
?>