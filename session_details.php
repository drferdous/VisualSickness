<?php
include 'inc/header.php';
include_once 'database.php';

Session::CheckSession();

if (isset($_POST['restart-session-btn'])){
    $startSessionMessage = $users->restart_session($_GET['session_ID']);
    if (isset($startSessionMessage)){
        echo $startSessionMessage;
    }
}

if (isset($_POST['end-session-btn'])){
    $endSessionMessage = $users->endSession($_GET['session_ID']);
    if (isset($endSessionMessage)){
        echo $endSessionMessage;
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3>Session Details
            <?php
            $sql = "SELECT end_time
                    FROM Session
                    WHERE session_ID = " . $_GET['session_ID'] . "
                    LIMIT 1;";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            
            if (!isset($row['end_time'])){?>
                <a href="chooseQuiz.php?session_ID=<?php echo $_GET['session_ID']; ?>" class="btn btn-primary float-right">New SSQ</a>    
            <?php }?>
        </h3>
    </div>

    <div class="card-body pr-2 pl-2">
        <table class="table table-striped table-bordered">
            <thead class="text-center">
                <tr>
                    <th>Session ID</th>
                        <?php
                        $sql_session = "SELECT * FROM Session WHERE session_ID = " . $_GET["session_ID"];
                        $mysqli_result = mysqli_query($conn, $sql_session);
                        $row_session = mysqli_fetch_assoc($mysqli_result);
                        echo "<td>" .  $row_session['session_ID']  . "</td>";     
                        ?>
                </tr> 
                
                <tr>
                    <th>Study Name</th>
                        <?php
                        // show name for study_ID, not id         
                        if (isset($row_session['study_ID'])){
                            $sql_users = "SELECT full_name FROM Study WHERE study_id = " . $row_session['study_ID'] . " LIMIT 1;";
                            $result_users = mysqli_query($conn, $sql_users);
                            $row_users = mysqli_fetch_assoc($result_users);
                                
                            echo "<td>" . $row_users['full_name'] . "</td>";
                        }
                        else{
                            echo "<td>-</td>";
                        }
                        ?>
                </tr>  
                
                <tr>
                    <th>Participant Name</th>   
                        <?php
                        // show name for participant_ID, not id         
                        if (isset($row_session['participant_ID'])){
                            $sql_users = "SELECT anonymous_name FROM Participant WHERE participant_id = " . $row_session['participant_ID'] . " LIMIT 1;";
                            $result_users = mysqli_query($conn, $sql_users);
                            $row_users = mysqli_fetch_assoc($result_users);
                                
                            echo "<td>" . $row_users['anonymous_name'] . "</td>";
                        }
                        else{
                            echo "<td>-</td>";
                        }     
                        ?>
                    </tr>
                    
                <tr>  
                    <th># of Quizzes Taken</th>   
                    <?php
                    echo "<td>" .  $row_session['quizzes_taken']     . "</td>";
                    ?>
                </tr> 
                
                <tr>
                    <th>Start Time</th>
                    <?php
                    echo "<td>" .  $row_session['start_time']     . "</td>";       
                    ?>
                </tr>
                
                <tr>
                    <th>End Time</th>
                    <?php
                    echo "<td>" .  $row_session['end_time'] . "</td>";
                    ?>
                </tr>
                
                <tr>
                    <th>Comment</th>
                    <?php
                    echo "<td>" .  $row_session['comment'] . "</td>";    
                    ?>
                </tr>
                
                <tr>
                    <th>Created By</th>
                    <?php
                    // show name for created_by, not id         
                    if (isset($row_session['created_by'])){
                        $sql_users = "SELECT name FROM tbl_users WHERE id = " . $row_session['created_by'] . " LIMIT 1;";
                        $result_users = mysqli_query($conn, $sql_users);
                        $row_users = mysqli_fetch_assoc($result_users);
                            
                        echo "<td>" . $row_users['name'] . "</td>";
                    } else{
                        echo "<td>-</td>";
                    }       
                    ?>
                </tr>   
                
                <tr>
                    <th>Last Edited By</th>  
                    <?php
                    // show name for last_edited_by, not id    
                    if (isset($row_session['last_edited_by'])){
                        $sql_users = "SELECT name FROM tbl_users WHERE id = " . $row_session['last_edited_by'] . " LIMIT 1;";
                        $result_users = mysqli_query($conn, $sql_users);
                        $row_users = mysqli_fetch_assoc($result_users);
                        echo "<td>" . $row_users['name'] . "</td>";
                    } else {
                        echo "<td>-</td>";
                    }    
                    ?>
                </tr>
                
                <tr>
                    <th>Action</th>
                    <?php
                    echo "<td>";
                    echo "<form method=\"post\">";
                    if (isset($row_session['end_time'])){
                        echo "<input type=\"submit\" name=\"restart-session-btn\" value=\"Restart Session\">";
                    }
                    else{
                        echo "<input type=\"submit\" name=\"end-session-btn\"   value=\"End Session\">";
                    }
                    echo "</form>";
                    echo "</td>";
                    ?>                     
                </tr>
                
            </thead>
        </table>
    </div>
</div>

<?php
  include 'inc/footer.php';
?>