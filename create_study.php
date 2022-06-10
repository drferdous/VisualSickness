<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_study'])) {

  $insert_study = $studies->insert_study($_POST);
}

if (isset($insert_study)) {
  echo $insert_study;
}


 ?>
 
    
 <div class="card">
   <div class="card-header">
          <h3 class='text-center'>Create a Study</h3>
        </div>
        <div class="card-body">
            <form class="" action="" method="post" id="createStudyForm">
                <div class="form-group">
                  <label for="full_name" class="required">Full Name</label>
                  <input type="text" id="full_name" name="full_name"  class="form-control">
                </div>
                <div class="form-group">
                  <label for="short_name" class="required">Short Name</label>
                  <input type="text" id="short_name" name="short_name"  class="form-control">
                </div>
                <div class="form-group">
                  <label for="IRB" class="required">IRB</label>
                  <input type="text" id="IRB" name="IRB"  class="form-control">
                </div>
                <div class="form-group">
                  <label for="description">Description (optional) </label>
                  <input type="text" id="description" name="description"  class="form-control">
                </div>
                <div class="form-group">
                  <label for="ssq_times" class="required">Input SSQ Times</label>
                  <input type="text" id="ssq_times" name="ssq_times" class="form-control">
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