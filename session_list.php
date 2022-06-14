<?php
include 'inc/header.php';
include_once 'lib/Database.php';

$db = Database::getInstance();
$pdo = $db->pdo;

Session::CheckSession();
if (!isset($_POST["study_ID"]) || !isset($_POST["iv"])){
    header("Location: view_study");
    exit();
}

$iv = explode(',', $_POST['iv']);
$iv_str = call_user_func_array("pack", array_merge(array("C*"), $iv));
$decrypted = Crypto::decrypt($_POST['study_ID'], $iv_str);
Session::setStudyId(intval($decrypted), $pdo);

if (Session::get("study_ID") == 0){
    header("Location: view_study");
    exit();
}
?>

<div class="card">
    <div class="card-header">
            <h3>Session List <span class="float-right"> <span class="float-right"> <a href="view_study" class="btn btn-primary">Back</a>    
  </h3> 
        </strong></span></h3>
    </div>
        
    <div class="card-body pr-2 pl-2">
    <?php
        $sql = "SELECT session_ID, start_time, participant_ID FROM Session WHERE study_ID = " . Session::get("study_ID");
        $result = $pdo->query($sql);
            
        if ($result->rowCount() > 0){
    ?>
        <br />
            <table class="table table-striped table-bordered" id="example">
                <thead class="text-center">
                    <tr>
                        <th>Participant Name</th>
                        <th>Start Time</th>                        
                        <th>Action</th>
                    </tr>
                </thead>
                    
                <tbody>
        
                <?php
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) { 
                        echo "<tr>";
                        
                        //echo "<td>" . $row['session_ID'] ."</td>";
                        
                        if (isset($row['participant_ID'])){
                            $sql_users = "SELECT anonymous_name FROM Participants WHERE participant_id = " . $row['participant_ID'] . " LIMIT 1;";
                            $result_users = $pdo->query($sql_users);
                            
                            if (!$result_users){
                                echo $pdo->errorInfo($conn);
                            }
                            
                            $row_users = $result_users->fetch(PDO::FETCH_ASSOC);

                            echo "<td>" . $row_users['anonymous_name'] . "</td>";
                        } else {
                            echo "<td>-</td>";
                        }                             

                        echo "<td>" .  $row['start_time']     . "</td>";                           
                    
                        echo "<td>";
                        echo "<a class='btn-success btn-sm' href=\"session_details\" data-session_ID=\"" . $row['session_ID'] . "\">Session Details</a>";
                        
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

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on("click", "a.btn-success", redirectUser);
        
        function redirectUser(){
            let form = document.createElement("form");
            let hiddenInput = document.createElement("input");
            
            form.setAttribute("method", "POST");
            form.setAttribute("action", $(this).attr("href"));
            form.setAttribute("style", "display: none");
            
            hiddenInput.setAttribute("type", "hidden");
            hiddenInput.setAttribute("name", "session_ID");
            hiddenInput.setAttribute("value", $(this).attr("data-session_ID"));
            
            form.appendChild(hiddenInput);
            document.body.appendChild(form);
            form.submit();
            
            return false;
        };
    });
</script>

<?php
  include 'inc/footer.php';
?>