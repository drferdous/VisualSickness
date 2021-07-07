<?php
include 'inc/header.php';
include_once 'database.php';

Session::CheckSession();

if (isset($_POST['end-session-btn'])){
    $endSessionMessage = $users->endSession($_GET['session_ID']);
    if (isset($endSessionMessage)){
        echo $endSessionMessage;
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3>Session Details</h3>
    </div>

    <div class="card-body pr-2 pl-2">
        <table class="table table-striped table-bordered">
            <thead class="text-center">
                <tr>
                    <th>Session ID</th>
                    <th>Study ID</th>
                    <th>Participant ID</th>                    
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Comment</th>
                    <th>Created By</th>
                    <th>Last Edited By</th>    
                    <th>Action</th>
                </tr>
            </thead>
            
            <tbody>
                <tr>
                <?php
                $sql_session = "SELECT * FROM Session WHERE session_ID = " . $_GET["session_ID"];
                $mysqli_result = mysqli_query($conn, $sql_session);
                $row_session = mysqli_fetch_assoc($mysqli_result);
                echo "<td>" .  $row_session['session_ID']  . "</td>";
                echo "<td>" .  $row_session['study_ID'] . "</td>";
                echo "<td>" .  $row_session['participant_ID'] . "</td>";                
                echo "<td>" . $row_session['start_time']     . "</td>";
                echo "<td>" . $row_session['end_time'] . "</td>";
                echo "<td>" . $row_session['comment'] . "</td>";
                echo "<td>" . $row_session['created_by'] . "</td>";
                echo "<td>" . $row_session['last_edited_by'] . "</td>";                
                
                echo "<td>";
                echo "<form method=\"post\">";
                echo "<input type=\"submit\" name=\"end-session-btn\" value=\"End Session\" />";
                echo "</form>";
                echo "</td>";
                ?> 
                </tr>
            </tbody>
            
        </table>
    </div>
</div>

<?php
  include 'inc/footer.php';
?><?php
include 'inc/header.php';
include_once 'database.php';

Session::CheckSession();

if (isset($_POST['end-session-btn'])){
    $endSessionMessage = $users->endSession($_GET['session_ID']);
    if (isset($endSessionMessage)){
        echo $endSessionMessage;
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3>Session Details</h3>
    </div>

    <div class="card-body pr-2 pl-2">
        <table class="table table-striped table-bordered">
            <thead class="text-center">
                <tr>
                    <th>Session ID</th>
                    <th>Study ID</th>
                    <th>Participant ID</th>                    
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Comment</th>
                    <th>Created By</th>
                    <th>Last Edited By</th>    
                    <th>Action</th>
                </tr>
            </thead>
            
            <tbody>
                <tr>
                <?php
                $sql_session = "SELECT * FROM Session WHERE session_ID = " . $_GET["session_ID"];
                $mysqli_result = mysqli_query($conn, $sql_session);
                $row_session = mysqli_fetch_assoc($mysqli_result);
                echo "<td>" .  $row_session['session_ID']  . "</td>";
                echo "<td>" .  $row_session['study_ID'] . "</td>";
                echo "<td>" .  $row_session['participant_ID'] . "</td>";                
                echo "<td>" . $row_session['start_time']     . "</td>";
                echo "<td>" . $row_session['end_time'] . "</td>";
                echo "<td>" . $row_session['comment'] . "</td>";
                echo "<td>" . $row_session['created_by'] . "</td>";
                echo "<td>" . $row_session['last_edited_by'] . "</td>";                
                
                echo "<td>";
                echo "<form method=\"post\">";
                echo "<input type=\"submit\" name=\"end-session-btn\" value=\"End Session\" />";
                echo "</form>";
                echo "</td>";
                ?> 
                </tr>
            </tbody>
            
        </table>
    </div>
</div>

<?php
  include 'inc/footer.php';
?>