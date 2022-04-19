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
        $mediaID = $_GET['channelID'];
    } else {
        die("Could not get channelID! Is it valid?");
    }
    $userID = $_COOKIE['user'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $query = "INSERT INTO Subscription (subscriber, subscribee) VALUES 
        ('{$channelID}', '{$userID}')";
        mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
    }

    $query = "SELECT * FROM Subscription WHERE subscrbee=$channelID AND subscriber=$userID";
    $result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    $subed = FALSE;
    if($result->num_rows >= 1)
        $subed = TRUE;

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
                <a href="/~cguynup/metube/index.php" class="brand-logo left col-s1">MeTube</a>
                <ul id="nav-mobile" class="right">
                    <li><a class="waves-effect waves-light" href="/profile-edit.html">Edit Profile</a></li>
                    <li><a class="dropdown-trigger" href="#!" data-target="page">Dropdown<i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
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
                            <p>
                            <label>
                                <?php
                                    if ($subed) {
                                        echo "<input type='checkbox' checked='checked' onchange='this.form.submit()'/>
                                        <span>Subscribed</span>";
                                    } else {
                                        echo "<input type='checkbox' onchange='this.form.submit()'/>
                                        <span>Subscribed</span>";
                                    }
                                ?>
                            </label>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="row">
                        
                    </div>
                </div>
            </div>
            
        </div>
    </body>
</html>