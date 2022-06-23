<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["time"])){
	header("Location: 404");
	exit();
}
include_once "lib/Session.php";
    session_start();
    Session::set('time_offset',intval($_POST['time']));
?>