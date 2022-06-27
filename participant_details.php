<?php
    include "inc/header.php";
    
    $db = Database::getInstance();
    $pdo = $db->pdo;
    
    $iv = hex2bin($_POST["iv"]);
    $participant_ID = Crypto::decrypt($_POST["participant_ID"], $iv);
    
    $sql = "SELECT * FROM participants AS P
            INNER JOIN demographics AS D
            ON P.demographics_id = D.demographics_id
            WHERE participant_id = " . $participant_ID . "
            LIMIT 1;";
    $result = $pdo->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);
    
    if (!isset($_POST['referrer'])) {
        if (isset($_SERVER['HTTP_REFERER'])) $referrer = ltrim(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH), '/') . '?' . parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
    }
    else $referrer = $_POST['referrer'];
    
    Session::requireResearcherOrUser(intval($row['study_id']), $pdo);
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['removeParticipant'])) {
        $removeParticipant = $studies->removeParticipant($participant_ID);
    
        if (isset($removeParticipant)) {
            echo $removeParticipant;?>
            <script type="text/javascript">
                const divMsg = document.getElementById("flash-msg");
                if (divMsg.classList.contains("alert-success")){
                    setTimeout(function(){
                        let redirectURL = "<?= isset($referrer) ? $referrer : "participant_list" ?>";
                        location.href = redirectURL;
                    }, 1000);
                }
            </script>
        <?php }
    }
    
    $study_sql = "SELECT short_name FROM study
                    WHERE study_id = " . $row['study_id'] . " LIMIT 1;";
    $study_name_result = $pdo->query($study_sql);
    $study_row = $study_name_result->fetch(PDO::FETCH_ASSOC);
    $study_name = $study_row['short_name'];
    
    $iv = hex2bin($row["iv"]);
    $name = Crypto::decrypt($row["anonymous_name"], $iv); ?>
<div class="card">
    <div class="card-header">
        <span class="float-left d-flex align-items-center"><h3><?= $name ?></h3></span>
        <?php if (isset($referrer)) { ?><span class="float-right"> <a href='<?= $referrer ?>' class="btn btn-primary backBtn">Back</a></span><?php } ?>
    </div>
    <div class="card-body">
        <div style="overflow-x: auto">
            <table class="table table-striped table-bordered table-responsive" style="display: table; max-width: 600px; margin: 0px auto;">
                <tbody>
                    <tr>
                        <th>DOB</th>
                        <td><?= $row["dob"] ?></td>
                    </tr>
                    <tr>
                        <th>Age</th>
                        <td><?= $row["age"] ?></td>
                    </tr>
                    <tr>
                        <th>Weight</th>
                        <td><?= $row["weight"] ?></td>
                    </tr>
                    <tr>
                        <th>Gender</th>
                        <td><?= $row["gender"] ?></td>
                    </tr>
                    <tr>
                        <th>Education</th>
                        <td><?= $row["education"] ?></td>
                    </tr>
                    <tr>
                        <th>Race Ethnicity</th>
                        <td><?= $row["race_ethnicity"] ?></td>
                    </tr>
                    <tr>
                        <th>Occupation</th>
                        <td><?= $row["occupation"] ?></td>
                    </tr>
                    <tr>
                        <th>Phone Number</th>
                        <td><a href="tel:<?= $row['phone_no'] ?>"><?= $row["phone_no"] ?></a></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><a href="mailto:<?= $row['email'] ?>"><?= $row["email"] ?></a></td>
                    </tr>
                    <tr>
                        <th>Comments</th>
                        <td><?= $row["comments"] ?></td>
                    </tr>
                </tbody>
            </table>
            <?php
            $role_sql = "SELECT study_role FROM researchers
                        WHERE researcher_id = " . Session::get('id') . "
                        AND study_id = " . $row['study_id'] . "
                        AND is_active = 1
                        LIMIT 1;";
            $role_result = $pdo->query($role_sql);
            $role_row = $role_result->fetch(PDO::FETCH_ASSOC);
            if (!isset($role_row['study_role'])) {
                header('Location: study_list');
                exit();
            }
            if ($role_row['study_role'] == 2 || $role_row['study_role'] == 3) { ?>
                <form class="text-center mt-2" action="" method="POST" onsubmit="return confirm('Are you sure you want to remove <?= $name ?> from the study \'<?= $study_name ?>\'? This action cannot be undone.');">
                    <input type="submit" name="removeParticipant" class="btn btn-danger" value="Remove Participant">
                    <?php if (isset($referrer)) { ?><input type="hidden" name="referrer" value="<?= $referrer ?>"><?php } ?>
                    <input name="participant_ID" type="hidden" value="<?= $_POST['participant_ID'] ?>">
                    <input name="iv" type="hidden" value="<?= $_POST['iv'] ?>">
                </form>
            <?php } ?>
        </div>
    </div>
</div>


<?php
    include "inc/footer.php";
?>