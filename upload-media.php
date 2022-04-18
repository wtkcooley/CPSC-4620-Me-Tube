<?php

if($_SERVER['REQUEST_METHOD'] == "POST") {
// Setup
$target_dir = "/media/";
$target_file = $_COOKIE['user'] . date("_YmdHis_") . trim(basename($_FILES["fileToUpload"]["name"])); // "media/DATETIME_FILENAME.EXT"
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
date_default_timezone_set("America/New_York"); // maybe later we could grab timezone from ip location

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
mediaType varchar(5) THIS
mediaExt varchar(5) THIS
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
$uploadIP = ip2long($_SERVER['REMOTE_ADDR']);
$uploadTime = date("Y-m-d H:i:s"); // format YYYY-MM-DD hh:mm:ss
$title = $_POST['title'];
$description = $_POST['description'];
$path = $target_file; // this should be right i think
$type = $_POST['mediaType'];

$categoryID = 0;
if (isset($_POST['category']) && $_POST['category'] != 0) {
    $categoryID = $_POST['category'];
}

// query
$query = "INSERT INTO Media (uploadUser, uploadIP, uploadTime, title, description, mediaType, path) VALUES ('$uploadUser', '$uploadIP', '$uploadTime', '$title', '$description', '$type', '$path')";
mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

// category
$query = "SELECT mediaID FROM Media WHERE path='$path'";
$result = mysqli_query($mysqli, $query);
$mediaID = '';
while($line = mysqli_fetch_array($result)) {
    $mediaID = $line['mediaID'];
}

foreach($categoryID as $id) {
    $query = "INSERT INTO Media_Category VALUES ('$mediaID', '$id')";
    mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
}

// keywords
$words = explode(',', $_POST['keywords']);
foreach($words as $word) {
    $query = "INSERT INTO Media_Keyword VALUES ('$mediaID', '$word')";
    mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
}
header("Location: /~cguynup/metube/profile-home.php", true, 301);
exit();
}

?>
<!DOCTYPE html>

<!--IMPORT MATERIALIZE AND FONTS-->
<html>
<head>
    <title>Upload Media</title>
    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection" />
    <!--Import jquery-->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <!--Import materialize.js-->
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body class="blue-grey darken-3">
    <!--NAV BAR-->
    <nav>
        <div class="nav-wrapper row teal lighten-2">
            <a href="/metube/index.html" class="brand-logo left col-s1">Logo</a>
            <ul id="nav-mobile" class="right">
                <li><a class="waves-effect waves-light" href="/profile.html">Edit Profile</a></li>
                <li><a class="waves-effect waves-light btn teal darken-3 modal-trigger" href="/metube/login.html">Login</a></li>
            </ul>
            <form class="col s4 offset-s4">
                <div class="input-field">
                    <input id="search" type="search" required>
                    <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                    <i class="material-icons">close</i>
                </div>
            </form>
        </div>
    </nav>

    <div class="media-upload row">
        <form name="uploadForm" class="col s12" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" enctype="multipart/form-data">
            <div class="row">
                <h4>Upload Media</h4>
            </div>
            <!--FILE PATH-->
            <div class="row">
                <div class="col s12">
                    <div class="file-field input-field">
                        <div class="btn">
                            <span>File</span>
                            <input type="file" name="fileToUpload" id="fileToUpload">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text">
                        </div>
                    </div>
                </div>
            </div>

            <!--DESCRIPTION-->
            <div class="row">
                <div class="col s12">
                    <div class="input-field col s6">
                        <input name="title" id="title" type="text" class="validate">
                        <label for="title">Title</label>
                    </div>
                    <div class="input-field col s6">
                        <textarea name="description" id="description" class="materialize-textarea"></textarea>
                        <label for="description">Description</label>
                    </div>
                </div>
            </div>

            <!--METADATA-->
            <div class="row">
                <div class="col s12">
                    <!--CATEGORY-->
                    <div class="input-field col s6">
                        <select multiple id="category" name="category[]">
                            <option value="0" disabled selected>Choose your categorys</option>
                            <option value="1">Sports</option>
                            <option value="2">Family</option>
                            <option value="3">Comedy</option>
                            <option value="4">News</option>
                            <option value="5">Outdoors</option>
                            <option value="6">Drama</option>
                            <option value="7">Business</option>
                            <option value="8">Self-Care</option>
                            <option value="9">Hobbies</option>
                        </select>
                        <label>Category</label>
                    </div>

                    <!--TYPE-->
                    <div class="input-field col s6">
                        <select id="mediaType" name="mediaType">
                            <option value="0" disabled selected>Choose your media type</option>
                            <option value="VIDEO">Video</option>
                            <option value="IMAGE">Image or GIF</option>
                        </select>
                        <label>Media Type</label>
                    </div>

                    <!--KEYWORDS-->
                    <div class="input-field col s6">
                        <textarea name="keywords" id="keywords" class="materialize-textarea"></textarea>
                        <label for="keywords">Keywords (seperated by commas)</label>
                    </div>
                </div>
            </div>
            <!--SUBMIT-->
            <div class="row">
                <button onclick="this.form.submit()" type="submit" class="waves-effect waves-light btn col s6 offset-s3" value="submit" name="submit">submit</button>
            </div>
        </form>
    </div>

    <!--tbh idk what this does but it was here so im leaving it-->
    <script>
        $(document).ready(function() {
            $('select').formSelect();
        });
    </script>
</body>
</html>