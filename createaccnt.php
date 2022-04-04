<?php
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];

$db_host = 'mysql1.cs.clemson.edu';
$db_username = 'MeTube_sjoz';
$db_password = '4620Project!';
$db_name = 'MeTube_24dp';
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
mysqli_select_db($conn, $db_name);

$query = "SELECT * FROM `User` WHERE `username` = '$username'";
$sqlsearch = $conn->query($query);
$resultcount = mysqli_numrows($sqlsearch);
if ($resultcount > 0) {
    echo "<script>alert(\"Username already exists\");</script>";
    die(mysqli_error());
}
else {
    $sql = "INSERT INTO 'User' ('username', 'password', 'email', 'fname', 'lname') VALUES ('$username', '$password', '$email','$fname', '$lname')";
    $conn->query($sql) or die(mysqli_error());
}

$conn->close();
?>