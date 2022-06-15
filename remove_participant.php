<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();

$db = Database::getInstance();
$pdo = $db->pdo;

if (Session::get('study_ID') == 0) {
    header('Location: view_study');
    exit();
}
$study_ID = Session::get('study_ID');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['removeParticipant'])) {
    $removeParticipant = $studies->removeParticipant($_POST);
}
if (isset($removeParticipant)) {
  echo $removeParticipant;?>
    <script type="text/javascript">
        const divMsg = document.getElementById("flash-msg");
        if (divMsg.classList.contains("alert-success")){
            setTimeout(function(){
                location.href = 'study_details';
            }, 1000);
        }
    </script>
<?php } ?>
 
<div class="card">
    <div class="card-header">
        <h3>Remove A Participant<span class="float-right"><a href="study_details" class="btn btn-primary">Back</a></span></h3> 
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
                                WHERE is_active = 1 AND study_id = $study_ID;";
                        $result = $pdo->query($sql);
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
                            <option value="<?php echo $row['participant_ID']; ?>"><?php echo $row['anonymous_name']; ?></option>
                    <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                 <button type="submit" name="removeParticipant" class="btn btn-success">Submit</button>
            </div>
        </form>
    </div>
</div>

<?php
  include 'inc/footer.php';
?>