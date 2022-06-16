<?php 

include_once 'lib/Session.php';

Session::init();
Session::destroy();
exit();

?>