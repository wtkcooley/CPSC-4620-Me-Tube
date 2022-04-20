<?php
// Ensure user is logged in
if(!isset$_COOKIE['user']) {
    header("Location: /~cguynup/metube/missingcookie.php", true, 301);
}

// Setup
date_default_timezone_set("America/New_York"); // maybe later we could grab timezone from ip location
$comment = "";

// Get comment from POST
if (isset($_POST['comment'])) {
    $comment = $_POST['comment'];
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
    VALUES ($commentUser, $mediaID, $commentTime, $comment)";

mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
exit();

?>