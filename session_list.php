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
        $sql = "SELECT session_ID, start_time, participant_ID FROM Session WHERE study_ID = " . $_GET["study_ID"];
        $result = mysqli_query($conn, $sql);
            
        if (mysqli_num_rows($result) > 0){
    ?>
        <br />
            <table class="table table-striped table-bordered" id="example">
                <thead class="text-center">
                    <tr>
                        <th>Session ID</th>
                        <th>Participant Name</th>
                        <th>Start Time</th>                        
                        <th>Action</th>
                    </tr>
                </thead>
                    
                <tbody>
        
                <?php
                    while ($row = mysqli_fetch_assoc($result)) { 
                        echo "<tr>";
                        
                        echo "<td>" . $row['session_ID'] ."</td>";
                        
                        if (isset($row['participant_ID'])){
                            $sql_users = "SELECT anonymous_name FROM Participant WHERE participant_id = " . $row['participant_ID'] . " LIMIT 1;";
                            $result_users = mysqli_query($conn, $sql_users);
                            $row_users = mysqli_fetch_assoc($result_users);

                            echo "<td>" . $row_users['anonymous_name'] . "</td>";
                        } else {
                            echo "<td>-</td>";
                        }                             

                        echo "<td>" .  $row['start_time']     . "</td>";                           
                    
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