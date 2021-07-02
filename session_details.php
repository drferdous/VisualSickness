<?php
include 'inc/header.php';
include_once 'database.php';

Session::CheckSession();
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
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Comment</th>
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
                echo "<td>" . $row_session['start_time']     . "</td>";
                echo "<td>" . $row_session['end_time'] . "</td>";
                echo "<td>" . $row_session['comment'] . "</td>";
                    
                ?> 
                </tr>
            </tbody>
            
        </table>
    </div>
</div>

<?php
  include 'inc/footer.php';
?>