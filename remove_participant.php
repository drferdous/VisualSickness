<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();

$db = Database::getInstance();
$pdo = $db->pdo;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['study_ID'])) {
    header('Location: view_study');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['removeParticipant'])) {
    $removeParticipant = $studies->removeParticipant($_POST);
    echo $removeParticipant;
}
if (isset($removeParticipant)) {
  echo $removeParticipant;
}
?>
 
<div class="card">
    <div class="card-header">
        <h3>Remove A Participant<span class="float-right"><a href="study_details" class="btn btn-primary redirectUser" data-study_ID="<?php echo $_POST['study_ID']; ?>">Back</a></span></h3> 
    </div>
    <div class="card-body pr-2 pl-2">
        <form class="" action="" method="post">
            <div style="margin-block: 6px;">
                <small style='color: red'>
                    * Required Field
                </small>
            </div>
            <div class="form-group">
                <div class="form-group">
                    <label for="researcher_ID" class="required">Remove a Participant</label>
                    <select class="form-control" name="participant_ID" id="participant_ID" required>
                        <option value="" disabled hidden selected>Participant Name</option>
                    <?php
                        $sql = "SELECT participant_ID, anonymous_name
                                FROM Participants
                                WHERE study_id = " . $_POST["study_ID"];
                        $result = $pdo->query($sql);
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
                            <option value="<?php echo $row['participant_ID']; ?>"><?php echo $row['anonymous_name']; ?></option>
                    <?php } ?>
                    </select>
                </div>
                <input type="hidden" name="study_ID" value="<?php echo $_POST['study_ID']; ?>">
            </div>
            <div class="form-group">
                 <button type="submit" name="removeParticipant" class="btn btn-success">Submit</button>
            </div>
        </form>
    </div>
</div>
      
 <script type="text/javascript">
    $(document).ready(function() {
        $(document).on("click", "a.redirectUser", redirectUser);
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
        
        form.append(hiddenInput);
        document.body.append(form);
        
        form.submit();
        
        return false;
    }
 </script>      


<?php
  include 'inc/footer.php';
?>