<?php
include 'inc/header.php';
include_once 'lib/Database.php';

$db = Database::getInstance();
$pdo = $db->pdo;

Session::CheckSession();
unset($_SESSION['participant_ID']);
if (isset($_POST['study_ID']) && isset($_POST['iv'])) {
    $iv = hex2bin($_POST['iv']);
    $decrypted = Crypto::decrypt($_POST['study_ID'], $iv);
    Session::setStudyId(intval($decrypted), $pdo);
    header('Location: study_details');
    exit();
}

Session::requireStudyID();
$study_ID = Session::get('study_ID');
if (Session::get('roleid') == 1) {
    $affil_sql = "SELECT users.affiliation_id FROM study
                    JOIN users ON study.created_by = users.user_id
                    WHERE study.study_id = $study_ID
                    AND users.affiliation_id = " . Session::get('affiliationid');
    $affil_result = $pdo->query($affil_sql);
    if (!$affil_result->rowCount()) {
        header('Location: study_list');
    }
} else {
    Session::requireResearcherOrUser($study_ID, $pdo);
}

unset($_SESSION['participant_ID']);
    
if (isset($_POST['deactivate-btn']) && Session::CheckPostID($_POST)){
    $message = "This function is called.";
    $studyDeactivatedMessage = $studies->deactivateStudy($study_ID);
    if (isset($studyDeactivatedMessage)){
        echo $studyDeactivatedMessage;
    }
}


if (isset($_POST["activate-btn"]) && Session::CheckPostID($_POST)){
    $studyActivatedMessage = $studies->activateStudy($study_ID);
    if (isset($studyActivatedMessage)){
        echo $studyActivatedMessage;
    }
}

if (isset($_POST['leave-btn']) && Session::CheckPostID($_POST)){
    $leaveStudyMessage = $studies->leaveStudy($study_ID);
    if (isset($leaveStudyMessage)){
        echo $leaveStudyMessage;?>
        
        <script type="text/javascript">
            const divMsg = document.getElementById("flash-msg");
            if (divMsg.classList.contains("alert-success")){
                setTimeout(function() {
                    location.href = "study_list";
                }, 1000);
            }
        </script>
<?php }
}

$sql_study = "SELECT S.study_id, S.is_active, S.full_name, S.short_name, S.IRB, S.description, S.created_by, S.created_at, S.last_edited_at, S.last_edited_by 
              FROM study AS S 
              WHERE S.study_id = " . $study_ID . ";";
$result_study = $pdo->query($sql_study);
$row_study = $result_study->fetch(PDO::FETCH_ASSOC);

$sql_times = "SELECT name FROM ssq_times
              WHERE study_id = " . $study_ID . " AND is_active = 1;";
$result_times = $pdo->query($sql_times);

$sql_session_times = "SELECT name FROM session_times
              WHERE study_id = " . $study_ID . " AND is_active = 1;";
$result_session_times = $pdo->query($sql_session_times);
                    
$role_sql = "SELECT study_role FROM researchers WHERE study_id = " . Session::get("study_ID") . " AND  researcher_id = " . Session::get("id") . " AND is_active = 1;";
    
$role_result = $pdo->query($role_sql);
$role = $role_result->fetch(PDO::FETCH_ASSOC);

$rand = bin2hex(openssl_random_pseudo_bytes(16));
Session::set("post_ID", $rand);

$timezone = Session::get('time_offset');
$time_created = new DateTime($row_study['created_at']);
$time_edited = new DateTime($row_study['last_edited_at']);
if($timezone < 0) {
    $time_created->sub(new DateInterval('PT' . abs($timezone) . 'M'));
    $time_edited->sub(new DateInterval('PT' . abs($timezone) . 'M'));
} else {
    $time_created->add(new DateInterval('PT' . $timezone . 'M'));
    $time_edited->add(new DateInterval('PT' . $timezone . 'M'));
}

?>
<div class="card">
    <nav class="navDropdown navbar navbar-expand-md navbar-light bg-light justify-content-between">
        <h1 class="mb-0">Study Details</h1>
        <div class="navbar-collapse collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown mr-2">
                    <a class="nav-link dropdown-toggle font-weight-bold" href="javascript:void(0);" id="navbarDropdownMenuLinkRight" role="button" data-mdb-toggle="dropdown" aria-expanded="false" style="color: black; background-color: rgba(0, 98, 204, 0.32); border-radius: 4px;">
                        More Actions
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkRight">
                        <li class="dropdown-submenu">
                            <a href="javascript:void(0)" class="nested-dropdown dropdown-item"><i class="fas fa-caret-left mr-2"></i>Researcher</a>
                            <ul class="dropdown-menu dropdown-submenu">
                                <li><a href="researcher_list" class="dropdown-item nested-dropdown-item">View Researchers</a></li>
                                <?php if(isset($role['study_role']) && $role['study_role'] == 2){
                                    if ($row_study['is_active'] == 1) { ?>
                                        <li><a href="edit_researchers" class="dropdown-item nested-dropdown-item">Edit Researchers</a></li>
                                        <li><a href="remove_researcher" class="dropdown-item nested-dropdown-item">Remove A Researcher</a></li>
                                        <li><a href="add_researcher" class="dropdown-item nested-dropdown-item">Add A Researcher</a></li>
                                    <?php }
                                    $sql = "SELECT u.email FROM users AS u
                                        JOIN researchers AS researcher ON researcher.researcher_id = u.user_id
                                        WHERE researcher.study_id = $study_ID
                                        AND researcher.is_active = 1
                                        AND u.status = 1;";
                                    $email_result = $pdo->query($sql);
                                    $emails = array();
                                    while ($row = $email_result->fetch(PDO::FETCH_ASSOC)) {
                                        array_push($emails, $row['email']);
                                    }
                                    $mailing_list = implode(',', $emails);
                                    ?>
                                    <li><a href="mailto:<?= $mailing_list ?>" class="dropdown-item nested-dropdown-item">Contact Researchers</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <?php if(isset($role['study_role'])) { ?>
                            <li class="dropdown-submenu">
                                <a href="javascript:void(0)" class="nested-dropdown dropdown-item"><i class="fas fa-caret-left mr-2"></i>Participant</a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <li><a href="participant_list?forStudy=true" class="dropdown-item nested-dropdown-item">View Participants</a></li>
                                    <?php if($role['study_role'] != 4 && $row_study['is_active'] == 1){ ?>
                                        <li><a href="add_participant" class="dropdown-item nested-dropdown-item">Add A Participant</a></li>
                                        <li><a href="remove_participant" class="dropdown-item nested-dropdown-item">Remove A Participant</a></li>
                                        <li>
                                            <form method="post" class="d-inline" action="edit_participants">
                                                <input type="hidden" name="forStudy" value="true">
                                                <input class="dropdown-item nested-dropdown-item" value="Edit A Participant" type="submit">
                                            </form>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>        
                        <?php } if(isset($role['study_role']) && $role['study_role'] == 2) { ?>
                            <li class="dropdown-submenu">
                                <a href="javascript:void(0)" class="nested-dropdown dropdown-item"><i class="fas fa-caret-left mr-2"></i>Manage</a>
                                <ul class="dropdown-menu dropdown-submenu">
                                    <?php if ($row_study['is_active'] == 1) { ?>
                                        <li><a href="edit_study" class="dropdown-item nested-dropdown-item">Edit Study</a></li>
                                    <?php } ?>
                                    <li><form method="post" class="d-inline" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>">
                                        <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
                                        <?php if ($row_study["is_active"] === "1"){ ?>
                                            <input class="dropdown-item nested-dropdown-item" type="submit" name="deactivate-btn" value="Deactivate" onclick="return confirm('Are you sure you want to deactivate the study \'<?php echo $row_study['short_name']; ?>\'? You cannot edit the study if it is inactive.');">
                                        <?php } else { ?>
                                            <input class="dropdown-item nested-dropdown-item" type="submit" name="activate-btn" value="Activate" onclick="return confirm('Are you sure you want to activate the study \'<?php echo $row_study['short_name']; ?>\'?');">
                                        <?php } ?>
                                    </form></li>
                                </ul>
                            </li>
                        <?php } ?>
                        
                        <?php 
                        $pi_sql = "SELECT COUNT(study_role) AS Count FROM researchers WHERE study_id = " . Session::get("study_ID") . " AND study_role = 2 AND is_active = 1;";
                        $pi_result = $pdo->query($pi_sql);
                        $pi_count = $pi_result->fetch(PDO::FETCH_ASSOC);
                        if ($row_study['is_active'] == 1 && isset($role['study_role']) && ($role['study_role'] != 2 || $pi_count['Count'] > 1)) { ?>
                            <li><div class="dropdown-divider"></div></li>
                            <li><form method="post" class="d-inline" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>">
                                <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
                                <input class="dropdown-item" type="submit" name="leave-btn" value="Leave" onclick="return confirm('Are you sure you want to leave the study? You will no longer have access to the study \'<?php echo $row_study['short_name']; ?>\' unless a researcher adds you back.');">
                            </form></li>
                        <?php } ?>
                    </ul>
                </li>
                <li><a href="study_list" class="backBtn btn btn-primary">Back</a></li>
            </ul>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#studyNavbar" aria-controls="studyNavbar" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="studyNavbar">
            <ul class="navbar-nav ml-auto">
 
                <?php if($row_study['is_active'] == 1) { ?>
                    <li class="nav-item dropdown">
                        <a href="javascript:void(0)" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="researcherDropdown">Researcher</a>
                        <ul class="dropdown-menu">
                            <li><a href="researcher_list" class="dropdown-item">View Researchers</a></li>
                            <?php if(isset($role['study_role']) && $role['study_role'] == 2){ ?>
                                <li><a href="edit_researchers" class="dropdown-item">Edit Researchers</a></li>
                                <li><a href="remove_researcher" class="dropdown-item">Remove A Researcher</a></li>
                                <li><a href="add_researcher" class="dropdown-item">Add A Researcher</a></li>
                                <?php
                                $sql = "SELECT u.email FROM users AS u
                                    JOIN researchers AS researcher ON researcher.researcher_id = u.user_id
                                    WHERE researcher.study_id = $study_ID
                                    AND researcher.is_active = 1
                                    AND u.status = 1;";
                                $email_result = $pdo->query($sql);
                                $emails = array();
                                while ($row = $email_result->fetch(PDO::FETCH_ASSOC)) {
                                    array_push($emails, $row['email']);
                                }
                                $mailing_list = implode(',', $emails);
                                ?>
                                <li><a href="mailto:<?= $mailing_list ?>" class="dropdown-item">Contact Researchers</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php if(isset($role['study_role'])) { ?>
                    <li class="nav-item dropdown">
                        <a href="javascript:void(0)" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="participantDropdown">Participant</a>
                        <ul class="dropdown-menu">
                            <li><a href="participant_list?forStudy=true" class="dropdown-item">View Participants</a></li>
                            <?php if($role['study_role'] != 4){ ?>
                                <li><a href="add_participant" class="dropdown-item">Add A Participant</a></li>
                                <li><a href="remove_participant" class="dropdown-item">Remove A Participant</a></li>
                                <li>
                                    <form method="post" class="d-inline" action="edit_participants">
                                        <input type="hidden" name="forStudy" value="true">
                                        <input class="dropdown-item" value="Edit A Participant" type="submit">
                                    </form>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } 
                } if(isset($role['study_role']) && $role['study_role'] == 2) { ?>
                    <li class="nav-item dropdown">
                        <a href="javascript:void(0)" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="manageDropdown">Manage</a>
                        <ul class="dropdown-menu">
                            <li><a href="edit_study"  class="dropdown-item">Edit Study</a></li>
                            <li><form method="post" class="d-inline" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>">
                                <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
                                <?php if ($row_study["is_active"] === "1"){ ?>
                                    <input class="dropdown-item" type="submit" name="deactivate-btn" value="Deactivate" onclick="return confirm('Are you sure you want to deactivate the study \'<?php echo $row_study['short_name']; ?>\'? You cannot edit the study if it is inactive.');">
                                <?php } else { ?>
                                    <input class="dropdown-item" type="submit" name="activate-btn" value="Activate" onclick="return confirm('Are you sure you want to activate the study \'<?php echo $row_study['short_name']; ?>\'?');">
                                <?php } ?>
                            </form></li>
                        </ul>
                <?php  }
                $pi_sql = "SELECT COUNT(study_role) AS Count FROM researchers WHERE study_id = " . Session::get("study_ID") . " AND study_role = 2 AND is_active = 1;";
                $pi_result = $pdo->query($pi_sql);
                $pi_count = $pi_result->fetch(PDO::FETCH_ASSOC);
                if ($row_study['is_active'] == 1 && isset($role['study_role']) && ($role['study_role'] != 2 || $pi_count['Count'] > 1)) { ?>
                    <li class="nav-item">
                        <form method="post" class="d-inline" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>">
                            <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
                            <input style="background:none;width: 100%;border:none;text-align: start;" class="nav-link" type="submit" name="leave-btn" value="Leave" onclick="return confirm('Are you sure you want to leave the study? You will no longer have access to the study \'<?php echo $row_study['short_name']; ?>\' unless a researcher adds you back.');">
                        </form>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a href="study_list" class="backBtn nav-link">Back</a>
                </li>
            </ul>
        </div>
    </nav>
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
                        
                        $times = [];
                        
                        while ($row = $result_times->fetch(PDO::FETCH_ASSOC)) { 
                            array_push($times, $row["name"]);
                        }
                    
                        
                        $final_times = implode(", ",$times);
                        
                        echo $final_times;
                    ?></td>
                </tr> 
                <tr>
                    <th class="align-middle">Session Names</th>
                    <td class="align-middle"><?php
                        // show name for Session Times
                        
                        $session_times = [];
                        
                        while ($row = $result_session_times->fetch(PDO::FETCH_ASSOC)) { 
                            array_push($session_times, $row["name"]);
                        }
                    
                        
                        $final_times = implode(", ",$session_times);
                        
                        echo $final_times;
                    ?></td>
                </tr> 
                <tr>
                    <th class="align-middle">Created By</th>
                    <td class="align-middle"><?php
                    // show name for created_by, not id                  
                    if (isset($row_study['created_by'])){
                        $sql_users = "SELECT name FROM users WHERE user_id = " . $row_study['created_by'] . " LIMIT 1;";
                        $result_users = $pdo->query($sql_users);
                        $row_users = $result_users->fetch(PDO::FETCH_ASSOC);
                        
                        echo $row_users['name'];
                    }
                    ?></td>
                </tr> 
                    
                <tr>                    
                    <th class="align-middle">Time Created</th>
                    <td class="align-middle"><?= date_format($time_created,"M d, Y h:i A") ?></td>
                </tr> 
                    
                <tr>                    
                    <th class="align-middle">Time of Last Edit</th>
                    <td class="align-middle"><?= date_format($time_edited,"M d, Y h:i A") ?></td>
                </tr> 
                    
                <tr>
                    <th class="align-middle">Last Edited By</th>
                    <td class="align-middle"><?php          
                    // show name for last_edited_by, not id    
                    if (isset($row_study['last_edited_by'])){
                        $sql_users = "SELECT name FROM users WHERE user_id = " . $row_study['last_edited_by'] . " LIMIT 1;";
                        $result_users = $pdo->query($sql_users);
                        $row_users = $result_users->fetch(PDO::FETCH_ASSOC);
                        echo $row_users['name'];
                    }
                                
                    ?></td>
                </tr> 
            </thead>
        </table>
    </div>
    <script>
        let dropped = false;
        $(document).ready(() => {
            $(document).on('click', function (e) {
                if (e.target.id != 'navbarDropdownMenuLinkRight' && !e.target.classList.contains('nested-dropdown') && dropped) {
                    $('#navbarDropdownMenuLinkRight').dropdown('toggle');
                    $('.dropdown-submenu a.nested-dropdown').next('ul').toggle(false);
                    dropped = false;
                }
            });
            // $('a.editParticipant').on('click', function () {
            //     let form = document.createElement("form");
                    
            //     form.setAttribute("method", "POST");
            //     form.setAttribute("action", "edit_participants");
            //     form.setAttribute("style", "display: none");
                
            //     hiddenInput = document.createElement("input");
            //     hiddenInput.setAttribute("type", "hidden");
            //     hiddenInput.setAttribute("name", "forStudy");
            //     hiddenInput.setAttribute("value", "true");
            //     form.appendChild(hiddenInput);
                
            //     document.body.appendChild(form);
            //     form.submit();
                    
            //     return false;
            // });
            $('#navbarDropdownMenuLinkRight').on('click', () => {
                $('#navbarDropdownMenuLinkRight').dropdown('toggle');
                dropped = !dropped;
            });
            $('.dropdown-submenu a.nested-dropdown').on("mouseover", function(e) {
                $('.dropdown-submenu a.nested-dropdown').next('ul').toggle(false);
                $(this).next('ul').toggle(true);
                e.stopPropagation();
                e.preventDefault();
            });
            $('.dropdown-submenu a.nested-dropdown').on("mouseout", function(e) {
                if (e.relatedTarget.classList.contains('dropdown-submenu') || e.relatedTarget.classList.contains('nested-dropdown-item')) return;
                $(this).next('ul').toggle(false);
                e.stopPropagation();
                e.preventDefault();
            });
        });
    </script>
</div>
<?php
  include 'inc/footer.php';
?>