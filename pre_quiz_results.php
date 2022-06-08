<?php
    include 'inc/header.php';
    include_once 'lib/Database.php';
    
    $db = Database::getInstance();
    $pdo = $db->pdo;
    
    Session::CheckSession();
?>

<div class="card">
    <div class="card-header">
        <h3>
            Pre Quiz Details
            <a href="session_details.php" class="btn btn-primary float-right">Back</a>
        </h3>
    </div>
    
    <div class="card-body">
        
        <?php
            $sql = "SELECT *
                    FROM SSQ
                    WHERE session_ID = " . $_GET['session_ID'] . "
                    AND ssq_time = 0;";
            $result = $pdo->query($sql);
            if ($result->rowCount() > 0){
                while ($row = $result->fetch(PDO::FETCH_ASSOC)){
        ?>
                    <table class="table table-striped table-bordered">
                        <thead class="text-center">
                            <tr>
                                <th>Quiz ID</th>
                                <td><?php echo $row['ssq_ID']; ?></td>
                            </tr>
                            <tr>
                                <th>SSQ Type</th>
                                <td>
                                <?php
                                    $sql_type = "SELECT type 
                                                FROM SSQ_type
                                                WHERE type = " . $row['ssq_type'] . "
                                                LIMIT 1;";
                                    $result_type = $pdo->query($sql_type);
                                    $row_type = $result_type->fetch(PDO::FETCH_ASSOC);
                                    echo $row_type['type'];
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <th>General Discomfort</th>
                                <td><?php echo $row['general_discomfort']; ?></td>
                            </tr>
                            <tr>
                                <th>Fatigue</th>
                                <td><?php echo $row['fatigue']; ?></td>
                            </tr>
                            <tr>
                                <th>Headache</th>
                                <td><?php echo $row['headache']; ?></td>
                            </tr>
                            <tr>
                                <th>Eye Strain</th>
                                <td><?php echo $row['eye_strain']; ?></td>
                            </tr>
                            <tr>
                                <th>Difficulty Focusing</th>
                                <td><?php echo $row['difficulty_focusing']; ?></td>
                            </tr>
                            <tr>
                                <th>Increased Salivation</th>
                                <td><?php echo $row['increased_salivation']; ?></td>
                            </tr>
                            <tr>
                                <th>Sweating</th>
                                <td><?php echo $row['sweating']; ?></td>
                            </tr>
                            <tr>
                                <th>Nausea</th>
                                <td><?php echo $row['nausea']; ?></td>
                            </tr>
                            <tr>
                                <th>Difficulty Concentrating</th>
                                <td><?php echo $row['difficulty_concentrating']; ?></td>
                            </tr>
                            <tr>
                                <th>Dizziness with Eyes Open</th>
                                <td><?php echo $row['dizziness_with_eyes_open']; ?></td>
                                </tr>
                                <tr>
                                    <th>Dizziness with Eyes Closed</th>
                                    <td><?php echo $row['dizziness_with_eyes_closed']; ?></td>
                                </tr>
                                <tr>
                                    <th>Vertigo</th>
                                    <td><?php echo $row['vertigo']; ?></td>
                                </tr>
                                <tr>
                                    <th>Stomach Awareness</th>
                                    <td><?php echo $row['stomach_awareness']; ?></td>
                                </tr>
                                <tr>
                                    <th>Burping</th>
                                    <td><?php echo $row['burping']; ?></td>
                                </tr>
                            </thead>
                        </table>
        <?php   }
            }
        ?>
    </div>
</div>

<?php
  include 'inc/footer.php';
?>