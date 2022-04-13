<?php
// Setup
$target_dir = "media/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if file already exists
if(file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}

/* Code to check file size, if we need to use it
if ($_FILES["fileToUpload"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
} */

/* Code to allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
  $uploadOk = 0;
} */

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
    die();

// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
        die();
    }
}

// File has been uploaded; Now insert metadata into database
/* Metadata info:
PK mediaID INT
FK uploadUser varchar(15)
uploadIP INT
uploadTime datetime
title varchar(50)
description varchar(240)
path varchar(100) */

// save DB info
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

// save Metadata
// mediaID will be autogenerated by mySQL
$uploadUser = $_COOKIE['user'];
$uploadIP = $_SERVER['REMOTE_ADDR'];
date_default_timezone_set("America/New_York"); // maybe later we could grab timezone from ip location
$uploadTime = date("Y-m-d H:i:s"); // format YYYY-MM-DD hh:mm:ss
$title = $_POST['title'];
$description = $_POST['description'];
$path = $target_file; // this should be right i think

// setup query
$query = "INSERT INTO Media (uploadUser, uploadIP, uploadTime, title, description, path) VALUES 
    ($uploadUser, $uploadIP, $uploadTime, $title, $description, $path)";

mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
exit();

?>