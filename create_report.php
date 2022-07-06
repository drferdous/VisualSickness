<?php
    include 'inc/header.php';
    include_once 'lib/Database.php';
    Session::CheckSession();
    $db = Database::getInstance();
    $pdo = $db->pdo;

    if(Session::get('roleid') == 4) {
        header('Location: study_list');
        exit();
    }

    $idToSearch = Session::get('id');
?>

<div class="card">
    <div class="card-header">
        <h1 class="float-left mb-0">Create Report</h1>
    </div>
    <div class="card-body">
        <?php 
            $rand = bin2hex(openssl_random_pseudo_bytes(16));
            Session::set("post_ID", $rand);
        ?>

        <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">

        <div class="form-group">
            <label for="study_id">Studies</label>
            <?php 
                $sql = "SELECT S.full_name, S.study_id
                        FROM study AS S JOIN researchers as R ON(S.study_id = R.study_id) 
                        WHERE R.is_active = 1 AND researcher_id = " . $idToSearch . " AND R.study_role < 4
                        ORDER BY S.last_edited_at DESC;";

                $result = $pdo->query($sql);
            ?>
            <select class="form-control form-select" name="study_id" id="study_id" <?= $result->rowCount() === 0 ? 'disabled' : '' ?>>
                <?php if($result->rowCount() === 0) { ?>
                    <option value="" disabled hidden selected>There are no studies available!</option>
                <?php } else {?>
                    <option value="" selected>All Studies</option>
                <?php }
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)){ 
                        $enc_id = Crypto::encrypt($row['study_id'], $iv); ?>
                        <option value="<?= $enc_id ?>;<?= bin2hex($iv) ?>"><?php echo $row['full_name'];?></option>
                <?php } ?>
            </select>
        </div>
        <div id="report_info"></div>
        <br>
        <?php if ($result->rowCount()) { ?>
            <div class="form-group">
                <button type="submit" name="create_report" class="btn btn-success" onclick="return downloadReport()">Download</button>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#study_id').change(function() {
            getOptions();
        });
        getOptions();
    });
    function getOptions() {
        const info = $('#study_id').val();
        const study_id = info.split(';')[0];
        const iv = info.split(';')[1];
        $.ajax({
            url: "report_options",
            type: "POST",
            cache: false,
            data: {
                study_id,
                iv
            },
            success:function(data){
                let reportSelector = document.getElementById("report_info");
                $(reportSelector).html(data);
            }
        });
    }
    function downloadReport() {
        const study_ID = $("#study_id").val()?.split(';')[0];
        const study_iv = $("#study_id").val()?.split(';')[1];
        const session_name = $("#session_id").val()?.split(';')[0];
        const participant_ID = $("#participant_id").val()?.split(';')[0];
        const participant_iv = $("#participant_id").val()?.split(';')[1];
        const ssq_time = $("#SSQ_id").val()?.split(';')[0];
        $.ajax({
            url: "report_download",
            type: "POST",
            cache: false,
            data: {
                study_ID,
                study_iv,
                session_name,
                participant_ID,
                participant_iv,
                ssq_time,
                downloadResults: true
            },
            success:function(data){
                const folderRegex = /([^]*?);FOLDERBREAK - name: ([^\s;]+);/g;
                let folders = false;
                const zip = new JSZip();
                while (match = folderRegex.exec(data)) {
                    const folder = match[1];
                    const folderName = match[2];
                    folders = true;
                    const f = zip.folder(folderName);
                    const fileRegex = /([^]*?);FILEBREAK - name: ([^\s;]+);/g;
                    while (match = fileRegex.exec(folder)) {
                        const file = match[1];
                        const fileName = match[2];
                        f.file(`${fileName}.csv`, file);
                    }
                }
                if (!folders) {
                    const fileRegex = /([^]*?);FILEBREAK - name: ([^\s;]+);/g;
                    while (match = fileRegex.exec(data)) {
                        const file = match[1];
                        const fileName = match[2];
                        zip.file(`${fileName}.csv`, file);
                    }
                }
                zip.generateAsync({type:"blob"})
                .then(function(content) {
                    const a = document.createElement('a');
                    a.download = 'VSreport.zip';
                    a.href = window.URL.createObjectURL(content);
                    a.dataset.downloadurl = ['text/csv', a.download, a.href].join(':');
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                });
            }
        });
    return false;
    }
 </script>

<?php
  include 'inc/footer.php';
?>