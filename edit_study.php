<?php
include 'inc/header.php';
include_once 'lib/Database.php';
$db = Database::getInstance();
$pdo = $db->pdo;

Session::CheckSession();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && (!isset($_POST['study_ID'])) || $_POST['study_ID'] == 'undefined') {
    header('Location: view_study');
    exit();
}
?>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateStudy'])) {
    $updateStudy = $studies->updateStudy($_POST);
    if (isset($updateStudy)){
        echo $updateStudy; ?>
        <script type="text/javascript">
            const divMsg = document.getElementById("flash-msg");
            if (divMsg.classList.contains("alert-success")){
                setTimeout(function(){
                    location.href = "view_study";
                }, 1000);
            }
        </script>
<?php }
}
?>

<div class="card ">
    <div class="card-header">
        <h3 class="text-center">Edit a Study
            <a href="study_details" class="btn btn-primary float-right" data-study_ID="<?php echo $_POST['study_ID']; ?>">Back</a>
        </h3>
    </div>
    
    <?php
        $sql = "SELECT study_ID, full_name, short_name, IRB, description FROM Study WHERE study_ID = " . $_POST['study_ID'] . " LIMIT 1;";
        $result = $pdo->query($sql);
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $study_ID = $row['study_ID'];
            $full_name = $row['full_name'];
            $short_name = $row['short_name'];      
            $IRB = $row['IRB'];
            $description = $row['description'];
        }
        $ssq_times = array();
        $ssq_times_sql = "SELECT name FROM SSQ_times WHERE is_active = 1 AND study_id = " . $_POST['study_ID'];
        $ssq_times_result = $pdo->query($ssq_times_sql);
        while ($row = $ssq_times_result->fetch(PDO::FETCH_ASSOC)) {
            array_push($ssq_times, $row['name']);
        }
    ?>
    <div class="card-body pr-2 pl-2">
        <form class="" action="edit_study" method="POST", id="submit_form">
            <div style="margin-block: 6px;">
                <small style='color: red'>
                    * Required Field
                </small>
            </div>
            <input type="hidden" name="study_ID" value=<?php echo $study_ID; ?>>
            <div class="form-group">
                <label for="full_name" class="required">Full Name </label>
                <input type="text" name="full_name" value="<?php echo $full_name;?>"  class="form-control" id="full_name" required>
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


<script type="text/javascript">
    $(document).ready(function(){
       $(document).on("click", "a", redirectUser);
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
        
        form.appendChild(hiddenInput);
        document.body.appendChild(form);
        form.submit();
        
        return false;
    }
</script>
<?php
    include 'inc/footer.php';
?>