<?php
    if ($_SERVER["REQUEST_METHOD"] !== "POST" || empty($_POST)){
        header("Location: 404");
        exit();
    }
    
    include "lib/Session.php";
    include_once "lib/Database.php";
    
    Session::init();
    
    $db = Database::getInstance();
    $pdo = $db->pdo;
    
    $idToSearch = $_POST["idToSearch"];
    $activeStatus = $_POST["activeStatus"];
    $sqlActiveStatus;
    $sql;
    
    if ($activeStatus === "active"){
        $sqlActiveStatus = " AND Study.is_active = 1;";
    }
    else{
        $sqlActiveStatus = ";";
    }
    
    if (Session::get("roleid") === '1'){
        $sql = "SELECT study_ID, full_name, created_at, is_active
                FROM Study
                WHERE is_active = 1
                AND created_by IN (SELECT id FROM tbl_users WHERE affiliationid = " . Session::get("affiliationid") . ");";
    }
    else{
        $sql = "SELECT Study.study_ID, Study.full_name, Study.created_at, Study.is_active, Researcher_Study.study_role
                FROM Study, Researcher_Study
                WHERE Study.study_ID IN (SELECT study_ID
                                         FROM Researcher_Study
                                         WHERE researcher_ID = " . $idToSearch . " AND is_active = 1)
                AND Study.study_ID = Researcher_Study.study_ID
                AND Researcher_Study.researcher_ID = " . $idToSearch . "
                AND Researcher_Study.is_active = 1;";
    }
    
    $sql = $sql . $sqlActiveStatus;
    
    $result = $pdo->query($sql);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
        <tr>
            <td><?php echo $row['full_name']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
                <a class="btn-success btn-sm" href="study_details" data-study_ID="<?php echo $row['study_ID']; ?>">Study Details</a>
                        
                <?php if ($row["is_active"] === "1" &&
                          Session::get("roleid") === "1" ||
                         ((isset($row["study_role"]) &&
                         ($row["study_role"] === "2" || $row["study_role"] === "3")))){ ?>
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