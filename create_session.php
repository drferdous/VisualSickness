<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();
$db = Database::getInstance();
$pdo = $db->pdo;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_session'])){
    $insertSessionMessage = $studies->insert_session($_POST); 
    if (isset($insertSessionMessage)){
        echo $insertSessionMessage; ?>
        
        <script type="text/javascript">
            const divMsg = document.getElementById("flash-msg");
            if (divMsg.classList.contains("alert-success")){
                setTimeout(redirectUser, 1000);
            }
            
            function redirectUser(){
                let form = document.createElement("form");
                let hiddenInput = document.createElement("input");
                
                form.setAttribute("method", "POST");
                form.setAttribute("action", "session_details");
                form.setAttribute("style", "display: none");
                
                hiddenInput.setAttribute("type", "hidden");
                hiddenInput.setAttribute("name", "session_ID");
                hiddenInput.setAttribute("value", "" + <?php echo Session::get('session_ID'); ?>);
                
                form.appendChild(hiddenInput);
                document.body.appendChild(form);
                form.submit();
                
                return;
            };
        </script>
<?php
    }
}?>
    
 <div class="card">
    <div class="card-header">
        <h3 class="text-center">
            Create a Session
            <a class="float-right btn btn-primary redirectUser" href="addParticipant" data-study_ID="<?php echo $_POST["study_ID"]; ?>">Add Participant</a>
            <a class="float-right btn btn-primary" href="view_study" style="transform: translateX(-10px)">Back </a>
        </h3>
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
                    
                        $sql = "SELECT participant_id,anonymous_name, dob
                    FROM Participants 
                    WHERE is_active = 1
                    AND study_id IN (SELECT study_ID
                                    FROM Researcher_Study
                                    WHERE researcher_ID =" . Session::get("id") . ");";
                                    
                        $result = $pdo->query($sql);
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                            echo "<option value=\"" . $row['participant_id'] . "\">";
                            echo $row['anonymous_name'] . " - " . $row['dob'];
                            echo "</option>";
                        }
                        ?>
                    </select>
                </div>
                    
                <div class="form-group">
                    <label for="comment">Comments</label>
                    <input type="text" class="form-control" id="comment" name="comment">
                </div>
                
                <div>
                    <input type="hidden" class="form-control" name="study_ID" value="<?php echo $_POST['study_ID']; ?>">
                </div>
                <div class="form-group">
                     <button type="submit" name="insert_session" class="btn btn-success">Start Session</button>
                </div>
            </form>
            
        </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on("click", "a.redirectUser", goToAddParticipant);
    });
    
    function goToAddParticipant(){
        let form = document.createElement("form");
        let hiddenInput;
        
        form.setAttribute("method", "POST");
        form.setAttribute("action", $(this).attr("href"));
        form.setAttribute("style", "display: none");

        hiddenInput = document.createElement("input");
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