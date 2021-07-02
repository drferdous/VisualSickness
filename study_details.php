<?php
include 'inc/header.php';
include_once 'database.php';

Session::CheckSession();

if (isset($_POST['deactivate-btn'])){
    $studyDeactivatedMessage = $users->deactivateStudy($_GET["study_ID"]);
    if (isset($studyDeactivatedMessage)){
        echo $studyDeactivatedMessage;
    }
}

if (isset($_POST['initialize-session-btn'])){
    $successMessage = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Success!</strong> You created a session!</div>';
    
    $sessionInitializedMessage = $users->initializeNewSession($_GET["study_ID"]);
    if (isset($sessionInitializedMessage)){
        if ($sessionInitializedMessage === $successMessage){
            echo $sessionInitializedMessage;
            header("Location: ./create_session.php");
        }
        else{
            echo $sessionInitializedMessage;
        }
    }
}

if (isset($_POST['leave-btn'])){
    $successMessage = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Success!</strong> You left this study!</div>';
    $leaveStudyMessage = $users->leaveStudy($_GET["study_ID"]);
    
    if (isset($leaveStudyMessage)){
        if ($leaveStudyMessage === $successMessage){
            echo $leaveStudyMessage;
            header("Location: ./view_study.php");
        }
        else{
            echo $leaveStudyMessage;
        }
    }
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
                $sql_study = "SELECT * FROM Study WHERE study_ID = " . $_GET["study_ID"] . " LIMIT 1;";
                $result_study = mysqli_query($conn, $sql_study);
                $row_study = mysqli_fetch_assoc($result_study);
                echo "<td>" . $row_study['full_name']  . "</td>";
                echo "<td>" . $row_study['short_name'] . "</td>";
                echo "<td>" . $row_study['IRB']        . "</td>";
                    
                if (isset($row_study['created_by'])){
                    $sql_users = "SELECT name FROM tbl_users WHERE id = " . $row_study['created_by'] . " LIMIT 1;";
                    $result_users = mysqli_query($conn, $sql_users);
                    $row_users = mysqli_fetch_assoc($result_users);
                        
                    echo "<td>" . $row_users['name'] . "</td>";
                }
                else{
                    echo "<td>-</td>";
                }
                    
                echo "<td>" . $row_study['created_at']     . "</td>";
                echo "<td>" . $row_study['last_edited_at'] . "</td>";
                    
                if (isset($row_study['last_edited_by'])){
                    $sql_users = "SELECT name FROM tbl_users WHERE id = " . $row_study['last_edited_by'] . " LIMIT 1;";
                    $result_users = mysqli_query($conn, $sql_users);
                    $row_users = mysqli_fetch_assoc($result_users);
                    echo "<td>" . $row_users['name'] . "</td>";
                }
                else{
                    echo "<td>-</td>";
                }
                    
                echo "<td>";
                if (Session::get('roleid') === '1' || Session::get('roleid') === '2'){
                    echo "<ul>";
                        
                    echo "<li>";
                    echo "<form method=\"post\">";
                    echo "<input type=\"submit\" name=\"deactivate-btn\" value=\"Deactivate\" />";
                    echo "</form>";
                    echo "</li>";
                    
                    echo "<li>";
                    echo "<form method=\"post\">";
                    echo "<input type=\"submit\" name=\"initialize-session-btn\" value=\"Create Session\" />";
                    echo "</form>";
                    echo "</li>";
                        
                    echo "<li>";
                    echo "<a href=\"edit_study.php?id=". $row_study['study_ID'] ."\">Edit</a>";
                    echo "</li>";
                    
                    echo "</ul>";
                }
                else if (Session::get('roleid') === '3' || Session::get('roleid') === '4'){
                    echo "<form method=\"post\">";
                    echo "<input type=\"submit\" name=\"leave-btn\" value=\"Leave\" />";
                    echo "</form>";
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