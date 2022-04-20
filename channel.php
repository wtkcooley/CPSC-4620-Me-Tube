<?php
    //had issues with the cached file automatically redirecting to the login page
    //hence include every header that will make sure the browser does not use cache to get the page
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Expires: Mon, 21 Aug 2000 12:00:00 GMT");
    header("Pragma: no-cache");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

    if(isset($_COOKIE['user']) && $_GET['channelID'] == $_COOKIE['user']) {
        header("Location: /~cguynup/metube/profile-home.php", true, 301);
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

    $channelID = -1;
    if (isset($_GET['channelID'])) {
        $channelID = $_GET['channelID'];
    } else {
        die("Could not get channelID! Is it valid?");
    }

    $subed = FALSE;
    $friends = FALSE;
    $pending = FALSE;
    $denied = FALSE;
    $media= [];
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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_COOKIE['user'])) {
            $userID = $_COOKIE['user'];
            if($_POST['formtype'] == 'sub') {
                $sub = filter_input(INPUT_POST, 'sub', FILTER_SANITIZE_STRING);
                if($sub) {
                    $query = "SELECT * FROM Subscription WHERE subscriber='$userID' AND subscribee='$channelID'";
                    $result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
                    if($result->num_rows == 0) {
                        $query = "INSERT INTO Subscription (subscriber, subscribee, dateSubscribed) VALUES 
                        ('{$userID}', '{$channelID}', NOW())";
                        mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
                    }
                } else {
                    $query = "SELECT * FROM Subscription WHERE subscriber='$userID' AND subscribee='$channelID'";
                    $result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
                    if($result->num_rows > 0) {
                        $query = "DELETE FROM Subscription WHERE 
                        subscriber = '$userID' AND subscribee = '$channelID'";
                        mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
                    }
                }
            } elseif ($_POST['formtype'] == 'friends') {
                $friend = filter_input(INPUT_POST, 'friend', FILTER_SANITIZE_STRING);
                if($friend) {
                    $query = "SELECT * FROM Relation WHERE (uname1 = '$channelID' AND uname2 = '$userID' AND status = 1)";
                    $result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
                    if($result->num_rows == 0) {
                        $query = "SELECT * FROM Relation WHERE (uname1 = '$channelID' AND uname2 = '$wcooley' AND status = 3)";
                        $result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
                        if($result->num_rows == 0) {
                            $query = "INSERT INTO Relation (uname1, uname2, status, dateModified) VALUES 
                            ('{$userID}', '{$channelID}', 1, NOW())";
                            mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
                        } else {
                            $query = "UPDATE Relation SET status = 1, dateModified=NOW() WHERE (uname1 = '$userID' AND uname2 = '$channelID')";
                            mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
                        }
                    } else {
                        $query = "UPDATE Relation SET status = 2, dateModified=NOW() WHERE (uname1 = '$channelID' AND uname2 = '$userID' AND status = 1)";
                        mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
                    }
                } else {
                    $query = "SELECT * FROM Relation WHERE (uname1 = '$channelID' AND uname2 = '$userID' AND status = 2)";
                    $result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
                    if($result->num_rows > 0)
                        $query = "DELETE FROM Relation WHERE (uname1 = '$channelID' AND uname2 = '$userID' AND status = 2)";
                    else
                        $query = "DELETE FROM Relation WHERE (uname1 = '$userID' AND uname2 = '$channelID' AND status = 2)";
                    mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
                }
            }
        
        } else {
            header("Location: /~cguynup/metube/missingcookie.php", true, 301);
        }
    }
    // Ensure user logged in before continuing
    if(isset($_COOKIE['user'])) {
        $userID = $_COOKIE['user'];
        $query = "SELECT * FROM Subscription WHERE subscribee = '$channelID' AND subscriber = '$userID'";
        $result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        if($result->num_rows >= 1)
            $subed = TRUE;
        
        $query = "SELECT * FROM Relation WHERE (uname1 = '$userID' AND uname2 = '$channelID' AND status = 1)";
        $result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        if($result->num_rows >= 1)
            $pending = TRUE;
        
        $query = "SELECT * FROM Relation WHERE (uname1 = '$channelID' AND uname2 = '$userID' AND status = 2) OR (uname2 = '$channelID' AND uname1 = '$userID' AND status = 2)";
        $result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        if($result->num_rows >= 1)
            $friends = TRUE;
        
        $query = "SELECT * FROM Relation WHERE (uname1 = '$userID' AND uname2 = '$channelID' AND status = 3)";
        $result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        if($result->num_rows >= 1)
            $denied = TRUE;
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
        <div class="channel row">
            <div class="row">
                <div class="col s12">
                    <div class="row">
                        <h5 class="col s6">
                            <?php
                                echo $channelID;
                            ?>
                        </h5>
                        <form class="col s3" method="POST">
                            <p>
                                <label>
                                    <?php
                                        if ($subed) {
                                            echo "<input onchange='this.form.submit()' name='sub' type='checkbox' checked='checked'/>";
                                        } else {
                                            echo "<input onchange='this.form.submit()' name='sub' type='checkbox' />";
                                        }
                                    ?>
                                    <span>Subscribed</span>
                                </label>
                            </p>
                            <input type='hidden' name='formtype' value='sub'>
                            <!--<input type="submit" name="submit" value="Submit"/>-->
                        </form>
                        <form class="col s3" method="POST">
                            <p>
                                <label>
                                    <?php
                                        if ($friends) {
                                            echo "<input onchange='this.form.submit()' name='friend' type='checkbox' checked='checked'/><span>Friends</span>";
                                        } elseif($pending) {
                                            echo "<input onchange='this.form.submit()' name='friend' type='checkbox' disabled='disabled'/><span>Friend Request Pending</span>";
                                        } elseif($denied) {
                                            echo "<input onchange='this.form.submit()' name='friend' type='checkbox' disabled='disabled'/><span>Friend Request Denied :(</span>";
                                        } else {
                                            echo "<input onchange='this.form.submit()' name='friend' type='checkbox'/><span>Friends</span>";
                                        }
                                    ?>
                                </label>
                            </p>
                            <input type='hidden' name='formtype' value='friends'>
                            <!--<input type="submit" name="submit" value="Submit"/>-->
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