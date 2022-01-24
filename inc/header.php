<?php
$filepath = realpath(dirname(__FILE__));
include_once $filepath."/../lib/Session.php";
Session::init();



spl_autoload_register(function($classes){

  include 'classes/'.$classes.".php";

});


$users = new Users();

?>



<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Visual Sickness</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" 
          crossorigin="anonymous">
    <link rel="stylesheet" href="assets/bootstrap.min.css">
    <link href="https://use.fontawesome.com/releases/v5.0.4/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  </head>
  <body>


<?php


if (isset($_GET['action']) && $_GET['action'] == 'logout') {
  Session::destroy();
}



 ?>


    <div class="container">

      <nav class="navbar navbar-expand-md navbar-dark bg-dark card-header">
        <a class="navbar-brand" href="index"><i class="fas fa-home mr-2"></i>Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
          <ul class="navbar-nav ml-auto">
 
        <?php if (Session::get('login') === TRUE) { ?>
            <li class="nav-item dropdown">
                <a href="#"
                   class="nav-link dropdown-toggle"
                   id="study-dropdown"
                   role="button"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">
                   <i class="fas fa-sticky-note mr-2"></i>Study
                </a>
                <ul class="dropdown-menu"
                    aria-labelledby="study-dropdown">
                    <li class="dropdown-item"><a href="view_study">Study List</a></li>
                    <li class="dropdown-item"><a href="create_study">Add Study</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a href="#"
                   class="nav-link dropdown-toggle"
                   id="participant-dropdown"
                   role="button"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">
                   <i class="fas fa-user mr-2"></i>Participant
                </a>
                <ul class="dropdown-menu"
                    aria-labelledby="participant-dropdown">
                    <li class="dropdown-item"><a href="participantList">Participant List</a></li>
                    <li class="dropdown-item"><a href="addParticipant">Add Participant</a></li>
                </ul>
            </li>
            <?php if (Session::get('roleid') == '1') { ?>
                  <li class="nav-item">

                      <a class="nav-link" href="userlist"><i class="fas fa-users mr-2"></i>User lists </span></a>
                  </li>
        <?php  } ?>
            <!-- Anything between this comment can be deleted.
            <?php if (Session::get('roleid') == '1' || Session::get('roleid') == '2' || Session::get('roleid') == '3') { ?>
              <li class="nav-item

              <?php

                          $path = $_SERVER['SCRIPT_FILENAME'];
                          $current = basename($path, '.php');
                          if ($current == 'addUser') {
                            echo " active ";
                          }

                         ?>">

                <a class="nav-link" href="addParticipant"><i class="fas fa-user-plus mr-2"></i>Add Participant </span></a>
              </li>
            <?php  } ?>
            -->
            <li class="nav-item
            <?php

      				$path = $_SERVER['SCRIPT_FILENAME'];
      				$current = basename($path, '.php');
      				if ($current == 'profile') {
      					echo "active ";
      				}

      			 ?>

            ">

              <a class="nav-link" href="profile" data-user_ID="<?php echo Session::get('id'); ?>" onclick="redirectUser"><i class="fab fa-500px mr-2"></i>Profile <span class="sr-only">(current)</span></a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="logout"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
            </li>
          <?php }else{ ?>

              <li class="nav-item

              <?php

                          $path = $_SERVER['SCRIPT_FILENAME'];
                          $current = basename($path, '.php');
                          if ($current == 'register') {
                            echo " active ";
                          }

                         ?>">
                <a class="nav-link" href="register"><i class="fas fa-user-plus mr-2"></i>Register</a>
              </li>
              <li class="nav-item
                <?php

                    				$path = $_SERVER['SCRIPT_FILENAME'];
                    				$current = basename($path, '.php');
                    				if ($current == 'login') {
                    					echo " active ";
                    				}

                    			 ?>">
                <a class="nav-link" href="login"><i class="fas fa-sign-in-alt mr-2"></i>Login</a>
              </li>

        <?php } 
        ?>


          </ul>

        </div>
      </nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" 
        crossorigin="anonymous">
</script>      
<script type="text/javascript">
    $(document).ready(function() {
       $("nav a[data-user_ID]").on('click', function() {
            let form = document.createElement("form");
            let hiddenInput;
            
            form.setAttribute("method", "POST");
            form.setAttribute("action", $(this).attr("href"));
            form.setAttribute("style", "display: none");
            
            hiddenInput = document.createElement("input");
            hiddenInput.setAttribute("type", "hidden");
            hiddenInput.setAttribute("name", "user_ID");
            hiddenInput.setAttribute("value", $(this).attr("data-user_ID"));
            form.appendChild(hiddenInput);
            
            hiddenInput = document.createElement("input");
            hiddenInput.setAttribute("type", "hidden");
            hiddenInput.setAttribute("name", "purpose");
            hiddenInput.setAttribute("value", "edit");
            form.appendChild(hiddenInput);
            
            document.body.appendChild(form);
            form.submit();
            
            return false;
       }); 
    });
</script>
