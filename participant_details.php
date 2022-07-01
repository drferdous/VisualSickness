<?php
    include "inc/header.php";
    
    $db = Database::getInstance();
    $pdo = $db->pdo;
    if (isset($_POST['participant_ID'])) {
        $iv = hex2bin($_POST["iv"]);
        $participant_ID = Crypto::decrypt($_POST["participant_ID"], $iv);
    } else $participant_ID = Session::get('participant_ID');
    
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
    $name = Crypto::decrypt($row["anonymous_name"], $iv);
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
    } ?>
<div class="card">
    <div class="card-header">
        <div class="float-left d-flex align-items-center">
            <h1 class="mb-0">
                <?php if ($role_row['study_role'] == 2 || $role_row['study_role'] == 3) { ?>
                    <a href="edit_participants" class="mr-2 redirectUser" style="color: #222;"><i class="fas fa-pencil-alt"><span class="d-none">Edit <?= $name ?></span></i></a>
                <?php } ?>
                <?= $name ?>
            </h1>
        </div>
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
                        <td>
                            <?php if ($row['phone_no']) { ?>
                                <a href="tel:<?= $row['phone_no'] ?>"><?= $row["phone_no"] ?></a>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>
                            <?php if ($row['email']) { ?>
                                <a href="mailto:<?= $row['email'] ?>"><?= $row["email"] ?></a>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Comments</th>
                        <td><?= $row["comments"] ?></td>
                    </tr>
                </tbody>
            </table>
            <?php if ($role_row['study_role'] == 2 || $role_row['study_role'] == 3) { ?>
                <form class="text-center mt-2" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="POST" onsubmit="return confirm('Are you sure you want to remove <?= $name ?> from the study \'<?= $study_name ?>\'? This action cannot be undone.');">
                    <input type="submit" name="removeParticipant" class="btn btn-danger" value="Remove Participant">
                    <?php if (isset($referrer)) { ?><input type="hidden" name="referrer" value="<?= $referrer ?>"><?php } ?>
                    <input name="participant_ID" type="hidden" value="<?= $_POST['participant_ID'] ?>">
                    <input name="iv" type="hidden" value="<?= $_POST['iv'] ?>">
                </form>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(() => {
        $(document).on("click", "a.redirectUser", redirectUser);
    });
    
    function redirectUser() {
        <?php $participant_enc = Crypto::encrypt($participant_ID, $iv); ?>
        let form = document.createElement("form");
        let hiddenInput;
        
        form.setAttribute("method", "POST");
        form.setAttribute("action", $(this).attr("href"));
        form.setAttribute("style", "display: none");
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "participant_ID");
        hiddenInput.setAttribute("value", '<?= $participant_enc ?>');
        form.appendChild(hiddenInput);
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "iv");
        hiddenInput.setAttribute("value", '<?= bin2hex($iv) ?>');
        form.appendChild(hiddenInput);
        
        document.body.appendChild(form);
        form.submit();
        
        return false;
    };
</script>


<?php
    include "inc/footer.php";
?>