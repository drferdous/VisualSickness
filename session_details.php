<?php
include 'inc/header.php';
include_once 'lib/Database.php';

$db = Database::getInstance();
$pdo = $db->pdo;

if (Session::get('study_ID') == 0) {
    header('Location: view_study');
    exit();
}

Session::CheckSession();
if (isset($_POST['session_ID']) && isset($_POST['iv'])) {
    $iv = hex2bin($_POST['iv']);
    $decrypted = Crypto::decrypt($_POST['session_ID'], $iv);
    Session::set('session_ID', intval($decrypted));
    header('Location: session_details');
    exit();
}

$session_ID = Session::get('session_ID');

if (isset($_POST['restart-session-btn'])){
    $startSessionMessage = $studies->restart_session($session_ID);
    if (isset($startSessionMessage)){
        echo $startSessionMessage;
    }
}

if (isset($_POST['end-session-btn'])){
    $endSessionMessage = $studies->endSession($session_ID);
    if (isset($endSessionMessage)){
        echo $endSessionMessage;
    }
}

if (isset($_POST['remove-session-btn'])){
    $removeSessionMessage = $studies->removeSession($session_ID);
    if (isset($removeSessionMessage)){
        echo $removeSessionMessage; ?>
        <script type="text/javascript">
        const divMsg = document.getElementById("flash-msg");
        if (divMsg.classList.contains("alert-success")){
            setTimeout(function(){
                location.href = 'session_list';
            }, 1000);
        }
    </script> <?php
    }
}

if (isset($_POST['delete-ssq-btn'])){
    $deleteSSQmessage = $studies->deleteSSQ($session_ID);
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
                            WHERE session_ID = $session_ID
                            LIMIT 1;";
                    $result = $pdo->query($sql);
                    $row = $result->fetch(PDO::FETCH_ASSOC);
                ?>
                <a href="session_list" style="transform: translateX(-10px)" class="btn btn-primary">Back</a>

                <?php
                    $sql = "SELECT end_time
                            FROM Session
                            WHERE session_ID = $session_ID
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
                                         WHERE session_ID = $session_ID
                                         AND is_active = 1) 
                            AND is_active = 1;";
                    $result = $pdo->query($sql);
                    $numQuizTimesTaken = $result->rowCount();
                    $areQuizTimesAvailable = $totalQuizTimesAvailable - $numQuizTimesTaken > 0;
            
                    if ($isSessionActive && $areQuizTimesAvailable){?>
                        <a href="chooseQuiz" class="btn btn-primary">New SSQ</a>
              <?php }?>
            </span>
        </h3>
    </div>

    <div class="card-body pr-2 pl-2">
        <table class="table table-striped table-bordered">
            <thead class="text-center">
                <?php
                    $sql_session = "SELECT * FROM Session WHERE session_ID = $session_ID";
                    $sql_result = $pdo->query($sql_session);
                    $row_session = $sql_result->fetch(PDO::FETCH_ASSOC);
                ?>
                
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
                            $sql_users = "SELECT anonymous_name, iv FROM Participants WHERE participant_id = " . $row_session['participant_ID'] . " LIMIT 1;";
                            $result_users = $pdo->query($sql_users);
                            $row_users = $result_users->fetch(PDO::FETCH_ASSOC);
                            
                        $iv = hex2bin($row_users['iv']);
                        $name = Crypto::decrypt($row_users['anonymous_name'], $iv); 
                                
                            echo "<td>" . $name . "</td>";
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
                            WHERE SSQ.session_ID = $session_ID
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
                    <th class='align-middle'>Action</th>
                    <?php
                        $remove_sql = "SELECT is_active, created_by FROM Session WHERE session_ID = $session_ID;";
                        $remove_result = $pdo->query($remove_sql);
                        $remove_row = $remove_result->fetch(PDO::FETCH_ASSOC); 
                        
                        $role_sql = "SELECT study_role FROM Researcher_Study WHERE study_ID = " . Session::get("study_ID") . " AND  researcher_ID = " . Session::get("id") . " AND is_active = 1;";
                        $role_result = $pdo->query($role_sql);
                        $role_row = $role_result->fetch(PDO::FETCH_ASSOC);
                        
                        ?>
                    <td class='d-flex justify-content-center align-items-center'>
                        <form action="session_details" method="POST">
                        <?php if (isset($row_session['end_time']) && $remove_row['is_active'] == 1){ ?>
                            <input class="btn btn-warning" type="submit" name="restart-session-btn" value="Restart Session">
                            <?php if($role_row['study_role'] == 2 || $remove_row['created_by'] == Session::get("id")) {
                            ?>
                                    <input class="btn btn-danger" type="submit" name="remove-session-btn" value="Remove Session">
                            <?php }
                                
                            }
                        else {
                            if ($remove_row['is_active'] == 1) { ?>
                                <input class="btn btn-warning" type="submit" name="end-session-btn" value="End Session">
                                <?php if($role_row['study_role'] == 2 || $remove_row['created_by'] == Session::get("id")) {
                                 ?>
                                <input class="btn btn-danger" type="submit"  name="remove-session-btn" value="Remove Session">
                            <?php 
                                }
                             }
                        } ?>
                        </form>
                    </td>                 
                </tr>
                
            </thead>
        </table>
    </div>
</div>
<?php
  include 'inc/footer.php';
?>