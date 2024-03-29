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

Session::requireStudyID();    
Session::requireResearcherOrUser(Session::get('study_ID'), $pdo);
$active_sql = "SELECT is_active FROM study WHERE study_id = " . Session::get('study_ID') . " LIMIT 1;";
$res = $pdo->query($active_sql);
if ($res->fetch(PDO::FETCH_ASSOC)['is_active'] == 0) {
    header('Location: study_list');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_session']) && Session::CheckPostID($_POST)){
    if (isset($_POST["participant_ID"])){
        $info = explode(';', $_POST['participant_ID']);
        $participant_ID = $info[0];
        $iv = $info[1];
        $_POST["participant_ID"] = Crypto::decrypt($participant_ID, hex2bin($iv));
    
        $insertSessionMessage = $studies->insertSession($_POST); 
        if (isset($insertSessionMessage)){
            echo $insertSessionMessage; ?>
        
            <script type="text/javascript">
                const divMsg = document.getElementById("flash-msg");
                if (divMsg.classList.contains("alert-success")){
                    setTimeout(() => {
                        location.href = "session_details";
                    }, 1000);
                }
            </script>
<?php   }
    }
    else{
        echo Util::generateErrorMessage("No participant was given.");
    }
} 
$role_sql = "SELECT study_role FROM researchers WHERE study_id = " . Session::get("study_ID") . " AND  researcher_id = " . Session::get("id") . " AND is_active = 1;";
                    
$role_result = $pdo->query($role_sql);
$role = $role_result->fetch(PDO::FETCH_ASSOC);
?>
    
 <div class="card">
    <div class="card-header">
        <h1 class="text-center float-left mb-0">
            Create a Session
        </h1>
        <span class="float-right">
            <?php if(isset($role['study_role']) && $role['study_role'] != 4){ ?>
                <a class="btn btn-primary mx-2" href="add_participant">Add Participant</a>
            <?php } ?>
            <a class="btn btn-primary backBtn" href="study_list">Back</a>
        </span>
    </div>
        <div class="card-body">
            <form class="" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="post">
                <?php 
                    $rand = bin2hex(openssl_random_pseudo_bytes(16));
                    Session::set("post_ID", $rand);
                ?>
                <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
                <div style="margin-block: 6px;">
                    <small class="required-msg">
                        * Required Field
                    </small>
                </div>
                <div class="form-group">
                    <label for="participant_name" class="required">Select a Participant</label>
                    <?php 
                         $sql = "SELECT participant_id, anonymous_name, dob, iv
                                FROM participants 
                                WHERE is_active = 1
                                AND study_id = " . Session::get('study_ID') . ";";
                                    
                        $result = $pdo->query($sql); ?>
                    <select class="form-control form-select" name="participant_ID" id="participant_name" required <?= $result->rowCount() === 0 ? 'disabled' : '' ?>>
                        <option value="" disabled hidden selected><?= $result->rowCount() === 0 ? 'There are no participants in this study!' : 'Participant Name' ?></option>
                        <?php
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                            $iv = hex2bin($row['iv']);
                            $name = Crypto::decrypt($row['anonymous_name'], $iv); ?>
                            <option value="<?php echo Crypto::encrypt($row['participant_id'], $iv) . ";" . bin2hex($iv); ?>">
                            <?php echo $name . " - " . $row['dob']; ?>
                            </option>
                  <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="session_time" class="required">Select a Session Name</label>
                    <select class="form-control form-select" name="session_time" id="session_time" required disabled>
                        <option value="" selected hidden disabled>Please Choose...</option>
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

<script>
     $(document).ready(function() {
         $('#participant_name').change(function() {
            const info = $(this).val();
            const participant_id = info.split(';')[0];
            const iv = info.split(';')[1];
            $.ajax({
                url: "session_times",
                type: "POST",
                cache: false,
                data: {
                    participant_id,
                    iv
                },
                success:function(data){
                    $('#session_time').html(data);
                    $('#session_time').removeAttr("disabled");
                    if ($('.timesNotFound')[0]) $('#session_time').attr('disabled', 'true');
                }
            });	
         });
     });
 </script>

<?php
  include 'inc/footer.php';
?>