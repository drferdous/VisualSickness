<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();
$db = Database::getInstance();
$pdo = $db->pdo;

if (isset($_POST['study_ID']) && isset($_POST['iv'])) {
    $iv = hex2bin($_POST['iv']);
    $decrypted = Crypto::decrypt($_POST['study_ID'], $iv);
    Session::setStudyId(intval($decrypted), $pdo);
}

if (Session::get('study_ID') == 0) {
    header('Location: view_study');
    exit();
}
    
Session::requireResearcherOrUser(Session::get('study_ID'), $pdo);
$active_sql = "SELECT is_active FROM Study WHERE study_ID = " . Session::get('study_ID') . " LIMIT 1;";
$res = $pdo->query($active_sql);
if ($res->fetch(PDO::FETCH_ASSOC)['is_active'] == 0) {
    header('Location: view_study');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_session'])){
    $insertSessionMessage = $studies->insert_session($_POST); 
    if (isset($insertSessionMessage)){
        echo $insertSessionMessage; ?>
        
        <script type="text/javascript">
            const divMsg = document.getElementById("flash-msg");
            if (divMsg.classList.contains("alert-success")){
                setTimeout(() => {
                    redirect('session_details', {'session_ID': <?= Session::get('session_ID') ?>})
                }, 1000);
            }
        </script>
<?php
    }
} 
$role_sql = "SELECT study_role FROM Researcher_Study WHERE study_ID = " . Session::get("study_ID") . " AND  researcher_ID = " . Session::get("id") . " AND is_active = 1;";
                    
$role_result = $pdo->query($role_sql);
$role = $role_result->fetch(PDO::FETCH_ASSOC);
?>
    
 <div class="card">
    <div class="card-header">
        <h3 class="text-center float-left">
            Create a Session
        </h3>
        <?php if(isset($role['study_role']) && $role['study_role'] != 4){ ?>
        <a class="float-right btn btn-primary" href="addParticipant">Add Participant</a>
        <?php } ?>
        <a class="float-right btn btn-primary" href="view_study" style="transform: translateX(-10px)">Back </a>
    </div>
        <div class="card-body">
            <form class="" action="" method="post">
                <div style="margin-block: 6px;">
                    <small style='color: red'>
                        * Required Field
                    </small>
                </div>
                <div class="form-group">
                    <label for="participant_name" class="required">Select a Participant</label>
                    <select class="form-control" name="participant_ID" id="participant_name" required>
                        <option value="" selected hidden disabled>Please Choose...</option>
                        <?php
                    
                        $sql = "SELECT participant_id,anonymous_name, dob, iv
                    FROM Participants 
                    WHERE is_active = 1
                    AND study_id = " . Session::get('study_ID') . ";";
                                    
                        $result = $pdo->query($sql);
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                            $iv = hex2bin($row['iv']);
                            $name = Crypto::decrypt($row['anonymous_name'], $iv);
                            //print_r($row);
                            echo "<option value=\"" . $row['participant_id'] . "\">";
                            echo $name . " - " . $row['dob'];
                            echo "</option>";
                        }
                        ?>
                    </select>
                </div>
                    
                <div class="form-group">
                    <label for="comment">Comments</label>
                    <input type="text" class="form-control" id="comment" name="comment">
                </div>
                
                <div class="form-group">
                     <button type="submit" name="insert_session" class="btn btn-success">Start Session</button>
                </div>
            </form>
            
        </div>
</div>
<?php
  include 'inc/footer.php';
?>