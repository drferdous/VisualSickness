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
        <?php if (Session::get('roleid') == '1' || Session::get('roleid') == '2') { ?>
            <h3>Manage Studies <span class="float-right">        
            <a href="add_researcher.php" class="btn btn-primary">Add A Researcher</a> 
            <a href="remove_researcher.php" class="btn btn-primary">Remove A Researcher</a> 
        <?php  } ?> 
        
        <?php if (Session::get('roleid') == '3' || Session::get('roleid') == '4') { ?>
            <h3>View Studies <span class="float-right">        
        <?php  } ?>         
        </div>
  </h3> 
        </strong></span></h3>
    </div>
        
    <div class="card-body pr-2 pl-2">
    <?php
        if (Session::get('roleid') === '1'){
            $sql = "SELECT study_ID, full_name 
                    FROM Study
                    WHERE is_active = 1;";
                    
            $result = mysqli_query($conn, $sql);
        }
        else{
            
            $sql = "CREATE TABLE `#StudiesAndRoles`(
                        study_ID INT(11) NOT NULL,
                        study_role TINYINT(4) DEFAULT NULL
                    );";
            mysqli_query($conn, $sql);
            
            $sql =  "CREATE TABLE `#StudiesAndNames`(
                        study_ID INT(11) NOT NULL,
                        full_name TEXT NOT NULL
                    );";
            mysqli_query($conn, $sql);
        
            $sql = "INSERT INTO `#StudiesAndRoles` (study_ID, study_role)
                    SELECT study_ID, study_role
                    FROM Researcher_Study
                    WHERE Researcher_ID = " . Session::get('id') . ";";
            mysqli_query($conn, $sql);
            
            $sql = "SELECT * 
                    FROM `#StudiesAndRoles`;";
            $studyIDList = mysqli_query($conn, $sql);
            
            $sql = "INSERT INTO `#StudiesAndNames` (study_ID, full_name)
                    SELECT study_ID, full_name
                    FROM Study
                    WHERE ("; 
            while ($studyIDRow = mysqli_fetch_assoc($studyIDList)){
                $sql = $sql . "study_ID = " . $studyIDRow['study_ID'] . " OR ";
            }
            $sql = $sql . " FALSE)
                   AND (is_active = 1);";
            mysqli_query($conn, $sql);
            
            $sql = "SELECT `#StudiesAndNames`.study_ID, `#StudiesAndNames`.full_name, `#StudiesAndRoles`.study_role       FROM `#StudiesAndNames` 
                    INNER JOIN `#StudiesAndRoles` 
                    ON `#StudiesAndNames`.study_ID = `#StudiesAndRoles`.study_ID;";
            $result = mysqli_query($conn, $sql);
            
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
                        <th>Action</th>
                    </tr>
                </thead>
                    
                <tbody>
                <?php
                    while ($row = mysqli_fetch_assoc($result)) { 
                        echo "<tr>";
                        
                        echo "<td>" . $row['full_name'] ."</td>";
                        
                        echo "<td>";
                        echo "<a class='btn-success btn-sm' href=\"study_details.php?study_ID=" . $row['study_ID'] . "\">Study Details</a>";
                        
                        if (Session::get('roleid') === '1' || (isset($row['study_role']) && ($row['study_role'] === '2' || $row['study_role'] === '3'))){
                            echo "<br>";
                            echo "<br>";
                            echo "<a class='btn-success btn-sm' href=\"create_session.php?study_ID=" . $row['study_ID'] . "\">Create Session</a>";
                        }
                
                        echo "<br>";
                        echo "<br>";
                        echo "<a class='btn-success btn-sm' href=\"session_list.php?study_ID=" . $row['study_ID'] . "\">Session List</a>";                        
                        echo "</td>";
                        
                        echo "</tr>";
                    }
                ?>
                </tbody>
            </table>
    <?php } 
        else{
            echo "<p>You have no studies!</p>";
        }
    ?>
    </div>
</div>

<?php
  include 'inc/footer.php';
?>