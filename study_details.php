<?php
include 'inc/header.php';
include_once 'lib/Database.php';

$db = Database::getInstance();
$pdo = $db->pdo;

Session::CheckSession();

if (isset($_POST['deactivate-btn'])){
    $studyDeactivatedMessage = $studies->deactivateStudy($_POST["study_ID"]);
    if (isset($studyDeactivatedMessage)){
        echo $studyDeactivatedMessage; ?>
        
        <script type="text/javascript">
            const divMsg = document.getElementById("flash-msg");
            if (divMsg.classList.contains("alert-success")){
                setTimeout(function() {
                    location.href = "view_study";
                }, 2000);
            }
        </script>
<?php }
}

if (isset($_POST["activate-btn"])){
    $studyActivatedMessage = $studies->activateStudy($_POST["study_ID"]);
    if (isset($studyActivatedMessage)){
        echo $studyActivatedMessage;
    }
}

if (isset($_POST['leave-btn'])){
    $successMessage = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Success!</strong> You left this study!</div>';
    $leaveStudyMessage = $studies->leaveStudy($_POST["study_ID"]);
    
    if (isset($leaveStudyMessage)){
        if ($leaveStudyMessage === $successMessage){
            echo $leaveStudyMessage;
            header("Location: ./view_study");
        }
        else{
            echo $leaveStudyMessage;
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3>Study Details <span class="float-right"> <a href="view_study" class="btn btn-primary">Back</a></span></h3>
    </div>

    <div class="card-body pr-2 pl-2" style="display: flex; align-items: stretch;">
        <table class="table table-striped table-bordered" style="flex-basis: 60%;">
            <thead class="text-center">
                <?php
                    $sql_study = "SELECT S.study_ID, S.is_active, S.full_name, S.short_name, S.IRB, S.description, S.created_by, S.created_at, S.last_edited_at, S.last_edited_by 
                                  FROM Study AS S 
                                  WHERE S.study_ID = " . $_POST["study_ID"] . ";";
                    $result_study = $pdo->query($sql_study);
                    $row_study = $result_study->fetch(PDO::FETCH_ASSOC);
                    
                    $sql_times = "SELECT SSQ.name
                                  FROM SSQ_times as SSQ JOIN Study AS S ON(S.study_ID = SSQ.study_ID)
                                  WHERE S.study_ID = " . $_POST["study_ID"] . " AND SSQ.is_active = 1;";
                    $result_times = $pdo->query($sql_times);
                    $row_times = $result_times->fetch(PDO::FETCH_ASSOC);
                    
                ?>
                
                <tr>
                    <th>Full Name</th>
                    <td><?php echo $row_study['full_name']; ?></td> 
                    
                </tr>    
                    
                <tr>        
                    <th>Short Name</th>
                    <?php
                        echo "<td>" . $row_study['short_name']  . "</td>";     
                    ?>                        
                </tr> 
                <tr>
                    <th>Description</th>
                    <?php
                        echo "<td>" . $row_study['description'] . "</td>";
                    ?>
                </tr>
                <tr>    
                    <th>IRB</th>
                    <?php
                        echo "<td>" . $row_study['IRB']  . "</td>";     
                    ?>                        
                </tr> 
                <tr>
                    <th>SSQ Times</th>
                    <?php          
                    // show name for SSQ Times    
                    if (isset($row_times['name'])){
                        
                        $times = [];
                        
                        array_push($times, $row_times["name"]);
                        
                        while ($row = $result_times->fetch(PDO::FETCH_ASSOC)) { 
                            array_push($times, $row["name"]);
                        }
                    
                        
                        $final_times = implode(", ",$times);
                        
                        echo "<td>" . $final_times . "</td>";
                    }
                    else{
                        echo "<td>-</td>";
                    }    
                    ?>
                </tr> 
                <tr>
                    <th>Created By</th>
                    <?php
                    // show name for created_by, not id                  
                    if (isset($row_study['created_by'])){
                        $sql_users = "SELECT name FROM tbl_users WHERE id = " . $row_study['created_by'] . " LIMIT 1;";
                        $result_users = $pdo->query($sql_users);
                        $row_users = $result_users->fetch(PDO::FETCH_ASSOC);
                        
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
                        $result_users = $pdo->query($sql_users);
                        $row_users = $result_users->fetch(PDO::FETCH_ASSOC);
                        echo "<td>" . $row_users['name'] . "</td>";
                    }
                    else{
                        echo "<td>-</td>";
                    }    
                    ?>
                </tr> 
            </thead>
        </table>
                    <div style="border: 1px solid #e3e3e3; display: flex; flex-direction: column; margin-bottom: 1rem; flex-basis: 40%; align-items: center; justify-content: center;"><?php if (Session::get('roleid') === '1' || Session::get('roleid') === '2') {?>
                            <form method="post">
                                <input type="hidden" name="study_ID" value="<?php echo $_POST['study_ID']; ?>">
                                <?php if ($row_study["is_active"] === "1"){ ?>
                                        <input type="submit" name="deactivate-btn" value="Deactivate">
                                <?php }
                                      else{ ?>
                                        <input type="submit" name="activate-btn" value="Activate">
                                <?php } ?>
                            </form>
                            <br>
                            <div>
                                <a href="edit_study"  class="btn btn-success" data-study_ID="<?php echo $row_study['study_ID']; ?>">Edit</a>
                            </div>
                            <br>
                            
                            <div>
                                <a href="add_researcher" class="btn btn-primary btn-success" data-study_ID="<?php echo $_POST['study_ID']; ?>">Add A Researcher</a> 
                            </div>
                            <br>
                            
                            <div>
                                <a href="remove_researcher" class="btn btn-primary btn-success" data-study_ID="<?php echo $_POST['study_ID']; ?>">Remove A Researcher</a>
                            </div>
                            <br>
                            <div>
                                <a href="participantList" class="btn btn-primary btn-success" data-study_ID="<?php echo $_POST['study_ID']; ?>">View Participants</a>
                            </div>
                            <br>
                            <div>
                                <a href="addParticipant" class="btn btn-primary btn-success" data-study_ID="<?php echo $_POST['study_ID']; ?>">Add A Participant</a>
                            </div>
                            <br>
                            <div>
                                <a href="remove_participant" class="btn btn-primary btn-success" data-study_ID="<?php echo $_POST['study_ID']; ?>">Remove A Participant</a>
                            </div>
                            
                    <?php } else if (Session::get('roleid') === '3' || Session::get('roleid') === '4'){ ?>
                            <form method="POST">
                                <input type="hidden" name="study_ID" value="<?php echo $_POST['study_ID']; ?>">
                                <input type="submit" name="leave-btn" value="Leave">
                            </form>
                            <br>
                            <div>
                                <a href="participantList" class="btn btn-primary btn-success" data-study_ID="<?php echo $_POST['study_ID']; ?>">View Participants</a>
                            </div>
                    <?php } ?></div>
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