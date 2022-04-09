<?php

// Save DB info
$db_host = 'mysql1.cs.clemson.edu';
$db_username = 'MeTube_sjoz';
$db_password = '4620Project!';
$db_name = 'MeTube_24dp';

// Connect to DB and handle error
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_name);
if (mysqli_connect_errno()) {
    // Connection failed!
    echo "Connection failed: " . mysqli_connect_error();
    exit();
}

// Take variables from HTML
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];

//mysqli_select_db($conn, $db_name);

// Setup query to see if user already exists
$query = "SELECT * FROM `User` WHERE `username` = '$username'";

// Send query and save results into $sqlsearch
$sqlsearch = mysqli_query($mysqli, $query);
$resultcount = mysqli_num_rows($sqlsearch); // <- count number of rows
if ($resultcount > 0) {
    // Search returned a result! Username already exists!
    echo "<script>alert(\"Username already exists\");</script>";
    echo "Error: " . mysqli_error();
    exit();
}
else {
    // Search returned no results
    // Setup query to insert user into DB
    $query = "INSERT INTO `User` (`username`, `password`, `email`, `fname`, `lname`) VALUES ('$username', '$password', '$email','$fname', '$lname')";
    
    // Send query
    sqli_query($mysqli, $query) or die(mysqli_error());
}

// Close connection to DB
mysqli_close();
?>