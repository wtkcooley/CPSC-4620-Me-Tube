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

    $channelID = -1;
    if (isset($_GET['channelID'])) {
        $channelID = $_GET['channelID'];
    } else {
        die("Could not get channelID! Is it valid?");
    }

    $subed = FALSE;
    $media;
    $query = "SELECT mediaID, mediaType, title, path, description FROM Media WHERE (uploadUser = '$channelID')";
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
                        <a href="/~wcooley/metube/view-media.php?mediaID=' . $mediaID . '" class="row">
                            <img src="' . $path . '" class="col s12">
                            <div class="col s12">
                                <h4>' . $title . '</h4>
                                <p></p>
                            </div>
                        </a>
                    </div>
                ';
                array_push($media, $string);
            } else {
                $string = '
                    <div class="col s3">
                        <a href="/~wcooley/metube/view-media.php?mediaID=' . $mediaID . '" class="row">
                            <img src="/metube/images/videoThumbnail.png" class="col s12">
                            <div class="col s12">
                                <h4>' . $title . '</h4>
                                <p>' . $desc . '</p>
                            </div>
                        </a>
                    </div>
                ';
                array_push($media, $string);
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_COOKIE['user'])) {
            $subed = filter_input(INPUT_POST, 'sub', FILTER_SANITIZE_STRING);
            if($sub) {
                $query = "INSERT INTO Subscription (subscriber, subscribee) VALUES 
                ('{$channelID}', '{$userID}')";
                mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
            } else {
                $query = "DELETE FROM Subscription WHERE 
                channelID = $channelID AND userID = $userID";
                mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
            }
        } else {
            header("Location: /~wcooley/metube/missingcookie.php", true, 301);
        }
    }
    // Ensure user logged in before continuing
    if(isset($_COOKIE['user'])) {
        $userID = $_COOKIE['user'];
        $query = "SELECT * FROM Subscription WHERE subscribee = '$channelID' AND subscriber = '$userID'";
        $result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

        if($result->num_rows >= 1)
            $subed = TRUE;
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <title>MeTube Channel</title>
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
            <li><a href="/~wcooley/metube/profile-home.php">Profile</a></li>
            <li><a href="/~wcooley/metube/profile-edit.php">Edit Profile</a></li>
            <li><a href="/~wcooley/metube/messageScreen.php">Messages</a></li>
            <li><a href="/~wcooley/metube/upload-media.php">Upload</a></li>
            <li><a href="/~wcooley/metube/upload-media.php">Logout</a></li>
        </ul>
        <nav>
            <div class="nav-wrapper row teal lighten-2">
                <a href="/~wcooley/metube/index.php" class="brand-logo left col-s1">MeTube</a>
                <?php
                    if(isset($_COOKIE['user'])) {
                        echo '<ul id="nav-mobile" class="right">
                            <li><a class="dropdown-trigger" href="#!" data-target="page">' . $_COOKIE['user'] . '<i class="material-icons right">arrow_drop_down</i></a></li>
                        </ul>';
                    } else {
                        echo '<li><a href="/~wcooley/metube/login.php" class="waves-effect waves-light btn right">Login</a></li>';
                    }
                ?>
            </div>
        </nav>
        <div class="channel row">
            <div class="row">
                <div class="col s12">
                    <div class="row">
                        <h5 class="col s6">
                            <?php
                                echo $channelID;
                            ?>
                        </h5>
                        <form class="col s6" method="POST">
                            <?php
                                if ($subed) {
                                    echo "<input name='sub' type='checkbox' value='yes' checked='checked'/>";
                                } else {
                                    echo "<input name='sub' type='checkbox' value='yes' />";
                                }
                            ?>
                            <label for='sub'>Subscribed</label>
                            <input type="submit" name="submit" value="Submit"/>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
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
    </body>
    <script>
        $(document).ready(function(){
            $('select').formSelect();
            $(".dropdown-trigger").dropdown();
        });
    </script>
</html>