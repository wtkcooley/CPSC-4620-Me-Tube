<?php
    //had issues with the cached file automatically redirecting to the login page
    //hence include every header that will make sure the browser does not use cache to get the page
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Expires: Mon, 21 Aug 2000 12:00:00 GMT");
    header("Pragma: no-cache");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

    //connect to our database
    $db_host = 'mysql1.cs.clemson.edu';
    $db_username = 'MeTube_sjoz';
    $db_password = '4620Project!';
    $db_name = 'MeTube_24dp';
    $mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_name);

    if (mysqli_connect_errno()) {
        echo "Connection failed: " . mysqli_connect_error();
        exit();
    }

    // takes in an array of querys and pushes a media element for each non duplicated resulting row
    function setMedia($querys, $mysqli) {
        $media = [];
        //$querys = array_unique($querys);
        foreach($querys as $query) {
            echo $query;
            $results = mysqli_query($mysqli, $query);
            while ($row = $results->fetch_assoc()) {
                $mediaID = $row['mediaID'];
                $mediaType = $row['mediaType'];
                $path = $row['path'];
                $title = $row['title'];
                $desc = $row['description'];
                if ($mediaType == "IMAGE") {
                    $string = '
                        <div class="col s3">
                            <div href="/~cguynup/metube/view-media.php?mediaID=' . $mediaID . '" class="row">
                                <image src="' . $path . '" class="col s12">
                                </image>
                                <div class="col s12">
                                    <h4>' . $title . '</h4>
                                    <p></p>
                                </div>
                            </div>
                        </div>
                    ';
                    $media.array_push($string);
                } else {
                    $string = '
                        <div class="col s3">
                            <div href="/~cguynup/metube/view-media.php?mediaID=' . $mediaID . '" class="row">
                                <image src="/~cguynup/metube/images/videoThumbnail.png" class="col s12">
                                </image>
                                <div class="col s12">
                                    <h4>' . $title . '</h4>
                                    <p>' . $desc . '</p>
                                </div>
                            </div>
                        </div>
                    ';
                    $media.array_push($string);
                }
            }
        }
        return array_unique($media);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // ensure user logged in before continuing
        if(isset($_COOKIE['user'])) {
            header("Location: /~cguynup/metube/missingcookie.php", true, 301);
        }

        $querys = [];
        $playlsitID = "";
        if (isset($_GET['playlistID'])) {
            $playlistID = $_GET['playlistID'];
        } else {
            die("Could not get playlist ID! Is it valid?");
        }
        $userID = $_COOKIE['user'];
        $query = "SELECT mediaID FROM Playlist_Media WHERE playlistID = '$playlistID'";
        $results = mysqli_query($mysqli, $query);
        while($row = mysqli_fetch_row($results)) {
            $mediaID = $row['mediaID'];
            $query = "SELECT mediaID, mediaType, mediaTitle, description FROM Media WHERE mediaID = '$mediaID'";
            array_push($querys, $query);
        }
        $setMedia($querys, $mysqli);


        foreach($_GET['category'] as $category) {
            $words = explode(' ', $_GET['search']);
            foreach($words as $word) {
                array_push($querys, "SELECT Media.mediaID, Media.mediaType, Media.mediaTitle, Media.description FROM
                    Media INNER JOIN (Media_Category INNER JOIN Category ON (Media_Category.CategoryID = Category.CategoryID))
                    ON (Media.mediaID = Category.mediaID INNER JOIN Media_Keyword ON (Media.mediaID = Media_Keyword.mediaID))
                    WHERE (Media_Keyword.word = '$word') & (Category.categoryValue = '$category')");
            }
        }
        $media = setMedia($querys, $mysqli);
    }
    
?>
<!DOCTYPE html>
<html>
    <head>
        <title>MeTube Playlist</title>
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
        <ul id="page" class="dropdown-content">
            <li><a href="/~cguynup/metube/profile-home.php">Profile</a></li>
            <li><a href="/~cguynup/metube/profile-edit.php">Edit Profile</a></li>
            <li><a href="/~cguynup/metube/messageScreen.php">Messages</a></li>
            <li><a href="/~cguynup/metube/upload-media.php">Upload</a></li>
        </ul>
        <nav>
            <div class="nav-wrapper row teal lighten-2">
                <a href="/~cguynup/metube/index.php" class="brand-logo left col-s1">MeTube</a>
                <ul id="nav-mobile" class="right">
                    <li><a class="waves-effect waves-light" href="/profile-edit.html">Edit Profile</a></li>
                    <li><a class="dropdown-trigger" href="#!" data-target="page">Dropdown<i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
            </div>
        </nav>
        <div class="profile-home row">
            <div class="row">
                <div class="col s12">
                    <?php
                        $i = 0;
                        foreach($media as $m) {
                            $i++;
                            echo $m;
                            if($i == 4) {
                                echo "</div>\n<div class=row>\n";
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
    <script>
        $(document).ready(function(){
            $('select').formSelect();
            $(".dropdown-trigger").dropdown();
        });
    </script>
</html>