<?php
include 'inc/header.php';
include_once 'database.php';
Session::CheckSession();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_session'])){
    $insertSessionMessage = $users->insert_session($_POST); 
    if (isset($insertSessionMessage)){
        echo $insertSessionMessage; ?>
        
        <script type="text/javascript">
            const divMsg = document.getElementById("flash-msg");
            if (divMsg.classList.contains("alert-success")){
                setTimeout(redirectUser, 2000);
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
    
 <div class="card ">
    <div class="card-header">
        <h3 class="text-center">
            Create a Session
            <a class="float-right btn btn-primary" href="view_study">Back</a>
        </h3>
    </div>
    <div class="cad-body">
        <div class="card-body">
            <form class="" action="" method="post">
            <div class="form-group">
                <label for="participant_name">Add a Participant</label>
                <select class="form-control" name="participant_ID" id="participant_name">
                    <option value="" selected hidden disabled>Please Choose...</option>
                    <?php
                    
                    $sql = "SELECT participant_id, anonymous_name, dob 
                            FROM Participants
                            WHERE affiliation_id IN (SELECT affiliationid
                                                     FROM tbl_users
                                                     WHERE id = " . Session::get("id") . ");";
                    $result = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_assoc($result)){
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
</div>

<?php
  include 'inc/footer.php';
?>