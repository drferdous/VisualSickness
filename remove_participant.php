<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();

$db = Database::getInstance();
$pdo = $db->pdo;

Session::requireStudyID();
$study_ID = Session::get('study_ID');

Session::requirePIorRA($study_ID, $pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['removeParticipant']) && Session::CheckPostID($_POST)) {
    if (isset($_POST["participant_ID"])){
        $info = explode(';', $_POST['participant_ID']);
        $participant_ID = $info[0];
        $iv = $info[1];
        $participant_ID = Crypto::decrypt($participant_ID, hex2bin($iv));
        $removeParticipant = $studies->removeParticipant($participant_ID);
    }
    else{
        echo Util::generateErrorMessage("Participant was not given.");
    }
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
        <h1 class="float-left">Remove A Participant</h1>
        <span class="float-right"><a href="study_details" class="backBtn btn btn-primary">Back</a></span>
    </div>
    <div class="card-body pr-2 pl-2">
        <form action="remove_participant" method="post">
            <?php 
                $rand = bin2hex(openssl_random_pseudo_bytes(16));
                Session::set("post_ID", $rand);
            ?>
            <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
            <div style="margin-block: 6px;">
                <small class='required-msg'>
                    * Required Field
                </small>
            </div>
            <div class="form-group">
                <div class="form-group">
                    <label for="participant_ID" class="required">Remove a Participant</label>
                    <?php
                        $sql = "SELECT participant_id, anonymous_name, iv, dob
                                FROM participants
                                WHERE is_active = 1 AND study_id = $study_ID;";
                        $result = $pdo->query($sql); ?>
                    <select class="form-control form-select" name="participant_ID" id="participant_ID" required <?= $result->rowCount() === 0 ? 'disabled' : '' ?>>
                        <option value="" disabled hidden selected><?= $result->rowCount() === 0 ? '"There are no participants in this study!"' : 'Participant Name' ?></option>
                        <?php
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)){ 
                            $iv = hex2bin($row['iv']);
                            $name = Crypto::decrypt($row['anonymous_name'], $iv);
                            $participant_ID = Crypto::encrypt($row['participant_id'], $id_IV); ?>
                                <option value="<?= $participant_ID ?>;<?= bin2hex($id_IV) ?>">
                                    <?= $name . " - " . $row['dob'] ?>
                                </option>
                    <?php } ?>
                    </select>
                </div>
            </div>
            <?php if ($result->rowCount()) { ?>
                <div class="form-group">
                    <button type="submit" name="removeParticipant" class="btn btn-success">Submit</button>
                </div>
            <?php } ?>
        </form>
    </div>
</div>

<?php
  include 'inc/footer.php';
?>