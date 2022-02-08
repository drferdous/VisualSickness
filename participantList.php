<?php
    include "inc/header.php";
    include "database.php";
?>

<div class="card">
    <div class="card-header">
        <h3>Participant List</h3>
    </div>
    <?php
        $sql = "SELECT Name
                FROM Affiliation
                WHERE id IN (SELECT affiliationid
                             FROM tbl_users
                             WHERE id = " . Session::get("id") . ")
                LIMIT 1;";
                
        $result = mysqli_query($conn, $sql);
        
        if (!$result){
            echo mysqli_error($conn);
        }
        
        $row = mysqli_fetch_assoc($result);
    ?>
    
    <p>Affiliation: <?php echo $row["Name"]; ?></p>
    <?php
        $sql = "SELECT * FROM Participants;";
        $result = mysqli_query($conn, $sql);
        
        if (!$result){
            echo mysqli_error($conn);
        }
    ?>
    <div class="card-body pr-2 pl-2">
        <table class="table table-striped table-bordered" id="example">
            <thead class="text-center">
                <tr>
                    <th>Participant Name</th>
                    <th>Date of Birth</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)){ ?>
                        <tr>
                            <td><?php echo $row["anonymous_name"]; ?></td>
                            <td><?php echo $row["dob"]; ?></td>
                            <td><?php echo $row["email"]; ?></td>
                            <td><?php echo $row["phone_no"]; ?></td>
                        </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php
  include "inc/footer.php";
?>