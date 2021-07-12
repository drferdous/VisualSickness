<?php
include 'inc/header.php';
include_once 'database.php';
Session::CheckSession();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_session'])){
    $insertSessionMessage = $users->insert_session($_GET["study_ID"], $_POST); 
    if (isset($insertSessionMessage)){
        echo $insertSessionMessage;
    }
}
?>
    
 <div class="card ">
    <div class="card-header">
          <h3 class='text-center'>Manage a Session</h3>
    </div>
    <div class="cad-body">
        <div class="card-body">
            <form class="" action="" method="post">
            <div class="form-group">
                <label for="participant_name">Add a Participant</label>
                <select class="form-control" name="participant_ID" id="participant_name">
                    <option value="" selected hidden disabled>Please Choose...</option>
                    <?php
                    $sql = "SELECT participant_id, anonymous_name FROM Participant;";
                    $result = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_assoc($result)){
                        echo "<option value=\"" . $row['participant_id'] . "\" >";
                        echo $row['anonymous_name'];
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