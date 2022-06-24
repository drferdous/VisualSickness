<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();
$db = Database::getInstance();
$pdo = $db->pdo;

if (Session::get("study_ID") == 0){
    header("Location: study_list");
    exit();
}
Session::requirePI(Session::get('study_ID'), $pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editResearcher']) && Session::CheckPostID($_POST)) {
    $info = explode(';', $_POST['researcher_ID']);
    $researcher_ID = $info[0];
    $iv = $info[1];
    $researcher_ID = Crypto::decrypt($researcher_ID, hex2bin($iv));
    $editResearcher = $studies->editResearcher($researcher_ID, $_POST['study_role']);
}
if (!isset($_POST['referrer'])) {
    if (isset($_SERVER['HTTP_REFERER'])) $referrer = ltrim(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH), '/') . '?' . parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
    else $referrer = 'study_details';
}
else $referrer = $_POST['referrer'];

if (isset($editResearcher)) {
    echo $editResearcher;?>
    <script type="text/javascript">
        const divMsg = document.getElementById("flash-msg");
        if (divMsg.classList.contains("alert-success")){
            setTimeout(function(){
                location.href = '<?= $referrer ?>';
            }, 1000);
        }
    </script>
<?php } ?>

<div class="card">
    <div class="card-header">
        <h3 class="float-left">Edit A Researcher</h3>
        <span class="float-right"> <a href='<?= $referrer ?>' class="backBtn btn btn-primary">Back</a></span>
    </div>
    <div class="card-body pr-2 pl-2">
        <form class="" action="" method="post">
            <?php 
                $rand = bin2hex(openssl_random_pseudo_bytes(16));
                Session::set("post_ID", $rand);
            ?>
            <input type="hidden" name="randCheck" value="<?= $rand; ?>">
            <input type="hidden" name="referrer" value="<?= $referrer ?>">
            <div style="margin-block: 6px;">
                <small style='color: red'>
                    * Required Field
                </small>
            </div>
            <div class="form-group">
                <div class="form-group">
                    <label for="researcher_ID" class="required">Edit A Researcher</label>
                    <?php
                    $sql = "SELECT id, name, email
                            FROM tbl_users
                            WHERE id IN (SELECT researcher_ID 
                                             FROM Researcher_Study
                                             WHERE study_ID = " . Session::get("study_ID") . 
                                             " AND is_active = 1 AND study_role != 2)
                            AND status = 1
                            AND affiliationid = " . Session::get("affiliationid") . ";";
                    $result = $pdo->query($sql); ?>
                    <select class="form-control" name="researcher_ID" id="researcher_ID" required <?= $result->rowCount() === 0 ? 'disabled' : '' ?>>
                        <option value="" disabled hidden selected><?= $result->rowCount() === 0 ? 'There are no other researchers in this study!' : 'Researcher Name' ?></option>
                        <?php
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                                $enc_id = Crypto::encrypt($row['id'], $iv); ?>
                                <option value="<?= $enc_id ?>;<?= bin2hex($iv) ?>"><?= $row["name"] . " (" . $row["email"] . ")" ?></option>
                        <?php } ?>
                    </select>
                    <br>
                    <label for="study_role" class="required">Select Study Role</label>
                    <select class="form-control" name="study_role" id="study_role" required disabled>
                        <option value="" selected hidden disabled>Study Role</option>
                    </select> 
                </div>
            </div>
            <?php if ($result->rowCount()) { ?>
                <div class="form-group">
                    <button type="submit" name="editResearcher" class="btn btn-success">Submit</button>
                </div>
            <?php } ?>
        </form>
    </div>
</div>
      
<script type="text/javascript">
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