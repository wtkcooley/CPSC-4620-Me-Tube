<?php
$db_host = 'mysql1.cs.clemson.edu';
$db_username = 'MeTube_sjoz';
$db_password = '4620Project!';
$db_name = 'MeTube_24dp';
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_name);

if (mysqli_connect_errno()) {
    echo "Connection failed: " . mysqli_connect_error();
    exit();
}

$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM User WHERE username = '{$username}'";
$sqlsearch = mysqli_query($mysqli, $query);
$resultcount = mysqli_num_rows($sqlsearch);
if ($resultcount > 0) {
    header("Location: /unametaken.html", true, 301);
    exit();
}
else {
    $query = "INSERT INTO User (username, password, email, fname, lname) VALUES ('{$username}', '{$password}', '{$email}','{$fname}', '{$lname}')";
    mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
}

mysqli_close($mysqli);

header("Location: /accntsuccess.html", true, 301);
exit();
?>