<?php
    //had issues with the cached file automatically redirecting to the login page
    //hence include every header that will make sure the browser does not use cache to get the page
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Expires: Mon, 21 Aug 2000 12:00:00 GMT");
    header("Pragma: no-cache");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

    // ensure user is logged in
    if(!isset($_COOKIE['user'])) {
        header("Location: /~cguynup/metube/missingcookie.php", true, 301);
    }

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
    function setPlaylist($querys, $mysqli) {
        $playlist = [];
        $querys = array_unique($querys);
        foreach($querys as $query) {
            $results = mysqli_query($mysqli, $query);
            if($results) {
                while ($row = mysqli_fetch_array($results)) {
                    $playlistID = $row['playlistID'];
                    $playlistName = $row['playlistName'];
                    
                    $string = '
                        <div class="col s3">
                            <a class="row" href="/~cguynup/metube/playlist.php?playlistID=' . $playlistID . '">
                                <image src="/~cguynup/metube/images/placehodler.png" class="col s12">
                                </image>
                                <div class="col s12">
                                    <h4>' . $playlistName . '</h4>
                                </div>
                            </a>
                        </div>
                    ';
                    array_push($playlist, $string);
                }
            }
        }
        return array_unique($playlist);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $userID = $_COOKIE['user'];
        $querys = [];
        $media = [];
        $mediaIDs = [];
        $playlist = [];
        $playlistIDs = [];

        //get media
        $query = "SELECT mediaID, mediaType, title, path, description FROM Media WHERE (uploadUser = '$userID')";
        $results = mysqli_query($mysqli, $query);
        if($results) {
            while ($row = mysqli_fetch_array($results)) {
                $mediaID = $row['mediaID'];
            $mediaType = $row['mediaType'];
            $path = $row['path'];
            $title = $row['title'];
            $desc = $row['description'];
            if ($mediaType == "IMAGE") {
                $string = '
                    <div class="col s3">
                        <a href="/~cguynup/metube/view-media.php?mediaID=' . $mediaID . '" class="row">
                            <img  src="/~cguynup/metube/images/img_icon.jpg" class="col s12">
                            <div class="col s12">
                                <h4>' . $title . '</h4><br>
                                <p>' . $desc . '</p>
                            </div>
                        </a>
                    </div>
                ';
                array_push($media, $string);
            } else if ($mediaType == "VIDEO") {
                $string = '
                    <div class="col s3">
                        <a href="/~cguynup/metube/view-media.php?mediaID=' . $mediaID . '" class="row">
                            <img  src="/~cguynup/metube/images/video_icon.jpg" class="col s12">
                            <div class="col s12">
                                <h4>' . $title . '</h4><br>
                                <p>' . $desc . '</p>
                            </div>
                        </a>
                    </div>
                ';
                array_push($media, $string);
            } else {
                $string = '
                    <div class="col s3">
                        <a href="/~cguynup/metube/view-media.php?mediaID=' . $mediaID . '" class="row">
                            <img  src="/~cguynup/metube/images/audio_icon.jpg" class="col s12">
                            <div class="col s12">
                                <h4>' . $title . '</h4><br>
                                <p>' . $desc . '</p>
                            </div>
                        </a>
                    </div>
                ';
                array_push($media, $string);
                }
            }
        }

        //get playlists
        $query = "SELECT playlistID FROM User_Playlist WHERE (username = '$userID')";
        $results = mysqli_query($mysqli, $query);
        if($results) {
            while($row = mysqli_fetch_array($results)) {
                array_push($playlistIDs, $row['playlistID']);
            }
            foreach($playlistIDs as $playlistID) {
                $str = "SELECT playlistID, playlistName FROM Playlist WHERE playlistID = '$playlistID'";
                array_push($querys, $str);
            }
            $playlist = setPlaylist($querys, $mysqli);
        }

        $query = "SELECT fname, lname, email FROM User WHERE username='$userID'";
        $result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        $array = mysqli_fetch_array($result);
        $name = $array['fname'] . ' ' . $array['lname'];
        $email = $array['email'];
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>MeTube Profile Home</title>
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
    <style>
        div.mainwrap {
            width: 100%;
            height: calc(100vh - 64px);
            display: flex;
        }
        div.leftwindow {
            width: 75%;
            height: 100%;
            max-height: 100%;
            float: left;
            border-right: solid;
            border-width: 1px;
            overflow-y: scroll;
        }
        div.rightwindow {
            width: 25%;
            min-height: 100%;
            float: left;
            clear: right;
            overflow-y: scroll;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }
    </style>
    </head>
    <body class="blue-grey darken-3" style="overflow: hidden;">
        <ul id="page" class="dropdown-content">
            <li><a href="/~cguynup/metube/profile-home.php">Profile</a></li>
            <li><a href="/~cguynup/metube/profile-edit.php">Edit Profile</a></li>
            <li><a href="/~cguynup/metube/messageScreen.php">Messages</a></li>
            <li><a href="/~cguynup/metube/upload-media.php">Upload</a></li>
            <li><a href="/~cguynup/metube/logout.php">Logout</a></li>
        </ul>
        <nav>
            <div class="nav-wrapper row teal lighten-2">
                <a href="/~cguynup/metube/browse.php" class="brand-logo left col-s1">MeTube</a>
                <?php
                    if(isset($_COOKIE['user'])) {
                        echo '<ul id="nav-mobile" class="right">
                            <li><a class="dropdown-trigger" href="#!" data-target="page">' . $_COOKIE['user'] . '<i class="material-icons right">arrow_drop_down</i></a></li>
                        </ul>';
                    } else {
                        echo '<li><a href="/~cguynup/metube/login.php" class="waves-effect waves-light btn right">Login</a></li>';
                    }
                ?>
            </div>
        </nav>
        <div class="mainwrap">            
            <div class="leftwindow">
                <div class="profile-home row">
                    <div class="z-depth-2 teal darken-2 row" style="text-align: center; opacity: .6;">
                        <div class="col s12">
                            <div class="row">
                                <h5 class="col s4">Name: 
                                    <?php echo $name; ?>
                                </h5>
                                <h5 class="col s4">Username: <?php echo $userID; ?></h5>
                                <h5 class="col s4">Email: <?php echo $email; ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="z-depth-2 teal darken-2 row" style="text-align: left; opacity: .6;">
                            <h4>My Playlists:</h4>
                        </div>
                        <div class="col s12">
                            <div class="row">
                                <?php
                                    $i = 0;
                                    foreach($playlist as $p) {
                                        $i++;
                                        echo $p;
                                        if($i == 4) {
                                            echo "</div>\n<div class=row>\n";
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="z-depth-2 teal darken-2 row" style="text-align: left; opacity: .6;">
                            <h4>My Media:</h4>
                        </div>
                        <div class="col s12">
                            <div class="row">
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
                </div>
            </div>
            <div class="rightwindow">
                <iframe src="/~cguynup/metube/contactlist.php"></iframe>
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