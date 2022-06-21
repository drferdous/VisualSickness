<?php
include 'inc/header.php';
include_once 'lib/Database.php';
$db = Database::getInstance();
$pdo = $db->pdo;

Session::CheckSession();

if (Session::get('study_ID') == 0) {
    header('Location: view_study');
    exit();
}
$study_ID = Session::get('study_ID');
Session::requirePI($study_ID, $pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateStudy']) && Session::CheckPostID($_POST)) {
    $updateStudy = $studies->updateStudy($_POST);
    if (isset($updateStudy)){
        echo $updateStudy; ?>
        <script type="text/javascript">
            const divMsg = document.getElementById("flash-msg");
            if (divMsg.classList.contains("alert-success")){
                setTimeout(function(){
                    location.href = "study_details";
                }, 1000);
            }
        </script>
<?php }
}
?>

<div class="card ">
    <div class="card-header">
        <h3 class="text-center">Edit a Study
            <a href="study_details" class="btn btn-primary float-right">Back</a>
        </h3>
    </div>
    
    <?php
        $sql = "SELECT study_ID, full_name, short_name, IRB, description FROM Study WHERE study_ID = $study_ID LIMIT 1;";
        $result = $pdo->query($sql);
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $study_ID = $row['study_ID'];
            $full_name = $row['full_name'];
            $short_name = $row['short_name'];      
            $IRB = trim($row['IRB']);
            $description = $row['description'];
        }
        $ssq_times = array();
        $ssq_times_sql = "SELECT name FROM SSQ_times WHERE is_active = 1 AND study_id = $study_ID;";
        $ssq_times_result = $pdo->query($ssq_times_sql);
        while ($row = $ssq_times_result->fetch(PDO::FETCH_ASSOC)) {
            array_push($ssq_times, $row['name']);
        }
    ?>
    <div class="card-body pr-2 pl-2">
        <form class="" action="" method="POST", id="submit_form">
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
                <label for="full_name" class="required">Full Name </label>
                <input type="text" name="full_name" value="<?php echo $full_name;?>" class="form-control" id="full_name" required>
            </div>
            <div class="form-group">
                <label for="short_name" class="required">Short Name</label>
                <input type="text" name="short_name" value="<?php echo $short_name;?>" class="form-control" id="short_name" required>
            </div>
            <div class="form-group">
                <label for="IRB" class="required">IRB</label>
                <input type="text" name="IRB" value="<?php echo $IRB;?>" class="form-control" id="IRB" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" name="description" value="<?php echo $description;?>" class="form-control" id="description">
            </div>
            <div class="form-group">
                <label for="ssq_times">SSQ times (comma-separated)</label>
                <input type="text" name="ssq_times" value="<?= implode(', ', $ssq_times); ?>" class="form-control" id="ssq_times">
            </div>
            <div class="form-group">
                 <button type="submit" name="updateStudy" class="btn btn-success">Submit</button>
            </div>
        </form>
    </div>
</div>

<?php
    include 'inc/footer.php';
?>