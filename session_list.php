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
        $sql = "SELECT session_ID FROM Session WHERE study_ID = " . $_GET["study_ID"];
        
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
                        echo "<br>"; 
                        echo "<br>";                        
                        echo "<a class='btn-success btn-sm' href=\"chooseQuiz.php?session_ID=" . $row['session_ID'] . "\">New SSQ</a>";
                        
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