<?php
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

    if(isset($_GET['mediaID'])) {
        $mediaID = $_GET['mediaID'];
    } else {
        $mediaID = $_REQUEST['mediaID'];
    }

    $query = "SELECT uploadUser, title, description, mediaType, path FROM Media WHERE mediaID = '$mediaID'";
    $result = mysqli_query($mysqli, $query);

    if (mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_array($result);
        $uuser = $row['uploadUser'];
        $title = $row['title'];
        $desc = $row['description'];
        $type = $row['mediaType'];
        $path = $row['path'];
    } else {
        die ("There was an issue opening the requested media.");
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
        if(!isset($_COOKIE['user'])) {
            header("Location: /~cguynup/metube/missingcookie.php", true, 301);
        }
        $playlistName = $_POST['playlistName'];

        // get playlists
        $user = $_COOKIE['user'];
        $query = "SELECT Playlist.playlistName, User_Playlist.playlistID FROM User_Playlist INNER JOIN Playlist ON User_Playlist.playlistID = Playlist.playlistID WHERE username='$user' AND playlistName='$playlistName'";
        $result = $mysqli->query($query);
        
        if(mysqli_num_rows($result) != 0) {
            $rarray = $result->fetch_assoc();
            $playlistID = $rarray['playlistID'];
            // add to playlist_media
            $query = "INSERT INTO Playlist_Media (playlistID, mediaID) VALUES ('$playlistID', '$mediaID')";
            mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        } else {
            // PLAYLIST DOES NOT EXIST
            $mediaID = $_REQUEST['mediaID'];
            // create playlist
            $query = "INSERT INTO Playlist (playlistName, createUser, favorites) VALUES ('$playlistName', '$user', 0)";
            mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
 
            // get playlist ID
            $query = "SELECT playlistID FROM Playlist WHERE playlistName='$playlistName' AND createUser='$user'";
            $result = $mysqli->query($query);
            $rarray = $result->fetch_assoc();
            $playlistID = $rarray['playlistID'];
            
            // insert into user playlist table
            $query = "INSERT INTO User_Playlist (username, playlistID) VALUES ('$user', '$playlistID')";
            mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
            // insert into playlist media table
            $query = "INSERT INTO Playlist_Media (playlistID, mediaID) VALUES ('$playlistID', '$mediaID')";
            mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        }

        // redirect back to page
        header("Location: /~cguynup/metube/view-media.php?mediaID=" . htmlspecialchars($mediaID));
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Media View</title>
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
        div.leftwindow {
            padding: 5%;
            width: 75%;
            height: 100%;
            max-height: 100%;
            float: left;
        }
        div.rightwindow {
            text-align: left;
            padding: 10px;
            word-wrap: break-word;
            word-break: break-word;
            width: 25%;
            float: left;
            clear: right;
            overflow-y: auto;
        }
        img {
            width: 90%;
        }
        video {
            width: 90%;
        }
        audio {
            width: 90%;
        }
    </style>
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
        <div class="row">
            <div class="leftwindow">
                <?php
                    if($type == 'IMAGE') {
                        echo "<img src=".$path." alt=".$title.">";
                    } else if($type == 'VIDEO') {
                        echo "<video controls>";
                        echo "    <source src=".$path." type='video/mp4'>";
                        echo "    <source src=".$path." type='video/mov'>";
                        echo "    File type not supported.               ";
                        echo "</video>";
                    } else {
                        echo "<audio controls>";
                        echo "    <source src=".$path." type='audio/mp4'>";
                        echo "    <source src=".$path." type='audio/mp3'>";
                        echo "    <source src=".$path." type='audio/wav'>";
                        echo "    File type not supported.               ";
                        echo "</audio>";
                    }
                ?>
            </div>
            <div class="rightwindow">
                <?php
                    echo "<h4>$title</h4><br>";
                    echo "<p>Uploaded by:</p>";
                    echo "<a href='/~cguynup/metube/channel.php?channelID=$uuser'>$uuser</a><br>";
                    echo "<p>$desc</p>";

                    $query = "SELECT word FROM Media_Keyword WHERE mediaID='$mediaID'";
                    $results = mysqli_query($mysqli, $query);
                    while($row = mysqli_fetch_array($results)){
                        echo "<div class='col s6 teal lighten-2 z-depth-2'>".$row['word']."</div>";
                    }

                ?>
            </div>
        </div>
        <div class="row" style="padding: 10px;">
            <a class="waves-effect waves-light btn " href=<?php 
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

            // Get info for download table
            $downloadUser = '';
            if(isset($_COOKIE['user'])) {
                $downloadUser = $_COOKIE['user'];
                $downloadIP = $_SERVER['REMOTE_ADDR'];
                $downloadTime = date("Y-m-d H:i:s"); // format YYYY-MM-DD hh:mm:ss
                // Set up query
                $query = "INSERT INTO Download (mediaID, downloadUser, downloadIP, downloadTime) VALUES ('$mediaID', '$downloadUser', '$downloadIP', '$downloadTime')";
                mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
            } else {
                $downloadIP = $_SERVER['REMOTE_ADDR'];
                $downloadTime = date("Y-m-d H:i:s"); // format YYYY-MM-DD hh:mm:ss
                // Set up query
                $query = "INSERT INTO Download (mediaID, downloadIP, downloadTime) VALUES ('$mediaID', '$downloadIP', '$downloadTime')";
                mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
            }

            echo $path; 
            ?> download><i class="material-icons left">download</i>Download</a>
        </div>

        <div class="row" style="padding: 10px;">
            <!--PLAYLIST-->
            <!--<a class="waves-effect waves-light btn "><i class="material-icons left">playlist_add</i>Add to playlist</a>-->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                <input name="playlistName" id="playlistName" type="text" placehodler="Enter playlist name...">
                <input type="hidden" name="mediaID" value="<?php echo $_GET["mediaID"];?>">
                <button class="btn waves-effect waves-light" type="submit" name="action">Add to Playlist</button>
            </form>
        </div>

        <div class="row">
            <form class="col s12" action="/~cguynup/metube/submitComment.php" method="POST">
                <div class="row">
                    <div class="input-field col s11">
                        <textarea name="comment" id="comment" class="materialize-textarea"></textarea>
                        <label for="comment">Add a comment...</label>
                    </div>
                    <div class="col s1">
                        <button class="btn waves-effect waves-light" type="submit" name="action">Submit Comment</button>
                    </div>
                </div>
                <!--hidden media id parameter-->
                <input type="hidden" name="mediaID" value="<?php echo "$mediaID"; ?>">
            </form>
        </div>
        
        <div class="row" style="padding: 10px;">
        <?php
            $query = "SELECT commentUser, commentTime, comment FROM Comment WHERE mediaID=$mediaID ORDER BY commentTime DESC";
            $result = $mysqli->query($query);

            while($row = mysqli_fetch_array($result)) {
                $commentUser = $row['commentUser'];
                $comment = $row['comment'];
                echo "<h6 style='color: white;'>$commentUser says:</h6><p style='color: white; opacity: .6;'>$comment</p>";
            }
        ?>
        </div>
    </body>
    <script>
        $(document).ready(function(){
            $('select').formSelect();
            $(".dropdown-trigger").dropdown();
        });
    </script>
</html>
