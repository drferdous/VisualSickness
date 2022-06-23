<?php
$filepath = realpath(dirname(__FILE__));
include_once $filepath."/../lib/Session.php";
include "check_verification.php";
Session::init();
if (Session::get("login") == true && $_SERVER['REQUEST_URI'] !== '/pending_verification' && $_SERVER['REQUEST_URI'] !== '/verify_password') {
    checkVerification();
}

spl_autoload_register(function($classes){

  include 'classes/'.$classes.".php";

});


$users = new Users();
$studies = new Studies();

   
$timezone = Session::get('time_offset');


?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visual Sickness</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" 
          crossorigin="anonymous">
    <!--<link rel="stylesheet" href="assets/bootstrap.min.css">-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link href="https://use.fontawesome.com/releases/v5.0.4/css/all.css" rel="stylesheet">
    <!--<link rel="stylesheet" href="assets/dataTables.bootstrap4.min.css">-->
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css" integrity="sha384-V05SibXwq2x9UKqEnsL0EnGlGPdbHwwdJdMjmp/lw3ruUri9L34ioOghMTZ8IHiI" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
    <?php if($timezone === false) { ?>
    <script type="text/javascript">
    $(document).ready(function() {
            var visitortime = new Date();
            var visitortimezone = -visitortime.getTimezoneOffset();
            $.ajax({
                type: "POST",
                url: "timezone",
                data: {
                    time: visitortimezone,
                },
                success: function(){
                    location.reload();
                }
            });
    });
</script>
<?php } ?>
    
    <style>
        /* VisualQuiz.php CSS
           HIDE RADIO           */
        .pictures [type=radio] { 
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .symptoms {
            margin: auto;
            margin-top: 10px;
            background: white;
            width: 53%;
            padding: 10px;
        }

        .pictures {
            margin: auto;
            margin-top: 10px;
            background: white;
            align-items: center;
            padding: 10px;
        }

        .pictures img {
            border: 3px solid lightblue;
            border-radius: 4px;
            width: 100px;
            margin: auto;
        }

        /* IMAGE STYLES */
        .pictures [type=radio] + img {
            cursor: pointer;
        }

        /* CHECKED STYLES */
        .pictures [type=radio]:checked + img {
            outline: 2px solid #f00;
        }

        .pictures [type=radio]:checked {
            border-color: #f00;
        }
    </style>
  </head>
  <body>

<?php


if (isset($_GET['action']) && $_GET['action'] == 'logout') {
  Session::destroy();
}

if (Session::get('roleid') == '1') {
    $homepage = "userlist";
} else {
    $homepage = "view_study";
}

 ?>


    <div class="container">

      <nav class="navbar navbar-expand-md navbar-dark bg-dark card-header">
        <a class="navbar-brand" href= <?php echo $homepage; ?>><i class="fas fa-home mr-2"></i>Home</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
          <ul class="navbar-nav ml-auto">
 
        <?php if (Session::get('login') === TRUE) { ?>
            <?php if (Session::get("reg_stat") == 2) { ?>
                <?php if (intval(Session::get("roleid")) <= 3){ ?>
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
                <?php } 
                      else{ ?>
                        <li class="nav-item
                            <?php
                
                      				$path = $_SERVER['SCRIPT_FILENAME'];
                      				$current = basename($path, '.php');
                      				if ($current == 'view_study') {
                      					echo "active ";
                      				}
                
                      			 ?>
                
                            ">
                            <a class="nav-link" 
                               href="view_study">
                               <i class="fas fa-sticky-note mr-2"></i>Study List
                            </a>
                        </li>
                <?php } ?>
                <li class="nav-item
                <?php
    
          				$path = $_SERVER['SCRIPT_FILENAME'];
          				$current = basename($path, '.php');
          				if ($current == 'participantList' && !isset($_GET['forStudy'])) {
          					echo "active ";
          				}
    
          			 ?>
    
                ">

                      <a class="nav-link" href="participantList"><i class="fas fa-user mr-2"></i>Participants</a>
                  </li>
                <?php if (Session::get('roleid') == '1') { ?>
                      <li class="nav-item
                <?php
    
          				$path = $_SERVER['SCRIPT_FILENAME'];
          				$current = basename($path, '.php');
          				if ($current == 'userlist') {
          					echo "active ";
          				}
    
          			 ?>
    
                ">
    
                          <a class="nav-link" href="userlist"><i class="fas fa-users mr-2"></i>User Lists</a>
                      </li>
                <?php  } ?>
            <?php } ?>
            <?php if (Session::get("reg_stat") == 2) { ?>
                <li class="nav-item
                <?php
    
          				$path = $_SERVER['SCRIPT_FILENAME'];
          				$current = basename($path, '.php');
          				if ($current == 'profile') {
          					echo "active ";
          				}
    
          	    ?>
    
                ">
    
                  <a class="nav-link" href="profile"><i class="fab fa-500px mr-2"></i>Profile <span class="sr-only">(current)</span></a>
                </li>
                
                <li class="nav-item
                <?php
    
          				$path = $_SERVER['SCRIPT_FILENAME'];
          				$current = basename($path, '.php');
          				if ($current == 'index') {
          					echo "active";
          				}
    
          	    ?>"
                >
                </li>
            <?php } ?>
            
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

        <?php } ?>


          </ul>

        </div>
      </nav>
<script>
    $(document).ready(() => {
        $('.navbar-toggler').on('click', function (e) {
            $(`${e.target.dataset.target}`).collapse('toggle');
        });
        $('.navbar-toggler > span').on('click', function (e) {
            $(`${e.target.parentElement.dataset.target}`).collapse('toggle');
        });
    });
</script>
<script src="assets/bootstrap-show-password.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" 
        crossorigin="anonymous">
</script>
<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js" integrity="sha384-ZuLbSl+Zt/ry1/xGxjZPkp9P5MEDotJcsuoHT0cM8oWr+e1Ide//SZLebdVrzb2X" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js" integrity="sha384-jIAE3P7Re8BgMkT0XOtfQ6lzZgbDw/02WeRMJvXK3WMHBNynEx5xofqia1OHuGh0" crossorigin="anonymous"></script>