<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();
$db = Database::getInstance();
$pdo = $db->pdo;

Session::requireStudyID();
Session::requirePI(Session::get('study_ID'), $pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addResearcher']) && Session::CheckPostID($_POST)) {
    if (isset($_POST['researcher_ID']) && isset($_POST['study_role'])){
        $info = explode(';', $_POST['researcher_ID']);
        $researcher_ID = $info[0];
        $iv = $info[1];
        $researcher_ID = Crypto::decrypt($researcher_ID, hex2bin($iv));
        $addResearcher = $studies->addResearcher($researcher_ID, $_POST['study_role']);
    }
    else{
        echo Util::generateErrorMessage("No researcher or study role given.");
    }
}

if (isset($addResearcher)) {
    echo $addResearcher;?>
    <script type="text/javascript">
        const divMsg = document.getElementById("flash-msg");
        if (divMsg.classList.contains("alert-success")){
            setTimeout(function(){
                let redirectURL = "study_details";
                location.href = redirectURL;
            }, 1000);
        }
    </script>
<?php } ?>

<div class="card">
    <div class="card-header">
        <h1 class="float-left">Add A Researcher</h1>
        <a href="study_details"
            class="backBtn btn btn-primary float-right">
            Back
        </a>
    </div>
    <div class="card-body pr-2 pl-2">
        <form class="" action="add_researcher" method="post">
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
                    <label for="researcher_ID" class="required">Add A Researcher</label>
                    <?php
                    $sql = "SELECT user_id, name, email
                            FROM users
                            WHERE NOT user_id IN (SELECT researcher_id 
                                             FROM researchers
                                             WHERE study_id = " . Session::get("study_ID") . 
                                             " AND is_active = 1)
                            AND status = 1
                            AND affiliation_id = " . Session::get("affiliationid") . ";";
                    $result = $pdo->query($sql); ?>
                    <select class="form-control form-select" name="researcher_ID" id="researcher_ID" required <?= $result->rowCount() === 0 ? 'disabled' : '' ?>>
                        <option value="" disabled hidden selected><?= $result->rowCount() === 0 ? 'There are no researchers you can add to this study!' : 'Researcher Name' ?></option>
                        <?php
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                            $enc_id = Crypto::encrypt($row['user_id'], $iv); ?>
                                <option value="<?= $enc_id ?>;<?= bin2hex($iv) ?>"><?= $row["name"] . " (" . $row["email"] . ")"; ?></option>
                        <?php } ?>
                    </select>
                    <br>
                    <label for="study_role" class="required">Select Study Role</label>
                    <select class="form-control form-select" name="study_role" id="study_role" required disabled>
                        <option value="" selected hidden disabled>Study Role</option>
                    </select> 
                </div>
            </div>
            <?php if ($result->rowCount()) { ?>
                <div class="form-group">
                    <button type="submit" name="addResearcher" class="btn btn-success">Submit</button>
                </div>
            <?php } ?>
        </form>
    </div>
</div>
      
<script>
     $(document).ready(function() {
         $('#researcher_ID').change(function() {
            const info = $(this).val();
            const researcher_ID = info.split(';')[0];
            const iv = info.split(';')[1];
            $.ajax({
                url: "researcher_role",
                type: "POST",
                cache: false,
                data: {
                    researcher_ID,
                    iv
                },
                success:function(data){
                    let studyRoleSelector = document.getElementById("study_role");
                    $(studyRoleSelector).html(data);
                    studyRoleSelector.removeAttribute("disabled");
                }
            });	
         });
     });
 </script>

<?php
  include 'inc/footer.php';
?>