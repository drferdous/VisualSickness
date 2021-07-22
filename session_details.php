<?php
include 'inc/header.php';
include_once 'database.php';

Session::CheckSession();
Session::set('session_ID', intval($_POST['session_ID']));

if (isset($_POST['restart-session-btn'])){
    $startSessionMessage = $users->restart_session($_POST['session_ID']);
    if (isset($startSessionMessage)){
        echo $startSessionMessage;
    }
}

if (isset($_POST['end-session-btn'])){
    $endSessionMessage = $users->endSession($_POST['session_ID']);
    if (isset($endSessionMessage)){
        echo $endSessionMessage;
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3>Session Details 
            <span class="float-right"> 
                <?php
                    $sql = "SELECT study_ID
                            FROM Session
                            WHERE session_ID = " . $_POST['session_ID'] . "
                            LIMIT 1;";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                ?>
                <a href="session_list" style="transform: translateX(-10px)" class="btn btn-primary" data-study_ID="<?php echo $row['study_ID']; ?>" data-session_ID="-1">Back</a>

                <?php
                    $sql = "SELECT end_time
                            FROM Session
                            WHERE session_ID = " . $_POST['session_ID'] . "
                            LIMIT 1;";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
            
                    if (!isset($row['end_time'])){?>
                        <a href="chooseQuiz" class="btn btn-primary" data-study_ID="-1" data-session_ID="<?php echo $_POST['session_ID']; ?>">New SSQ</a>    
              <?php }?>
            </span>
        </h3>
    </div>

    <div class="card-body pr-2 pl-2">
        <table class="table table-striped table-bordered">
            <thead class="text-center">
                <tr>
                    <th>Session ID</th>
                        <?php
                        $sql_session = "SELECT * FROM Session WHERE session_ID = " . $_POST['session_ID'];
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
                    <th>Quizzes Taken</th>
                    <td>
                    <?php
                    $sql_ssq_info = "CREATE TABLE `#SSQ_info` (
                                    ssq_time TINYINT(4) NOT NULL,
                                    ssq_type TINYINT(4) NOT NULL,
                                    session_ID INT(11) NOT NULL
                                );";
                    mysqli_query($conn, $sql_ssq_info);
                    
                    $sql_ssq_info = "INSERT INTO `#SSQ_info`
                                    SELECT ssq_time, ssq_type, session_ID
                                    FROM SSQ
                                    WHERE session_ID = " . Session::get('session_ID') . ";";
                    mysqli_query($conn, $sql_ssq_info);
                    
                    $sql_times = "SELECT id, name FROM SSQ_times;";
                    $result_times = mysqli_query($conn, $sql_times);
                    while ($row_times = mysqli_fetch_assoc($result_times)){
                        $sql_ssq = "SELECT *
                                   FROM `#SSQ_info`
                                   WHERE ssq_time = " . $row_times['id'] . "
                                   LIMIT 1;";
                        $result_ssq = mysqli_query($conn, $sql_ssq);
                        
                        if (mysqli_num_rows($result_ssq) > 0){
                            $row_ssq = mysqli_fetch_assoc($result_ssq);
                            
                            $sql_type = "SELECT type
                                        FROM SSQ_type
                                        WHERE id = " . $row_ssq['ssq_type'] . "
                                        LIMIT 1;";
                            $result_type = mysqli_query($conn, $sql_type);
                            $row_type = mysqli_fetch_assoc($result_type);
                        ?>
                            <a  class="btn-sm btn-success" 
                                href="<?php echo strtolower($row_times['name']); ?>_quiz_results" 
                                data-ssq_time="<?php echo $row_times['id']; ?>" 
                                data-ssq_type="<?php echo $row_ssq['ssq_type']; ?>">
                                <?php echo $row_times['name'] . " (" . $row_type['type'] . ")"; ?>
                            </a>    
                    <?php }
                    }
                    
                        $sql_ssq_info = "DROP TABLE IF EXISTS `#SSQ_info`;";
                        mysqli_query($conn, $sql_ssq_info);
                    ?>
                    </td>
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
                    echo "<input type=\"hidden\" name=\"session_ID\" value=\"" . $_POST['session_ID'] ."\">";
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
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on("click", "a", redirectUser);
    });
    
    function redirectUser(){
        let form = document.createElement("form");
        let hiddenInput;
        
        form.setAttribute("method", "POST");
        form.setAttribute("action", $(this).attr("href"));
        form.setAttribute("style", "display: none");
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "study_ID");
        hiddenInput.setAttribute("value", $(this).attr("data-study_ID"));
        form.appendChild(hiddenInput);
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "session_ID");
        hiddenInput.setAttribute("value", $(this).attr("data-session_ID"));
        form.appendChild(hiddenInput);
        
        document.body.appendChild(form);
        form.submit();
        
        return false;
    }
</script>
<?php
  include 'inc/footer.php';
?>