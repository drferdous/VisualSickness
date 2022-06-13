<?php
include 'inc/header.php';
include_once 'lib/Database.php';

$db = Database::getInstance();
$pdo = $db->pdo;

Session::CheckSession();

if (isset($insert_study)) {
  echo $insert_study;
}

?>

<div class="card">
    <div class="card-header">
        <h3>Study List</h3>         
    </div>
    <div class="card-body pr-2 pl-2">
    
    
    <?php
        
        if (Session::get('roleid') == '1'){
            $sql = "SELECT study_ID, full_name, created_at, is_active
                    FROM Study
                    WHERE is_active = 1
                    AND created_by IN (SELECT id FROM tbl_users WHERE affiliationid = " . Session::get("affiliationid") . ");";
                    
            $result = $pdo->query($sql);
        }
        else if (Session::get('roleid') != '1') {
            $sql = "SELECT Study.study_ID, Study.full_name, Study.created_at, Study.is_active, Researcher_Study.study_role
                FROM Study, Researcher_Study
                WHERE Study.study_ID IN (SELECT study_ID
                                         FROM Researcher_Study
                                         WHERE researcher_ID = " . Session::get("id") . "
                                         AND is_active = 1)
                AND Study.study_ID = Researcher_Study.study_ID
                AND Researcher_Study.researcher_ID = " . Session::get("id") . "
                AND Researcher_Study.is_active = 1;";
                    
            $result = $pdo->query($sql);
        }
        
    if ($result->rowCount() > 0) {
    ?>
        <br />
            <table class="table table-striped table-bordered" id="example">
                <thead class="text-center">
                    <tr>
                        <th>Study Name</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                    
                <tbody id="study-contents">
                </tbody>
            </table>
            <br>
            <div class="form-check form-switch float-right">
                <input class="form-check-input" type="checkbox" id="show-studies" checked>
                <label class="form-check-label" for="show-studies">Show Active Studies Only</label>
            </div>
    <?php } 
        else{
            echo "<br>"; 
            echo "<br>";             
            echo "<p>You have no studies!</p>";
        }
    ?>
    </div>
</div>

<script type="text/javascript">
    let activeStatus = 'active';
    let idToSearch = <?php echo Session::get("id"); ?>;;
    $(document).ready(function(){
        $(document).on("click", "#show-studies", function() {
            if ($(this).prop("checked")){
                activeStatus = "active";
            }
            else{
                activeStatus = "all";
            }
            getData();
        });
        $(document).on("click", "a[data-study_ID]", redirectUser);
    });
    
    function getData() {
        $.ajax({
           url: "loadCorrectStudies",
           method: "POST",
           cache: false,
           data:{
               activeStatus: activeStatus,
               idToSearch: idToSearch
           },
           success: function(data){
               $("#example").DataTable().destroy();
               $("#study-contents").html(data);
               $("#example").DataTable();
           }
        });
    }
        
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
    };
    
    getData();
</script>
<?php
  include 'inc/footer.php';
?>