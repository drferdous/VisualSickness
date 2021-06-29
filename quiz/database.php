<?php
$servername='localhost';
$username='id14934272_admin';
$password='y8N(U%S+l@DTmaT%';
$dbname = "id14934272_database";
$conn=mysqli_connect($servername,$username,$password,"$dbname");
if(!$conn){
   die('Could not Connect My Sql:' .mysql_error());
}
?>