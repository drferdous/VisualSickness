<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();

$db = Database::getInstance();
$pdo = $db->pdo;


if (Session::get('study_ID') == 0) {
    header('Location: study_list');
    exit();
}
$study_ID = Session::get('study_ID');

Session::requirePI($study_ID, $pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['removeResearcher']) && Session::CheckPostID($_POST)) {
    $info = explode(';', $_POST['researcher_ID']);
    $researcher_ID = $info[0];
    $iv = $info[1];
    $researcher_ID = Crypto::decrypt($researcher_ID, hex2bin($iv));
    $removeResearcher = $studies->removeResearcher($researcher_ID);
    echo $removeResearcher;?>
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
        <h3 class="float-left">Remove A Researcher</h3>
        <span class="float-right"><a href="study_details" class="btn btn-primary">Back</a></span>
    </div>
    <div class="card-body pr-2 pl-2">
        <form class="" action="" method="post">
            <?php 
                $rand = bin2hex(openssl_random_pseudo_bytes(16));
                Session::set("post_ID", $rand);
            ?>
            <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
            <div style="margin-block: 6px;">
                <small style='color: red'>
                    * Required Field
                </small>
            </div>
            <div class="form-group">
                <div class="form-group">
                    <label for="researcher_ID" class="required">Remove a Researcher</label>
                    <?php
                    
                    $sql = "SELECT id, name
                            FROM tbl_users
                            WHERE id IN (SELECT researcher_ID
                                         FROM Researcher_Study
                                         WHERE study_ID = $study_ID
                                         AND is_active = 1
                                         AND NOT researcher_ID = " . Session::get('id') . ");";
                    $result = $pdo->query($sql); ?>
                    <select class="form-control" name="researcher_ID" id="researcher_ID" required <?= $result->rowCount() === 0 ? 'disabled' : '' ?>>
                        <option value="" disabled hidden selected><?= $result->rowCount() === 0 ? 'There are no researchers you can remove from this study!' : 'Researcher Name' ?></option>
                        <?php
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                            $enc_id = Crypto::encrypt($row['id'], $iv); ?>
                            <option value="<?= $enc_id ?>;<?= bin2hex($iv) ?>"><?= $row['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php if ($result->rowCount()) { ?>
                <div class="form-group">
                    <button type="submit" name="removeResearcher" class="btn btn-success">Submit</button>
                </div>
                <?php } ?>
        </form>
    </div>
</div>


<?php
  include 'inc/footer.php';
?>