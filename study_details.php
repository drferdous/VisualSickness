<?php
include 'inc/header.php';
include_once 'database.php';

Session::CheckSession();

if (isset($_POST['deactivate-btn'])){
    $studyDeactivatedMessage = $users->deactivateStudy($_POST["study_ID"]);
    if (isset($studyDeactivatedMessage)){
        echo $studyDeactivatedMessage; ?>
        
        <script type="text/javascript">
            const divMsg = document.getElementById("flash-msg");
            if (divMsg.classList.contains("alert-success")){
                setTimeout(function() {
                    location.href = "view_study.php";
                }, 2000);
            }
        </script>
<?php }
}

if (isset($_POST['leave-btn'])){
    $successMessage = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Success!</strong> You left this study!</div>';
    $leaveStudyMessage = $users->leaveStudy($_POST["study_ID"]);
    
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
        <h3>Study Details <span class="float-right"> <a href="view_study.php" class="btn btn-primary">Back</a></span></h3>
    </div>

    <div class="card-body pr-2 pl-2">
        <table class="table table-striped table-bordered">
            <thead class="text-center">
                <?php
                    $sql_study = "SELECT * FROM Study WHERE study_ID = " . $_POST["study_ID"] . " LIMIT 1;";
                    $result_study = mysqli_query($conn, $sql_study);
                    $row_study = mysqli_fetch_assoc($result_study);
                ?>
                
                <tr>
                    <th>Full Name</th>
                    <?php
                        echo "<td>" . $row_study['full_name']  . "</td>";     
                    ?>    
                </tr>    
                    
                <tr>        
                    <th>Short Name</th>
                    <?php
                        echo "<td>" . $row_study['short_name']  . "</td>";     
                    ?>                        
                </tr> 
                
                <tr>    
                    <th>IRB</th>
                    <?php
                        echo "<td>" . $row_study['IRB']  . "</td>";     
                    ?>                        
                </tr> 
                    
                <tr>        
                    <th>Created By</th>
                    <?php
                    // show name for created_by, not id                  
                    if (isset($row_study['created_by'])){
                        $sql_users = "SELECT name FROM tbl_users WHERE id = " . $row_study['created_by'] . " LIMIT 1;";
                        $result_users = mysqli_query($conn, $sql_users);
                        $row_users = mysqli_fetch_assoc($result_users);
                            
                        echo "<td>" . $row_users['name'] . "</td>";
                    }
                    else{
                        echo "<td>-</td>";
                    }  
                    ?>
                </tr> 
                    
                <tr>                    
                    <th>Time Created</th>
                    <?php
                    echo "<td>" . $row_study['created_at']     . "</td>";  
                    ?>
                </tr> 
                    
                <tr>                    
                    <th>Time of Last Edit</th>
                    <?php
                    echo "<td>" . $row_study['last_edited_at']     . "</td>";  
                    ?>                    
                </tr> 
                    
                <tr>
                    <th>Last Edited By</th>
                    <?php          
                    // show name for last_edited_by, not id    
                    if (isset($row_study['last_edited_by'])){
                        $sql_users = "SELECT name FROM tbl_users WHERE id = " . $row_study['last_edited_by'] . " LIMIT 1;";
                        $result_users = mysqli_query($conn, $sql_users);
                        $row_users = mysqli_fetch_assoc($result_users);
                        echo "<td>" . $row_users['name'] . "</td>";
                    }
                    else{
                        echo "<td>-</td>";
                    }    
                    ?>
                </tr>                     
                    
                <tr>
                    <th>Action</th>
                    <?php       
                        if (Session::get('roleid') === '1' || Session::get('roleid') === '2') {
 
                            echo "<td>";                             
                            echo "<form method=\"post\">";
                            echo "<input type=\"hidden\" name=\"study_ID\" value=\"" . $_POST['study_ID'] . "\">";
                            echo "<input type=\"submit\" name=\"deactivate-btn\" value=\"Deactivate\" />";
                            echo "</form>";
                                
                            echo "<a href=\"edit_study.php\"  class=\"btn btn-success\" data-study_ID=\"" . $row_study['study_ID'] . "\">Edit</a>";
                            echo "</td>";
                        
                        } else if (Session::get('roleid') === '3' || Session::get('roleid') === '4') {
                            echo "<td>";
                            echo "<form method=\"post\">";
                            echo "<input type=\"hidden\" name=\"study_ID\" value=\"" . $_POST['study_ID'] . "\">";
                            echo "<input type=\"submit\" name=\"leave-btn\" value=\"Leave\" />";
                            echo "</form>";
                            echo "</td>";
                        }
                    ?>                    
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
   $(document).on("click", "a.btn-success", redirectUser); 
});

function redirectUser(){
    let form = document.createElement("form");
    let hiddenInput = document.createElement("input");
    
    form.setAttribute("method", "POST");
    form.setAttribute("action", $(this).attr("href"));
    form.setAttribute("style", "display: none");
    
    hiddenInput.setAttribute("type", "hidden");
    hiddenInput.setAttribute("name", "study_ID");
    hiddenInput.setAttribute("value", $(this).attr("data-study_ID"));
    
    form.appendChild(hiddenInput);
    document.body.appendChild(form);
    form.submit();
    
    return false;
}
</script>

<?php
  include 'inc/footer.php';
?>