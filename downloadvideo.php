<?php
// Get mediaID from URL sent by video browse page
$mediaID = $_GET['mediaID'];

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

// Query for path based on mediaID
$query = "SELECT path FROM Media WHERE mediaID=$mediaID";
$result = $mysqli->query($query);

// Download from path
if (file_exists($path) && $result->num_rows == 1) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($path).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path));
    readfile($file);
    exit;
}
?>