<?php
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

// Ensure user is logged in before continuing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if(!isset($_COOKIE['user'])) {
        header("Location: /~cguynup/metube/missingcookie.php", true, 301);
    }
    $playlistName = $_POST['playlistName'];

    // get playlists
    $user = $_GET['user'];
    $query = "SELECT playlistName FROM User_Playlist INNER JOIN Playlist ON User_Playlist.playlistID = Playlist.playlistID WHERE username=$user";
    $result = $mysqli->query($query);
    
    if(in_array($playlistName, $result)) {
        // get ID of playlist
        $query = "SELECT playlistID FROM User_Playlist INNER JOIN Playlist ON User_Playlist.playlistID = Playlist.playlistID WHERE username=$user AND playlistName=$playlistName";
        $result = $mysqli->query($query);
        // add to playlist_media
        $mediaID = $_GET['mediaID'];
        $playlistID = $result['playlistID'];
        $query = "INSERT INTO Playlist_Media (playlistID, mediaID) VALUES ('$playlistID', '$mediaID')";
        mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
    } else {
        $mediaID = $_GET['mediaID'];
        $playlistID = $result['playlistID'];
        // create playlist
        $query = "INSERT INTO Playlist (playlistName, createUser, favorites) VALUES ($playlistName, $user, 0)";
        mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        // get playlist ID
        $query = "SELECT playlistID FROM Playlist WHERE playlistName='$playlistName' AND createUser='$user'";
        $result = $mysqli->query($query);
        // insert into user playlist table
        $query = "INSERT INTO User_Playlist (username, playlistID) VALUES ('$user', '$playlistID')";
        mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        // insert into playlist media table
        $query = "INSERT INTO Playlist_Media (playlistID, mediaID) VALUES ('$playlistID', '$mediaID')";
        mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>MeTube Media Viewer</title>
    <link rel="icon" href="/~cguynup/metube/images/metube_new.svg">
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

<!--INSERT NAVBAR INCLUSION-->
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
    <div class="view-media row">
        <!--MEDIA PLAYER-->
        <div class="row">
            <iframe width="720" height="576" src="
            <?php
            // Get mediaID from URL sent by video browse page
            $mediaID = -1;
            if (isset($_GET['mediaID'])) {
                $mediaID = $_GET['mediaID'];
            } else {
                die("Could not get mediaID! Is it valid?");
            }

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

            // Echo back path
            if($result->num_rows == 1) {
                $path = $result -> fetch_assoc();
                echo "$path";
            }
            ?>" class="col s12">
            </iframe>
        </div>

        <!--TITLE-->
        <div class="row">
            <h5 class="col s6"><?php 
            // Get mediaID from URL sent by video browse page
            $mediaID = -1;
            if (isset($_GET['mediaID'])) {
                $mediaID = $_GET['mediaID'];
            } else {
                die("Could not get mediaID! Is it valid?");
            }

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

            // Query for title based on mediaID
            $query = "SELECT title FROM Media WHERE mediaID=$mediaID";
            $result = $mysqli->query($query);
            
            // Echo back path
            if($result->num_rows == 1) {
                $title = $result -> fetch_assoc();
                echo "{$title['title']}";;
            }
            ?></h5>

            <div class="col s6">
                <!--DOWNLOAD/PLAYLIST BUTTONS-->
                <div class="right">
                    <a class="waves-effect waves-light btn " href="
                    <?php
                    // Get mediaID from URL sent by video browse page
                    $mediaID = -1;
                    if (isset($_GET['mediaID'])) {
                        $mediaID = $_GET['mediaID'];
                    } else {
                        die("Could not get mediaID! Is it valid?");
                    }

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


                    // Get info for download table
                    $downloadUser = '';
                    if(isset($_COOKIE['user'])) {
                        $downloadUser = $_COOKIE['user'];
                    } else {
                        $downloadUser = 'NOTLOGGEDIN';
                    }
                    $downloadIP = $_SERVER['REMOTE_ADDR'];
                    $downloadTime = date("Y-m-d H:i:s"); // format YYYY-MM-DD hh:mm:ss

                    // Set up query
                    $query = "INSERT INTO Download (mediaID, downloadUser, downloadIP, downloadTime) VALUES ('$mediaID', '$downloadUser', '$downloadIP', '$downloadTime')";
                    mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

                    // Echo back path
                    if($result->num_rows == 1) {
                        $path = $result -> fetch_assoc();
                        echo "$path";
                    }
                    ?>" download><i class="material-icons left">download</i>Download</a>

                    <!--PLAYLIST-->
                    <!--<a class="waves-effect waves-light btn "><i class="material-icons left">playlist_add</i>Add to playlist</a>-->
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                        <input name="playlistName" type="text"/>
                        <label>Add To Playlist</label>
                        <button class="btn waves-effect waves-light" type="submit" name="action">Add to Playlist</button>
                    </form>
                </div>
            </div>
        </div>

        <!--CHANNEL-->
        <div class="row">
            <?php
            // Get mediaID from URL sent by video browse page
            $mediaID = -1;
            if (isset($_GET['mediaID'])) {
                $mediaID = $_GET['mediaID'];
            } else {
                echo "Could not get mediaID! Is it valid?";
                die();
            }

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

            // Query for channel name based on mediaID
            $query = "SELECT uploadUser FROM Media WHERE mediaID=$mediaID";
            $result = $mysqli->query($query);
            $uploadUser = $result -> fetch_array();

            // Echo profile link
            $link_address = "/~cguynup/metube/channel.php?channelID={$uploadUser['uploadUser']}";
            echo "<a href="'.$link_address.'">Upload User: {$uploadUser['uploadUser']}</a>";
            ?>
        </div>

        <!--DESCRIPTION FETCH-->
        <div class="row">
            <p class="col s12">Description</p>

            <?php
            // Get mediaID from URL sent by video browse page
            $mediaID = -1;
            if (isset($_GET['mediaID'])) {
                $mediaID = $_GET['mediaID'];
            } else {
                echo "Could not get mediaID! Is it valid?";
                die();
            }

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
            $query = "SELECT description FROM Media WHERE mediaID=$mediaID";
            $result = $mysqli->query($query);
            $desc = $result -> fetch_array();

            // Print query results
            echo "{$desc['description']}";
            ?>
        </div>

        <!--COMMENT SUBMISSION-->
        <div class="row">
            <form class="col s12" action="submitComment.php" method="POST">
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
                <input type="hidden" name="mediaID" value=
                <?php
                $mediaID = -1;
                if (isset($_GET['mediaID'])) {
                    $mediaID = $_GET['mediaID'];
                    echo $mediaID;
                } else {
                    die("Could not get mediaID! Is it valid?");
                }
                ?>/>
            </form>
        </div>

        <!--DISPLAY COMMENTS-->
        <?php
        // Get mediaID from URL sent by video browse page
        $mediaID = -1;
        if (isset($_GET['mediaID'])) {
            $mediaID = $_GET['mediaID'];
        } else {
            die("Could not get mediaID! Is it valid?");
        }

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

        // Set up query
        $query = "SELECT commentUser, commentTime, comment FROM Comment WHERE mediaID=$mediaID ORDER BY commentTime DESC";
        $result = $mysqli->query($query);

        while($row = mysqli_fetch_array($result)) {
            $commentUser = $row['commentUser'];
            $comment = $row['comment'];
            echo "$commentUser says: $comment";
            echo "<br />";
            echo "<br />";
        }
        ?>

    </div>
</body>

</html>