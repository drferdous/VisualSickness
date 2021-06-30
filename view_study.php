<?php
include 'inc/header.php';
include_once 'database.php';

Session::CheckSession();

if (isset($insert_study)) {
  echo $insert_study;
}

?>

<div class="card ">
    <div class="card-header">
        <?php if (Session::get('roleid') == '1' || Session::get('roleid') == '2') { ?>
            <h3>Manage Studies <span class="float-right">        
            <a href="add_researcher.php" class="btn btn-primary">Add A Researcher</a> 
            <a href="remove_researcher.php" class="btn btn-primary">Remove A Researcher</a> 
        <?php  } ?> 
        
        <?php if (Session::get('roleid') == '3' || Session::get('roleid') == '4') { ?>
            <h3>View Studies <span class="float-right">        
        <?php  } ?>         
        </div>
  </h3> 
        </strong></span></h3>
    </div>
        
    <div class="card-body pr-2 pl-2">
    <?php
        if (Session::get('roleid') === '1'){
            $sql = "SELECT study_ID, full_name FROM Study;";
        }
        else if (Session::get('roleid') === '2'){
            $sql = "SELECT study_ID, full_name FROM Study WHERE created_by = " . Session::get('id') . ";";
        }
        else{
            $sql = "SELECT study_ID, full_name FROM Study WHERE created_by = " . Session::get('id') . ";";
        }
        $result = mysqli_query($conn, $sql);
            
        if (mysqli_num_rows($result) > 0){
    ?>
        <br />
            <table class="table table-striped table-bordered" id="example">
                <thead class="text-center">
                    <tr>
                        <th>Study Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                    
                <tbody>
                <?php
                    while ($row = mysqli_fetch_assoc($result)) { 
                        echo "<tr>";
                        echo "<td>" . $row['full_name'] ."</td>";
                        echo "<td class='text-center'><a class='btn-success btn-sm' href=\"study_details.php?id=" . $row['study_ID'] . "\">Study Details</a></td>";
                        echo "</tr>";
                    }
                ?>
                </tbody>
            </table>
    <?php } 
        else{
            echo "<p>You have no studies!</p>";
        }
    ?>
    </div>
</div>

<?php
  include 'inc/footer.php';
?>