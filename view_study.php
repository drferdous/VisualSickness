<?php
include 'inc/header.php';
include_once 'database.php';

Session::CheckSession();

if (isset($insert_study)) {
  echo $insert_study;
}

?>

<div class="card ">
    <div class="card-header">
        <h3>Study List</h3>         
    </div>
    <div class="card-body pr-2 pl-2">
    <?php
        if (Session::get('roleid') === '1'){
            $sql = "SELECT study_ID, full_name, created_at
                    FROM Study
                    WHERE is_active = 1;";
                    
            $result = mysqli_query($conn, $sql);
        }
        else{
            $sql = "CREATE TABLE `#StudiesAndRoles`(
                        study_ID INT(11) NOT NULL,
                        study_role TINYINT(4) DEFAULT NULL
                    );";
            $result = mysqli_query($conn, $sql);
            
            if (!$result){
                echo mysqli_error($conn);
            }
            
            $sql =  "CREATE TABLE `#StudiesAndNames`(
                        study_ID INT(11) NOT NULL,
                        full_name TEXT NOT NULL,
                        created_at TIMESTAMP NOT NULL,
                        is_active TINYINT(1) NOT NULL
                    );";
            $result = mysqli_query($conn, $sql);
            
            if (!$result){
                echo mysqli_error($conn);
            }
        
            $sql = "INSERT INTO `#StudiesAndRoles` (study_ID, study_role)
                    SELECT study_ID, study_role
                    FROM Researcher_Study
                    WHERE Researcher_ID = " . Session::get('id') . ";";
            $result = mysqli_query($conn, $sql);
            
            if (!$result){
                echo mysqli_error($conn);
            }
            
            $sql = "SELECT * 
                    FROM `#StudiesAndRoles`;";
            $studyIDList = mysqli_query($conn, $sql);
            
            if (!$studyIDList){
                echo mysqli_error($conn);
            }
            
            $sql = "INSERT INTO `#StudiesAndNames` (study_ID, full_name, created_at, is_active)
                    SELECT study_ID, full_name, created_at, is_active
                    FROM Study
                    WHERE ("; 
            while ($studyIDRow = mysqli_fetch_assoc($studyIDList)){
                $sql = $sql . "study_ID = " . $studyIDRow['study_ID'] . " OR ";
            }
            $sql = $sql . " FALSE);";
            $result = mysqli_query($conn, $sql);
            
            if (!$result){
                echo mysqli_error($conn);
            }
            
            $sql = "SELECT `#StudiesAndNames`.study_ID, `#StudiesAndNames`.full_name, `#StudiesAndNames`.created_at,             `#StudiesAndNames`.is_active, `#StudiesAndRoles`.study_role       
                    FROM `#StudiesAndNames` 
                    INNER JOIN `#StudiesAndRoles` 
                    ON `#StudiesAndNames`.study_ID = `#StudiesAndRoles`.study_ID;";
            $result = mysqli_query($conn, $sql);
            
            if (!$result){
                echo mysqli_error($conn);
            }
            
            $sql = "DROP TABLE IF EXISTS `#StudiesAndRoles`;";
            mysqli_query($conn, $sql);
            $sql = "DROP TABLE IF EXISTS `#StudiesAndNames`;";
            mysqli_query($conn, $sql);
        }
        
        if (mysqli_num_rows($result) > 0){
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
                    
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)){ ?>
                    <?php if ($row["is_active"] === '1'){
                            $classToSet = "active";
                            $displayToSet = "";
                          } 
                          else{
                            $classToSet = "inactive";
                            $displayToSet = "none";
                          } 
                    ?>
                        <tr class="<?php echo $classToSet; ?>" style="display: <?php echo $displayToSet; ?>;">
                            <td><?php echo $row['full_name']; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <a class="btn-success btn-sm" href="study_details" data-study_ID="<?php echo $row['study_ID']; ?>">Study Details</a>
                        
                        <?php if ((Session::get('roleid') === '1' || (isset($row['study_role']) && ($row['study_role'] === '2' || $row['study_role'] === '3'))) && $row["is_active"] === '1'){ ?>
                                <br>
                                <br>
                                <a class="btn-success btn-sm" href="create_session" data-study_ID="<?php echo $row['study_ID']; ?>" >Create Session</a>
                        <?php } ?>
                
                            <br>
                            <br>
                            <a class="btn-success btn-sm" href="session_list" data-study_ID="<?php echo $row['study_ID']; ?>">Session List</a>                       
                            </td>
                        </tr>
                <?php } ?>
                </tbody>
            </table>
            <br>
            <div class="form-check form-switch float-right">
                <input class="form-check-input" type="checkbox" id="show-studies" checked>
                <label class="form-check-label" for="show-studies">Show Active Studies Only</label>
            </div>
    <?php } 
        else{
            echo "<p>You have no studies!</p>";
        }
    ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on("click", "#show-studies", showCorrectStudies);
        $(document).on("click", "a[data-study_ID]", redirectUser);
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
    };
    
    function showCorrectStudies(){
        let inactiveStudies = document.querySelectorAll("tr + .inactive");
        let displayToSet = "";
        if (document.getElementById("show-studies").checked){
            displayToSet = "none";
        }
        for (let i = 0; i < inactiveStudies.length; i++){
            inactiveStudies[i].style.display = displayToSet;
        }
    };
</script>
<?php
  include 'inc/footer.php';
?>