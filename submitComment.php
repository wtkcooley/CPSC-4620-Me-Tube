<?php
//had issues with the cached file automatically redirecting to the login page
//hence include every header that will make sure the browser does not use cache to get the page
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Expires: Mon, 21 Aug 2000 12:00:00 GMT");
header("Pragma: no-cache");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

// Ensure user is logged in
if(!isset($_COOKIE['user'])) {
    header("Location: /~wcooley/metube/missingcookie.php", true, 301);
}

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

// Setup
date_default_timezone_set("America/New_York"); // maybe later we could grab timezone from ip location
$comment = "";

// Get comment from POST
if (isset($_POST['comment'])) {
    $comment = addslashes($_POST['comment']);
} else {
    die("Error retrieving comment from POST");
}

// Get user from cookie
if (isset($_COOKIE['user'])) {
    $commentUser = $_COOKIE['user'];
} else {
    die("Error retrieving username from cookie");
}

// Get mediaID from hidden parameter
if (isset($_POST['mediaID'])) {
    $mediaID = $_POST['mediaID'];
} else {
    die("Error retrieving mediaID from POST");
}

// Get comment time
$commentTime = date("Y-m-d H:i:s");

// Setup query
$query = "INSERT INTO Comment (commentUser, mediaID, commentTime, comment)
    VALUES ('$commentUser', '$mediaID', '$commentTime', '$comment')";

mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

header("Location: /~wcooley/metube/view-media.php?mediaID=" . htmlspecialchars($mediaID));

?>