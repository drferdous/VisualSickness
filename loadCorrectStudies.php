<?php
    if ($_SERVER["REQUEST_METHOD"] !== "POST" || empty($_POST)){
        header("Location: 404");
        exit();
    }
    
    include "database.php";
    include "lib/Session.php";
    
    $idToSearch = $_POST["idToSearch"];
    $activeStatus = $_POST["activeStatus"];
    $sqlActiveStatus;
    
    if ($activeStatus === "active"){
        $sqlActiveStatus = " AND Study.is_active = 1;";
    }
    else{
        $sqlActiveStatus = ";";
    }
    
    if (Session::get("roleid") === '1'){
        $sql = "SELECT study_ID, full_name, created_at, is_active
                FROM Study
                WHERE TRUE";
    }
    else{
        $sql = "SELECT Study.study_ID, Study.full_name, Study.created_at, Study.is_active, Researcher_Study.study_role
                FROM Study, Researcher_Study
                WHERE Study.study_ID IN (SELECT study_ID
                                         FROM Researcher_Study
                                         WHERE researcher_ID = " . $idToSearch . ")
                AND Study.study_ID = Researcher_Study.study_ID
                AND Researcher_Study.researcher_ID = " . $idToSearch;
    }
    
    $sql = $sql . $sqlActiveStatus;
    $result = mysqli_query($conn, $sql);
    
    if (!$result){
        echo mysqli_error($conn);
    }
      while ($row = mysqli_fetch_assoc($result)){ ?>
        <tr>
            <td><?php echo $row['full_name']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
                <a class="btn-success btn-sm" href="study_details" data-study_ID="<?php echo $row['study_ID']; ?>">Study Details</a>
                        
                <?php if (((isset($row["study_role"]) && ($row["study_role"] === '2' || $row["study_role"] === '3'))) && $row["is_active"] === '1'){ ?>
                        <br>
                        <br>
                        <a class="btn-success btn-sm" href="create_session" data-study_ID="<?php echo $row['study_ID']; ?>" >Create Session</a>
                <?php } ?>
                
                <br>
                <br>
                <a class="btn-success btn-sm" href="session_list" data-study_ID="<?php echo $row['study_ID']; ?>">Session List</a>                       
            </td>
        </tr>
<?php } ?>