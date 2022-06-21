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
if (Session::get('roleid') == 1) {
    $affil_sql = "SELECT users.affiliationid FROM Study
                    JOIN tbl_users AS users
                    ON Study.created_by = users.id
                    WHERE Study.study_ID = $study_ID
                    AND users.affiliationid = " . Session::get('affiliationid');
    $affil_result = $pdo->query($affil_sql);
    if (!$affil_result->rowCount()) {
        header('Location: view_study');
    }
} else {
    Session::requireResearcherOrUser($study_ID, $pdo);
}
    
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
                    
$role_sql = "SELECT study_role FROM Researcher_Study WHERE study_ID = " . Session::get("study_ID") . " AND  researcher_ID = " . Session::get("id") . " AND is_active = 1;";
    
    $role_result = $pdo->query($role_sql);
    $role = $role_result->fetch(PDO::FETCH_ASSOC);
?>

<div class="card">
    <nav class="navDropdown navbar navbar-expand-lg navbar-light bg-light justify-content-between">
        <a class="navbar-brand">Study Details</a>
      <div class="container-fluid">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item dropdown mr-2">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLinkRight" role="button"
                data-mdb-toggle="dropdown" aria-expanded="false">
                More Actions
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkRight">
              <li><a href="researcher_list" class="dropdown-item">View Researchers</a></li>
              <?php 
                if(isset($role['study_role']) && $role['study_role'] != 4){ ?>
                    <li><a href="edit_researchers" class="dropdown-item">Edit Researchers</a></li>
              <?php }
              if(isset($role['study_role']) && $role['study_role'] == 2){ ?>
                <li><a href="add_researcher" class="dropdown-item">Add A Researcher</a></li>
                    <?php
                        $sql = "SELECT u.email FROM tbl_users AS u
                            JOIN Researcher_Study AS rs ON rs.researcher_ID = u.id
                            WHERE rs.study_ID = $study_ID
                            AND rs.is_active = 1
                            AND u.status = 1;";
                        $email_result = $pdo->query($sql);
                        $emails = array();
                        while ($row = $email_result->fetch(PDO::FETCH_ASSOC)) {
                            array_push($emails, $row['email']);
                        }
                        $mailing_list = implode(',', $emails);
                    ?>
                <li><a href="mailto:<?= $mailing_list ?>" class="dropdown-item">Contact Researchers</a></li>
                <?php } 
                    if($role['study_role'] == 2){ ?>
                        <li><a href="remove_researcher" class="dropdown-item">Remove A Researcher</a></li>
                <?php }
                    if(isset($role['study_role'])) { ?>
                        <li><a href="participantList?forStudy=true" class="dropdown-item">View Participants</a></li>
                <?php }
                    if(isset($role['study_role']) && $role['study_role'] != 4){ ?>
                        <li><a href="addParticipant" class="dropdown-item">Add A Participant</a></li>
                <?php } 
                    if(isset($role['study_role']) && $role['study_role'] != 4){ ?>
                        <li><a href="remove_participant" class="dropdown-item">Remove A Participant</a></li>
                <?php } ?>
                <form method="post" class="d-inline" action="">
                    <?php 
                        if ($row_study["is_active"] === "1" && $role['study_role'] == 2){ ?>
                            <li><input class="dropdown-item" type="submit" name="deactivate-btn" value="Deactivate" onclick="return confirm('Are you sure you want to deactivate the study \'<?php echo $row_study['short_name']; ?>\'? You cannot edit the study if it is inactive.');"></li>
                    <?php } else{ ?>
                        <?php if($role['study_role'] == 2) { ?>
                            <li><input class="dropdown-item" type="submit" name="activate-btn" value="Activate" onclick="return confirm('Are you sure you want to activate the study \'<?php echo $row_study['short_name']; ?>\'?');"></li>
                        <?php } 
                    } ?>
                </form>
                <?php if($role['study_role'] == 2) { ?>
                    <li><a href="edit_study"  class="dropdown-item">Edit</a></li>
                <?php } ?>
                
                <div class="dropdown-divider"></div>
                  <?php 
                    $pi_sql = "SELECT COUNT(study_role) AS Count FROM Researcher_Study WHERE study_ID = " . Session::get("study_ID") . " AND study_role = 2 AND is_active = 1;";
                    $pi_result = $pdo->query($pi_sql);
                    $pi_count = $pi_result->fetch(PDO::FETCH_ASSOC);
                    if (isset($role['study_role']) && ($role['study_role'] != 2 || $pi_count['Count'] > 1)){ ?>
                        <form method="post" class="d-inline" action="">
                            <li><input class="dropdown-item" type="submit" name="leave-btn" value="Leave" onclick="return confirm('Are you sure you want to leave the study? You will no longer have access to the study \'<?php echo $row_study['short_name']; ?>\' unless a researcher adds you back.');"></li>
                        </form>
                    <?php } ?>
            </ul>
          </li>
          <li><a href="view_study" class="btn btn-primary backButton">Back</a></li>
        </ul>
      </div>
    </nav>
</div>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    <div class="card-body pr-2 pl-2" style="display: flex; align-items: stretch;">
        <table class="table table-striped table-bordered" style="flex-basis: 100%;">
            <thead class="text-center">
                
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
    </div>
</div>
<script>
    let dropped = false;
    $(document).ready(() => {
        $(document).on('click', (e) => {
            if (e.target.id != 'navbarDropdownMenuLinkRight' && dropped) {
                $('#navbarDropdownMenuLinkRight').dropdown('toggle');
                dropped = false;
            }
        });
        $('#navbarDropdownMenuLinkRight').on('click', () => {
            $('#navbarDropdownMenuLinkRight').dropdown('toggle');
            dropped = !dropped;
        });
    })
</script>
<?php
  include 'inc/footer.php';
?>