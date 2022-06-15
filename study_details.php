<?php
include 'inc/header.php';
include_once 'lib/Database.php';

$db = Database::getInstance();
$pdo = $db->pdo;

Session::CheckSession();
if (isset($_POST['study_ID']) && isset($_POST['iv'])) {
    $iv = hex2bin($_POST['iv']);
    $decrypted = Crypto::decrypt($_POST['study_ID'], $iv);
    Session::setStudyId(intval($decrypted), $pdo);
    header('Location: study_details');
    exit();
}
if (Session::get('study_ID') == 0) {
    header('Location: view_study');
    exit();
}
$study_ID = Session::get('study_ID');

if (isset($_POST['deactivate-btn'])){
    $studyDeactivatedMessage = $studies->deactivateStudy($study_ID);
    if (isset($studyDeactivatedMessage)){
        echo $studyDeactivatedMessage;
    }
}


if (isset($_POST["activate-btn"])){
    $studyActivatedMessage = $studies->activateStudy($study_ID);
    if (isset($studyActivatedMessage)){
        echo $studyActivatedMessage;
    }
}

if (isset($_POST['leave-btn'])){
    $leaveStudyMessage = $studies->leaveStudy($study_ID);
    if (isset($leaveStudyMessage)){
        echo $leaveStudyMessage;?>
        
        <script type="text/javascript">
            const divMsg = document.getElementById("flash-msg");
            if (divMsg.classList.contains("alert-success")){
                setTimeout(function() {
                    location.href = "view_study";
                }, 1000);
            }
        </script>
<?php }
}
?>

<div class="card">
    <div class="card-header">
        <h3>Study Details <span class="float-right"> <a href="view_study" class="btn btn-primary backButton">Back</a></span></h3>
    </div>

    <div class="card-body pr-2 pl-2" style="display: flex; align-items: stretch;">
        <table class="table table-striped table-bordered" style="flex-basis: 60%;">
            <thead class="text-center">
                <?php
                    $sql_study = "SELECT S.study_ID, S.is_active, S.full_name, S.short_name, S.IRB, S.description, S.created_by, S.created_at, S.last_edited_at, S.last_edited_by 
                                  FROM Study AS S 
                                  WHERE S.study_ID = " . $study_ID . ";";
                    $result_study = $pdo->query($sql_study);
                    $row_study = $result_study->fetch(PDO::FETCH_ASSOC);
                    
                    $sql_times = "SELECT SSQ.name
                                  FROM SSQ_times as SSQ JOIN Study AS S ON(S.study_ID = SSQ.study_ID)
                                  WHERE S.study_ID = " . $study_ID . " AND SSQ.is_active = 1;";
                    $result_times = $pdo->query($sql_times);
                    $row_times = $result_times->fetch(PDO::FETCH_ASSOC);
                    
                ?>
                
                <tr>
                    <th class="align-middle">Full Name</th>
                    <td class="align-middle"><?= $row_study['full_name'] ?></td>
                </tr>
                <tr>        
                    <th class="align-middle">Short Name</th>
                    <td class="align-middle"><?= $row_study['short_name'] ?></td>
                </tr> 
                <tr>
                    <th class="align-middle">Description</th>
                    <td class="align-middle"><?= $row_study['description'] ?></td>
                </tr>
                <tr>    
                    <th class="align-middle">IRB</th>
                    <td class="align-middle"><?= $row_study['IRB'] ?></td>
                </tr> 
                <tr>
                    <th class="align-middle">SSQ Times</th>
                    <td class="align-middle"><?php          
                    // show name for SSQ Times    
                    if (isset($row_times['name'])){
                        
                        $times = [];
                        
                        array_push($times, $row_times["name"]);
                        
                        while ($row = $result_times->fetch(PDO::FETCH_ASSOC)) { 
                            array_push($times, $row["name"]);
                        }
                    
                        
                        $final_times = implode(", ",$times);
                        
                        echo $final_times;
                    }
                    ?></td>
                </tr> 
                <tr>
                    <th class="align-middle">Created By</th>
                    <td class="align-middle"><?php
                    // show name for created_by, not id                  
                    if (isset($row_study['created_by'])){
                        $sql_users = "SELECT name FROM tbl_users WHERE id = " . $row_study['created_by'] . " LIMIT 1;";
                        $result_users = $pdo->query($sql_users);
                        $row_users = $result_users->fetch(PDO::FETCH_ASSOC);
                        
                        echo $row_users['name'];
                    }
                    ?></td>
                </tr> 
                    
                <tr>                    
                    <th class="align-middle">Time Created</th>
                    <td class="align-middle"><?= $row_study['created_at'] ?></td>
                </tr> 
                    
                <tr>                    
                    <th class="align-middle">Time of Last Edit</th>
                    <td class="align-middle"><?= $row_study['last_edited_at'] ?></td>
                </tr> 
                    
                <tr>
                    <th class="align-middle">Last Edited By</th>
                    <td class="align-middle"><?php          
                    // show name for last_edited_by, not id    
                    if (isset($row_study['last_edited_by'])){
                        $sql_users = "SELECT name FROM tbl_users WHERE id = " . $row_study['last_edited_by'] . " LIMIT 1;";
                        $result_users = $pdo->query($sql_users);
                        $row_users = $result_users->fetch(PDO::FETCH_ASSOC);
                        echo $row_users['name'];
                    }
                    ?></td>
                </tr> 
            </thead>
        </table>
                    <div style="padding-block: 32px; border: 1px solid #e3e3e3; display: flex; flex-direction: column; margin-bottom: 1rem; flex-basis: 40%; align-items: center; justify-content: center;"><?php if (Session::get('roleid') === '1' || Session::get('roleid') === '2') {?>
                            <form method="post">
                                <?php if ($row_study["is_active"] === "1"){ ?>
                                        <input class="btn btn-danger" type="submit" name="deactivate-btn" value="Deactivate">
                                <?php }
                                      else{ ?>
                                        <input class="btn btn-danger" type="submit" name="activate-btn" value="Activate">
                                <?php } ?>
                            </form>
                            <br>
                            <div>
                                <a href="edit_study"  class="btn btn-success">Edit</a>
                            </div>
                            <br>
                            <div>
                                <a href="researcher_list" class="btn btn-primary btn-success">View Researchers</a>
                            </div>
                            <br>
                            <div>
                                <a href="add_researcher" class="btn btn-primary btn-success">Add A Researcher</a> 
                            </div>
                            <br>
                            
                            <div>
                                <a href="remove_researcher" class="btn btn-primary btn-success">Remove A Researcher</a>
                            </div>
                            <br>
                            <div>
                                <a href="participantList?forStudy=true" class="btn btn-primary btn-success">View Participants</a>
                            </div>
                            <br>
                            <div>
                                <a href="addParticipant" class="btn btn-primary btn-success">Add A Participant</a>
                            </div>
                            <br>
                            <div>
                                <a href="remove_participant" class="btn btn-primary btn-success">Remove A Participant</a>
                            </div>
                            
                    <?php } else if (Session::get('roleid') === '3' || Session::get('roleid') === '4'){ ?>
                            <form method="POST">
                                <input class="btn btn-danger" type="submit" name="leave-btn" value="Leave">
                            </form>
                            <br>
                            <div>
                                <a href="participantList" class="btn btn-primary btn-success">View Participants</a>
                            </div>
                    <?php } ?></div>
    </div>
</div>
<?php
  include 'inc/footer.php';
?>