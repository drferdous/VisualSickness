<?php
include 'inc/header.php';
include_once 'lib/Database.php';

$db = Database::getInstance();
$pdo = $db->pdo;

Session::CheckSession();
Session::set('session_ID', intval($_POST['session_ID']));

if (isset($_POST['restart-session-btn'])){
    $startSessionMessage = $studies->restart_session($_POST['session_ID']);
    if (isset($startSessionMessage)){
        echo $startSessionMessage;
    }
}

if (isset($_POST['end-session-btn'])){
    $endSessionMessage = $studies->endSession($_POST['session_ID']);
    if (isset($endSessionMessage)){
        echo $endSessionMessage;
    }
}

if (isset($_POST['delete-ssq-btn'])){
    $deleteSSQmessage = $studies->deleteSSQ($_POST['session_ID']);
    if (isset($deleteSSQmessage)){
        echo $deleteSSQmessage ;
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
                    $result = $pdo->query($sql);
                    $row = $result->fetch(PDO::FETCH_ASSOC);
                ?>
                <a href="session_list" style="transform: translateX(-10px)" class="btn btn-primary redirectUser" data-study_ID="<?php echo $row['study_ID']; ?>" data-session_ID="-1">Back</a>

                <?php
                    $sql = "SELECT end_time
                            FROM Session
                            WHERE session_ID = " . $_POST['session_ID'] . "
                            LIMIT 1;";
                    $result = $pdo->query($sql);
                    $isSessionActive = !isset($result->fetch(PDO::FETCH_ASSOC)["end_time"]);
                    
                    $sql = "SELECT * 
                            FROM SSQ_times 
                            WHERE study_id = " . $row["study_ID"] . " 
                            AND is_active = 1;";
                    $result = $pdo->query($sql);
                    $totalQuizTimesAvailable = $result->rowCount();
                    
                    $sql = "SELECT * FROM SSQ_times 
                            WHERE id IN (SELECT ssq_time 
                                         FROM SSQ 
                                         WHERE session_ID = " . $_POST["session_ID"] . "
                                         AND is_active = 1) 
                            AND is_active = 1;";
                    $result = $pdo->query($sql);
                    $numQuizTimesTaken = $result->rowCount();
                    $areQuizTimesAvailable = $totalQuizTimesAvailable - $numQuizTimesTaken > 0;
            
                    if ($isSessionActive && $areQuizTimesAvailable){?>
                        <a href="chooseQuiz" class="btn btn-primary redirectUser" data-study_ID="-1" data-session_ID="<?php echo $_POST['session_ID']; ?>">New SSQ</a>
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
                        $sql_result = $pdo->query($sql_session);
                        $row_session = $sql_result->fetch(PDO::FETCH_ASSOC);
                        echo "<td>" .  $row_session['session_ID']  . "</td>";     
                        ?>
                </tr> 
                
                <tr>
                    <th>Study Name</th>
                        <?php
                        // show name for study_ID, not id         
                        if (isset($row_session['study_ID'])){
                            $sql_users = "SELECT full_name FROM Study WHERE study_id = " . $row_session['study_ID'] . " LIMIT 1;";
                            $result_users = $pdo->query($sql_users);
                            $row_users = $result_users->fetch(PDO::FETCH_ASSOC);
                                
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
                            $sql_users = "SELECT anonymous_name FROM Participants WHERE participant_id = " . $row_session['participant_ID'] . " LIMIT 1;";
                            $result_users = $pdo->query($sql_users);
                            $row_users = $result_users->fetch(PDO::FETCH_ASSOC);
                                
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
                    
                    $sql = "SELECT SSQ.ssq_ID, SSQ.ssq_time, SSQ.ssq_type
                            FROM SSQ JOIN SSQ_times ON (SSQ.ssq_time = SSQ_times.id)
                            WHERE SSQ.session_ID = " . Session::get('session_ID') . "
                            AND SSQ_times.is_active = 1
                            AND SSQ.is_active = 1
                            ORDER BY SSQ.ssq_time ASC;";
                    $result = $pdo->query($sql);
                    
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                        $ssq_times = "SELECT name FROM SSQ_times WHERE id = " . $row["ssq_time"];
                        $ssq_type = "SELECT type FROM SSQ_type WHERE id = " . $row["ssq_type"];
                        $result_times = $pdo->query($ssq_times);
                        $ssq_name = $result_times->fetch(PDO::FETCH_ASSOC)["name"];
                        $result_type = $pdo->query($ssq_type);
                        $ssq_type = $result_type->fetch(PDO::FETCH_ASSOC)["type"]; ?>
                        <a style="margin-inline: 3px;" class="btn-sm btn-success redirectUser" 
                            href="<?php echo $ssq_type; ?>Quiz"
                            data-ssq_ID="<?php echo $row['ssq_ID']; ?>"
                            data-ssq_time="<?php echo $row["ssq_time"]; ?>" 
                            data-ssq_type="<?php echo $row['ssq_type']; ?>"
                        >
                        <?php echo $ssq_name . " (" . $ssq_type . ")"; ?>
                        </a>
                    <?php } ?>
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
                        $result_users = $pdo->query($sql_users);
                        $row_users = $result_users->fetch(PDO::FETCH_ASSOC);
                            
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
                        $result_users = $pdo->query($sql_users);
                        $row_users = $result_users->fetch(PDO::FETCH_ASSOC);
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
                    echo "<form action=\"session_details\" method=\"POST\">";
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
        $(document).on("click", "a.redirectUser", redirectUser);
    });
    
    function redirectUser(){
        let form = document.createElement("form");
        let hiddenInput;
        
        form.setAttribute("method", "POST");
        form.setAttribute("action", $(this).attr("href"));
        form.setAttribute("style", "display: none");
        
        if ($(this).get(0).hasAttribute("data-study_ID")){
            hiddenInput = document.createElement("input");
            hiddenInput.setAttribute("type", "hidden");
            hiddenInput.setAttribute("name", "study_ID");
            hiddenInput.setAttribute("value", $(this).attr("data-study_ID"));
            form.appendChild(hiddenInput);
        }

        if ($(this).get(0).hasAttribute("data-session_ID")){
            hiddenInput = document.createElement("input");
            hiddenInput.setAttribute("type", "hidden");
            hiddenInput.setAttribute("name", "session_ID");
            hiddenInput.setAttribute("value", $(this).attr("data-session_ID"));
            form.appendChild(hiddenInput);    
        }
        
        if ($(this).get(0).hasAttribute("data-ssq_ID")){
            hiddenInput = document.createElement("input");
            hiddenInput.setAttribute("type", "hidden");
            hiddenInput.setAttribute("name", "ssq_ID");
            hiddenInput.setAttribute("value", $(this).attr("data-ssq_ID"));
            form.appendChild(hiddenInput);
        }

        if ($(this).get(0).hasAttribute("data-ssq_time")){
            hiddenInput = document.createElement("input");
            hiddenInput.setAttribute("type", "hidden");
            hiddenInput.setAttribute("name", "ssq_time");
            hiddenInput.setAttribute("value", $(this).attr("data-ssq_time"));
            form.appendChild(hiddenInput);
        }

        if ($(this).get(0).hasAttribute("data-ssq_type")){
            hiddenInput = document.createElement("input");
            hiddenInput.setAttribute("type", "hidden");
            hiddenInput.setAttribute("name", "ssq_type");
            hiddenInput.setAttribute("value", $(this).attr("data-ssq_type"));
            form.appendChild(hiddenInput);
        }

        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "is_first_time");
        hiddenInput.setAttribute("value", "false");
        form.appendChild(hiddenInput);
        
        document.body.appendChild(form);
        form.submit();      
        
        return false;
    }
</script>
<?php
  include 'inc/footer.php';
?>