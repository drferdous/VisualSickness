<?php
include 'inc/header.php';
include_once 'database.php';

Session::CheckSession();

?>

<div class="card ">
    <div class="card-header">
        <?php if (Session::get('roleid') == '1' || Session::get('roleid') == '2') { ?>
            <h3>Manage Sessions <span class="float-right">        
        <?php  } ?> 
        </div>
  </h3> 
        </strong></span></h3>
    </div>
        
    <div class="card-body pr-2 pl-2">
    <?php
        if (Session::get('roleid') === '1'){
            $sql = "SELECT session_ID FROM Session";
        }
        else if (Session::get('roleid') === '2' || Session::get('roleid') === '3' || Session::get('roleid') === '4'){
            $sql = "SELECT study_ID
                    FROM Researcher_Study
                    WHERE researcher_ID = " . Session::get('id');
            $studyIDList = mysqli_query($conn, $sql);
            
            $sql = "SELECT study_ID, full_name
                    FROM Study
                    WHERE ("; 
            while ($studyIDRow = mysqli_fetch_assoc($studyIDList)){
                $sql = $sql . "study_ID = " . $studyIDRow['study_ID'] . " OR ";
            }
            $sql = $sql . " FALSE)
                   AND (is_active = 0);";
        }
        
        $result = mysqli_query($conn, $sql);
            
        if (mysqli_num_rows($result) > 0){
    ?>
        <br />
            <table class="table table-striped table-bordered" id="example">
                <thead class="text-center">
                    <tr>
                        <th>Session ID</th>
                        <th>Action</th>
                    </tr>
                </thead>
                    
                <tbody>
                <?php
                    while ($row = mysqli_fetch_assoc($result)) { 
                        echo "<tr>";
                        
                        echo "<td>" . $row['session_ID'] ."</td>";
                        
                        echo "<td>";
                        echo "<a class='btn-success btn-sm' href=\"session_details.php?session_ID=" . $row['session_ID'] . "\">Session Details</a>";                        
                        echo "</td>";
                        
                        echo "</tr>";
                    }
                ?>
                </tbody>
            </table>
    <?php } 
        else{
            echo "<p>You have no sessions!</p>";
        }
    ?>
    </div>
</div>

<?php
  include 'inc/footer.php';
?>