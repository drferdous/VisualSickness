<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();
if (Session::get('roleid') > 2) {
    header('Location: study_list');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_study']) && Session::CheckPostID($_POST)) {

  $insert_study = $studies->insertStudy($_POST);
}

if (isset($insert_study)) {
  echo $insert_study[0];?>
    <script type="text/javascript">
        const divMsg = document.getElementById("flash-msg");
        if (divMsg.classList.contains("alert-success")){
            setTimeout(function(){
                location.href = 'study_details';
            }, 1000);
        }
    </script>
<?php } ?>
 
    
 <div class="card">
   <div class="card-header">
          <h3 class='text-center'>Create a Study</h3>
        </div>
        <div class="card-body">
            <form class="" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="post" id="createStudyForm">
                <?php 
                    $rand = bin2hex(openssl_random_pseudo_bytes(16));
                    Session::set("post_ID", $rand);
                ?>
                <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
                <div style="margin-block: 6px;">
                    <small style='color: red'>
                        * Required Field
                    </small>
                </div>
                <div class="form-group">
                  <label for="full_name" class="required">Full Name</label>
                  <input type="text" value="<?= Util::getValueFromPost('full_name', $_POST); ?>" id="full_name" name="full_name"  class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="short_name" class="required">Short Name</label>
                  <input type="text" value="<?= Util::getValueFromPost('short_name', $_POST); ?>" id="short_name" name="short_name"  class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="IRB">IRB</label>
                  <input type="text" value="<?= Util::getValueFromPost('IRB', $_POST); ?>" id="IRB" name="IRB"  class="form-control">
                </div>
                <div class="form-group">
                  <label for="description">Description</label>
                  <input type="text" value="<?= Util::getValueFromPost('description', $_POST); ?>" id="description" name="description"  class="form-control">
                </div>
                <div class="form-group">
                  <label for="session_times" class="required">Input Session Names</label>
                  <input type="text" value="<?= Util::getValueFromPost('session_times', $_POST); ?>" id="session_times" name="session_times" class="form-control" required>
                  <small>Format: comma-separated (e.g., Session 1, Session 2)</small>
                </div>
                <div class="form-group">
                  <label for="ssq_times" class="required">Input SSQ Times</label>
                  <input type="text" value="<?= Util::getValueFromPost('ssq_times', $_POST); ?>" id="ssq_times" name="ssq_times" class="form-control" required>
                  <small>Format: comma-separated (e.g., pre, post)</small>
                </div>
                <div class="form-group">
                 <button type="submit" name="insert_study" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>

      </div>
      <script>
      </script>

  <?php
  include 'inc/footer.php';

  ?>