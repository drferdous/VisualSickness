<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();
if (Session::get('roleid') > 2) {
    header('Location: view_study');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_study'])) {

  $insert_study = $studies->insert_study($_POST);
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
            <form class="" action="" method="post" id="createStudyForm">
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
                  <label for="IRB" class="required">IRB</label>
                  <input type="text" value="<?= Util::getValueFromPost('IRB', $_POST); ?>" id="IRB" name="IRB"  class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="description">Description</label>
                  <input type="text" value="<?= Util::getValueFromPost('description', $_POST); ?>" id="description" name="description"  class="form-control">
                </div>
                <div class="form-group">
                  <label for="ssq_times" class="required">Input SSQ Times</label>
                  <input type="text" value="<?= Util::getValueFromPost('ssq_times', $_POST); ?>" id="ssq_times" name="ssq_times" class="form-control" required>
                  <small>Format: comma-separated</small>
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