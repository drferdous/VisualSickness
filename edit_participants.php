<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();
$db = Database::getInstance();
$pdo = $db->pdo;
if (!isset($_POST['referrer'])) {
    if (isset($_SERVER['HTTP_REFERER'])) $referrer = ltrim(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH), '/') . '?' . parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
    else $referrer = 'study_details';
}
else $referrer = $_POST['referrer'];
if (Session::get('participant_ID')) $participant_ID = Session::get('participant_ID');
if (isset($_POST["participant_ID"]) && isset($_POST["iv"])){
    $iv = hex2bin($_POST["iv"]);
    $participant_ID = Crypto::decrypt($_POST["participant_ID"], $iv);
    Session::set('participant_ID', $participant_ID);
}
if (isset($_POST['forStudy']) && $_POST['forStudy'] === 'true') {
    $study_ID = Session::get('study_ID');
    Session::requirePIorRA($study_ID, $pdo);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editParticipant']) && Session::CheckPostID($_POST)) {
    if (isset($_POST["participant_ID"])){
        $info = explode(';', $_POST['participant_ID']);
        $participant_ID = $info[0];
        $iv = $info[1];
        $participant_ID = Crypto::decrypt($participant_ID, hex2bin($iv));
        $updateStudy = $studies->editParticipant($participant_ID, $_POST);
        if (isset($updateStudy)) {
            echo $updateStudy; ?>
            <script type="text/javascript">
                const divMsg = document.getElementById("flash-msg");
                if (divMsg?.classList.contains("alert-success")){
                    setTimeout(function(){
                        location.href = 'participant_list';
                    }, 1000);
                }
            </script>
    <?php }
    }
    else{
        echo Util::generateErrorMessage("No participant was selected.");
    }
}
?>

<div class="card">
    <div class="card-header">
        <h1 class="float-left mb-0">Edit A Participant</h1>
        <span class="float-right"><a href='<?= $referrer ?>' class="backButton btn btn-primary">Back</a></span>
    </div>
    <div class="card-body pr-2 pl-2">
        <form class="" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="post">
            <?php 
                $rand = bin2hex(openssl_random_pseudo_bytes(16));
                Session::set("post_ID", $rand);
            ?>
            <input type="hidden" name="randCheck" value="<?= $rand; ?>">
            <input type="hidden" name="referrer" value="<?= $referrer ?>">
            <div style="margin-block: 6px;">
                <small class="required-msg">
                    * Required Field
                </small>
            </div>
            <div class="form-group">
                <label for="participant_ID" class="required">Choose A Participant</label>
                <?php
                $sql = "SELECT participants.anonymous_name, participants.participant_id, participants.iv FROM participants
                        JOIN study ON study.study_id = participants.study_id
                        JOIN researchers ON researchers.study_id = study.study_id AND researchers.researcher_id = " . Session::get('id') . "
                        WHERE participants.is_active = 1 " . 
                        (isset($study_ID) ? "AND study.study_id = $study_ID" : '') . "
                        AND researchers.study_role <= 3
                        AND researchers.is_active = 1;";
                $result = $pdo->query($sql);
                ?>
                <select class="form-control form-select" name="participant_ID" id="participant_ID" <?= (!isset($participant_ID) || !$result->rowCount()) ? 'required' : '' ?> <?= $result->rowCount() === 0 ? 'disabled' : '' ?>>
                    <?php if (!isset($participant_ID) || !$result->rowCount()) { ?>
                        <option value="" disabled hidden selected><?= $result->rowCount() === 0 ? (isset($study_ID) ? 'There are no participants in this study!' : 'You do not have editing privileges on any participants.') : 'Participant Name' ?></option>
                    <?php }
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                            $enc_id = Crypto::encrypt($row['participant_id'], $iv); ?>
                            <option <?= (isset($participant_ID) && $row['participant_id'] == $participant_ID) ? 'selected' : '' ?> value="<?= $enc_id . ';' . bin2hex($iv) ?>"><?= Crypto::decrypt($row["anonymous_name"], hex2bin($row['iv'])) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div id="data-holder"></div>
            <div class="form-group">
                <input type="submit" class="btn btn-success" name="editParticipant" value="Update">
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(() => {
        $('#participant_ID').change(function () {
            getData($(this));
        });
        $('.backButton').on('click', function () {
            if ($('form')[0] && $('form').serialize().split('&').map(r => r.split('=')).filter(r => !['randCheck', 'referrer'].includes(r[0])).some((r, i) => r[1] != startVals[r[0]])) {
                return confirm('Are you sure you want to go back? Your data will not be saved.');
            }

        });
    });
    let startVals = [];
    
    const getData = (t) => {
        if (t.val()) $.ajax({
            url: "participant_info",
            type: "POST",
            cache: false,
            data: {
                participant_ID: t.val().split(';')[0],
                iv: t.val().split(';')[1]
            },
            success:function(data){
                $('#data-holder').html(data);
                startVals = Object.fromEntries($('form').serialize().split('&').map(r => r.split('=')).filter(r => !['randCheck', 'referrer'].includes(r[0])));
                $('select').each(function () {
                    if (!$(this).val()) startVals[$(this).attr('name')] = '';
                });
            }
        });
    }
    getData($('#participant_ID'));
</script>
<?php include 'inc/footer.php' ?>