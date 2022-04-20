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
    function setMedia($querys, $mysqli, $playlistID) {
        $media = [];
        //$querys = array_unique($querys);
        foreach($querys as $query) {
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
                            <a href="/~cguynup/metube/view-media.php?mediaID=' . $mediaID . '" class="row">
                                <img  src="/~cguynup/metube/images/img_icon.jpg" class="col s12">
                                <div class="col s12">
                                    <h4>' . $title . '</h4><br>
                                    <p>' . $desc . '</p>
                                </div>
                            </a>
                            <form method="POST" action='. htmlspecialchars($_SERVER["PHP_SELF"]) . '>
                                <input type="button" name="remove" value="Remove" onclick="this.form.submit()">
                                <input type="hidden" name="media" value="' . $mediaID .'">
                                <input type="hidden" name="formtype" value="remove">
                                <input type="hidden" name="playlistID" value=' . $playlistID . '>
                                <!--<input type="submit" name="submit" value="Change">-->
                            </form>
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
                            <form method="POST" action='. htmlspecialchars($_SERVER["PHP_SELF"]) . '>
                                <input type="button" name="remove" value="Remove" onclick="this.form.submit()">
                                <input type="hidden" name="media" value="' . $mediaID .'">
                                <input type="hidden" name="formtype" value="remove">
                                <input type="hidden" name="playlistID" value=' . $playlistID . '>
                                <!--<input type="submit" name="submit" value="Change">-->
                            </form>
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
                            <form method="POST" action='. htmlspecialchars($_SERVER["PHP_SELF"]) . '>
                                <input type="button" name="remove" value="Remove" onclick="this.form.submit()">
                                <input type="hidden" name="media" value="' . $mediaID .'">
                                <input type="hidden" name="formtype" value="remove">
                                <input type="hidden" name="playlistID" value=' . $playlistID . '>
                                <!--<input type="submit" name="submit" value="Change">-->
                            </form>
                        </div>
                    ';
                    array_push($media, $string);
                }
            }
        }
        return array_unique($media);
    }

    // ensure user logged in before continuing
    if(!isset($_COOKIE['user'])) {
        header("Location: /~cguynup/metube/missingcookie.php", true, 301);
    }

    $querys = [];
    $playlistID = "";
    $playlistName = "";
    $isFavorite = "";
    if (isset($_REQUEST['playlistID'])) {
        $playlistID = $_REQUEST['playlistID'];
    } else {
        die("Could not get playlist ID! Is it valid?");
    }
    $userID = $_COOKIE['user'];

    if($_SERVER['REQUEST_METHOD'] == "POST") {
        if ($_POST['formtype'] == 'nameChange') {
            $name =$_POST['playlistname'];
            $query = "UPDATE Playlist SET playlistName='$name' WHERE playlistID = '$playlistID'";
            mysqli_query($mysqli, $query);
        } elseif ($_POST['formtype'] == 'remove') {
            $mediaID = $_POST['media'];
            $query = "DELETE FROM Playlist_Media WHERE playlistID = '$playlistID' AND mediaID = '$mediaID'";
            mysqli_query($mysqli, $query);
        } elseif ($_POST['formtype'] == 'deletePlaylist') {
            $query = "DELETE FROM Playlist_Media WHERE playlistID = '$playlistID'";
            mysqli_query($mysqli, $query);
            $query = "DELETE FROM Playlist WHERE playlistID = '$playlistID'";
            mysqli_query($mysqli, $query);
            $query = "DELETE FROM User_Playlist WHERE playlistID = '$playlistID'";
            mysqli_query($mysqli, $query);
            header("Location: /~cguynup/metube/profile-home.php" , true, 301);
        }
    }

    $query = "SELECT playlistName, favorites FROM Playlist WHERE playlistID = '$playlistID'";
    $results = mysqli_query($mysqli, $query);
    $row = mysqli_fetch_array($results);
    $playlistName = $row['playlistName'];
    $isFavorite = $row['favorites'];
    mysqli_free_result($results);
    $query = "SELECT mediaID FROM Playlist_Media WHERE playlistID = '$playlistID'";
    $results = mysqli_query($mysqli, $query);
    $querys = [];
    while($row = mysqli_fetch_array($results)) {
        $mediaID = $row['mediaID'];
        $query = "SELECT mediaID, mediaType, title, description, path FROM Media WHERE mediaID = '$mediaID'";
        array_push($querys, $query);
    }
    $media = setMedia($querys, $mysqli, $playlistID);
    
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
        <div class="profile-home row">
            <div class="row">
                <form method="POST" class="col 8" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <?php
                        if($isFavorite)
                            echo "<input type='text' disabled='disabled' name='playlistname' onchange='this.form.submit()' value=\"" . $playlistName . "\">";
                        else
                            echo "<input type='text' name='playlistname' onchange='this.form.submit()' value=\"" . $playlistName . "\">";
                    ?>
                    <input type="hidden" name="formtype" value="nameChange">
                    <?php echo "<input type='hidden' name='playlistID' value='$playlistID'>" ?>
                    <!--<input type="submit" name="submit" value="Change">-->
                </form>
                <form method="POST" class="col 4" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <?php
                        if($isFavorite)
                            echo "";
                        else
                            echo "<input type='button' name='deletePlaylist' onclick='this.form.submit()' value='Delete'>";
                    ?>
                    <input type="hidden" name="formtype" value="deletePlaylist">
                    <?php echo "<input type='hidden' name='playlistID' value='$playlistID'>" ?>
                    <!--<input type="submit" name="submit" value="Change">-->
                </form>
            </div>
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