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

    // takes in an array of querys and pushes a media element for each non duplicated resulting row
    function setPlaylist($querys, $mysqli) {
        $playlist = [];
        //$querys = array_unique($querys);
        foreach($querys as $query) {
            $results = mysqli_query($mysqli, $query);
            if($result) {
                while ($row = $results->fetch_assoc()) {
                    $playlistName = $row['playlistID'];
                    $playlistName = $row['playlistName'];
                    
                    $string = '
                        <div class="col s3">
                            <div class="row" href="/~cguynup/metube/?playlistID=' . $playlistID . '">
                                <image src="/~cguynup/metube/images/placehodler.png" class="col s12">
                                </image>
                                <div class="col s12">
                                    <h4>' . $playlistName . '</h4>
                                    <p></p>
                                </div>
                            </div>
                        </div>
                    ';
                    $playlist.array_push($string);
                }
            }
        }
        return array_unique($playlist);
    }

    $userID = $_COOKIE['user'];
    $querys = [];
    $query = "SELECT playlistID FROM User_Playlist WHERE (username = $userID)";
    $results = mysqli_query($mysqli, $query);
    while($row = mysqli_fetch_array($results)) {
        array_push($playlistIDS, $row['playlistID']);
    }
    foreach($playlistIDs as $playlistID) {
        $str = "SELECT playlistID, playlistName FROM Playlist WHERE playlistID = '$playlistID'";
        array_push($querys, $str);
    }
    $playlist = setPlaylist($querys, $mysqli);

    $query = `SELECT fname, lname, email FROM User WHERE username=$userID`;
    $result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
    $array = sql_fetch_row($result);
    $name = $array['fname'] . $array[$lname];
    $email = $array['email'];
    
?>
<!DOCTYPE html>
<html>
    <head>
        <title>MeTube</title>
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
            <li><a href="/~cguynup/metube/profile.php">Profile</a></li>
            <li><a href="/~cguynup/metube/edit-profile.php">Edit Profile</a></li>
            <li><a href="/~cguynup/metube/messageScreen.php">Messages</a></li>
            <li><a href="/~cguynup/metube/upload-media.html">Upload</a></li>
        </ul>
        <nav>
            <div class="nav-wrapper row teal lighten-2">
                <a href="/~cguynup/metube/index.html" class="brand-logo left col-s1">MeTube</a>
                <ul id="nav-mobile" class="right">
                    <li><a class="waves-effect waves-light" href="/profile.html">Edit Profile</a></li>
                    <li><a class="dropdown-trigger" href="#!" data-target="page">Dropdown<i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
            </div>
        </nav>
        <div class="profile-home row">
            <div class="row">
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
                <div class="col s12">
                    <h4>My Playlist:</h4>
                    <div class="row">
                        <?php
                                $i = 0;
                                foreach($playlists as $p) {
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
                <div class="col s12">
                    <h4>My Media:</h4>
                    <div class="row">

                    </div>
                </div>
            </div>
            <iframe src="/~cguynup/metube/contactlist.php"></iframe>
        </div>
    </body>
    <script>
        $(document).ready(function(){
            $('select').formSelect();
            $(".dropdown-trigger").dropdown();
        });
    </script>
</html>