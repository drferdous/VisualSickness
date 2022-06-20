<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();
$db = Database::getInstance();
$pdo = $db->pdo;

if (Session::get("study_ID") == 0){
    header("Location: view_study");
    exit();
}
Session::requirePI(Session::get('study_ID'), $pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addResearcher'])) {
    $addResearcher = $studies->addResearcher($_POST);
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
        <h3><span class="float-left">Add A Researcher</span>
            <a href="study_details" 
               class="btn btn-primary float-right redirectUser">
               Back
            </a>
        </h3>  
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
                    <label for="researcher_ID" class="required">Add A Researcher</label>
                    <?php
                    $sql = "SELECT id, name, email
                            FROM tbl_users
                            WHERE NOT id IN (SELECT researcher_ID 
                                             FROM Researcher_Study
                                             WHERE study_ID = " . Session::get("study_ID") . 
                                             " AND is_active = 1)
                            AND status = 1
                            AND affiliationid = " . Session::get("affiliationid") . ";";
                    $result = $pdo->query($sql); ?>
                    <select class="form-control" name="researcher_ID" id="researcher_ID" required <?= $result->rowCount() === 0 ? 'disabled' : '' ?>>
                        <option value="" disabled hidden selected><?= $result->rowCount() === 0 ? 'There are no researchers you can add to this study!' : 'Researcher Name' ?></option>
                        <?php
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row["name"] . " (" . $row["email"] . ")"; ?></option>
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
                    <button type="submit" name="addResearcher" class="btn btn-success">Submit</button>
                </div>
            <?php } ?>
        </form>
    </div>
</div>
      
<script type="text/javascript">
     $(document).ready(function() {
         $('#researcher_ID').change(function() {
             var researcher_ID = $(this).val();
            $.ajax({
              url :"researcherrole",
              type:"POST",
              cache:false,
              data:{researcher_ID:researcher_ID},
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