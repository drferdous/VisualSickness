<?php
include 'inc/header.php';
include_once 'database.php';
Session::CheckSession();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_session'])){
    $insertSessionMessage = $users->insert_session($_GET["study_ID"], $_POST); 
    if (isset($insertSessionMessage)){
        echo $insertSessionMessage; ?>
        
        <script type="text/javascript">
            const divMsg = document.querySelector("#flash-msg");
            if (divMsg.classList.contains("alert-success")){
                setTimeout(function(){
                    let currSessionID = <?php echo Session::get('session_ID');?>; 
                    location.href = "session_details.php?session_ID=" + currSessionID;
                }, 2000);
            }
        </script>
<?php
    }
}?>
    
 <div class="card ">
    <div class="card-header">
        <h3 class="text-center">
            Create a Session
            <a class="float-right btn btn-primary" href="view_study.php">Back</a>
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
                    $sql = "SELECT participant_id, anonymous_name, dob FROM Participant;";
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