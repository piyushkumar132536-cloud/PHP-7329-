<?php

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'my_db';


$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}



if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$firstnm=$_POST['fnm'];
	$lastnm=$_POST['lnm'];
	$eml=$_POST['email'];
	$password=$_POST['pass'];
	

}
$sql = "INSERT INTO register (firstname, lastname, email,password)
VALUES ('$firstnm', '$lastnm', '$eml', '$password')";

echo $sql;
if (mysqli_query($conn, $sql)) {
  header("Location:index.html");
} 
else {
  echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);
?>