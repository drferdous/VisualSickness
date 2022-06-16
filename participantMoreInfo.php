<?php
    include "inc/header.php";
    
    $db = Database::getInstance();
    $pdo = $db->pdo;
    
    $iv = hex2bin($_POST["iv"]);
    $participant_ID = Crypto::decrypt($_POST["participant_ID"], $iv);
    
    $sql = "SELECT * FROM Participants AS P
            INNER JOIN Demographics AS D
            ON P.demographics_id = D.ID
            WHERE participant_ID = " . $participant_ID . "
            LIMIT 1;";
    $result = $pdo->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <span class="float-left"><h3>Participant List More Info</h3></span>
        <span class="float-right">
            <a href="participantList"
               class="btn btn-primary">
               Back
            </a>
        </span>
    </div>
    <div class="card-body">
        <table class="table table-striped table-bordered table-responsive" style="display: table; max-width: 600px; margin: 0px auto;">
            <tbody>
                <tr>
                    <th>Name</th>
                    <td><?php 
                            $iv = hex2bin($row["iv"]);
                            $name = Crypto::decrypt($row["anonymous_name"], $iv);
                            echo $name; ?>
                    </td>
                </tr>
                <tr>
                    <th>DOB</th>
                    <td><?php echo $row["dob"]; ?></td>
                </tr>
                <tr>
                    <th>Age</th>
                    <td><?php echo $row["Age"]; ?></td>
                </tr>
                <tr>
                    <th>Weight</th>
                    <td><?php echo $row["weight"]; ?></td>
                </tr>
                <tr>
                    <th>Gender</th>
                    <td><?php echo $row["Gender"]; ?></td>
                </tr>
                <tr>
                    <th>Education</th>
                    <td><?php echo $row["Education"]; ?></td>
                </tr>
                <tr>
                    <th>Race Ethnicity</th>
                    <td><?php echo $row["Race_Ethnicity"]; ?></td>
                </tr>
                <tr>
                    <th>Occupation</th>
                    <td><?php echo $row["occupation"]; ?></td>
                </tr>
                <tr>
                    <th>Phone Number</th>
                    <td><?php echo $row["phone_no"]; ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo $row["email"]; ?></td>
                </tr>
                <tr>
                    <th>Comments</th>
                    <td><?php echo $row["comments"]; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<?php
    include "inc/footer.php";
?>