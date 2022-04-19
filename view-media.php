<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $playlistName = $_POST['playlistName'];

    // get playlists
    $query = "SELECT playlistName FROM User_Playlist INNER JOIN Playlist ON User_Playlist.playlistID = Playlist.playlistID WHERE username=$_COOKIE['user']";
    $result = $mysqli->query($query);
    
    if(in_array($playlistName, $result)) {
        // get ID of playlist
        $query = "SELECT playlistID FROM User_Playlist INNER JOIN Playlist ON User_Playlist.playlistID = Playlist.playlistID WHERE username=$_COOKIE['user'] AND playlistName=$playlistName";
        $result = $mysqli->query($query);
        // add to playlist_media
        $query = "INSERT INTO Playlist_Media (playlistID, mediaID) VALUES ($result['playlistID'], $_GET['mediaID'])";
        mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
    } else {
        // create playlist
        $query = "INSERT INTO Playlist (playlistName, createUser) VALUES ($playlistName, $_COOKIE['user'])";
        mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        // get playlist ID
        $query = "SELECT playlistID FROM Playlist WHERE playlistName='$playlistName' AND createUser='$_COOKIE['user']'";
        $result = $mysqli->query($query);
        // insert into user playlist table
        $query = "INSERT INTO User_Playlist (username, playlistID) VALUES ($_COOKIE['user'], $result['playlistID'])";
        mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        // insert into playlist media table
        $query = "INSERT INTO Playlist_Media (playlistID, mediaID) VALUES ($result['playlistID'], $_GET['mediaID'])";
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
<nav>
    <div class="nav-wrapper row teal lighten-2">
        <div class="left">
            <a href="/~cguynup/metube/index.php" class="left">
                <img src="/~cguynup/metube/images/metube_new.svg" style="height: 50px; vertical-align: middle;" />
            </a>
        </div>
        <form class="col s4 offset-s4">
            <div class="input-field">
                <input id="search" type="search" required>
                <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                <i class="material-icons">close</i>
            </div>
        </form>
        <ul id="nav-mobile" class="right">
            <li><a class="waves-effect waves-light" href="/~cguynup/metube/profile-edit.html">Edit Profile</a></li>
            <li><a class="waves-effect waves-light btn teal darken-3" href="/~cguynup/metube/login.php">Login</a></li>
        </ul>

    </div>
</nav>

<body class="blue-grey darken-3">
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

        <div class="row">
            <h5 class="col s6">Title</h5>
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
            $desc = $result -> fetch_assoc();

            // Print query results
            echo "$desc";
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
            echo "User $commentUser: $comment";
        }
        ?>

    </div>
</body>

</html>