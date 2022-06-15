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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['removeResearcher'])) {
    $removeResearcher = $studies->removeResearcher($_POST);
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
        <h3>Remove A Researcher<span class="float-right"><a href="study_details" class="btn btn-primary">Back</a></span></h3> 
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
                    <label for="researcher_ID" class="required">Remove a Member</label>
                    <select class="form-control" name="researcher_ID" id="researcher_ID" required>
                        <option value="" disabled hidden selected>Member Name</option>
                    <?php
                        $sql = "SELECT id, name
                                FROM tbl_users
                                WHERE id IN (SELECT researcher_ID
                                             FROM Researcher_Study
                                             WHERE study_ID = $study_ID
                                             AND is_active = 1
                                             AND NOT researcher_ID IN (SELECT created_by
                                                                       FROM Study
                                                                       WHERE study_ID = $study_ID)
                                             AND NOT researcher_ID = " . Session::get('id') . ");";
                        $result = $pdo->query($sql);
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                    <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                 <button type="submit" name="removeResearcher" class="btn btn-success">Submit</button>
            </div>
        </form>
    </div>
</div>


<?php
  include 'inc/footer.php';
?>