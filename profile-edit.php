<?php
//had issues with the cached file automatically redirecting to the login page
//hence include every header that will make sure the browser does not use cache to get the page
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Expires: Mon, 21 Aug 2000 12:00:00 GMT");
header("Pragma: no-cache");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

if($_SERVER['REQUEST_METHOD'] == "POST") {
    // ensure user is logged in
    if(!isset($_COOKIE['user'])) {
        header("Location: /~wcooley/metube/missingcookie.php", true, 301);
    }
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

    /** I am NOT implementing the username update,
     * since it would have to update pretty much every
     * table in our database. That's just too much. */

    // Get info from POST
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];

    // Set up query
    $query = "UPDATE User SET password='$password', email='$email', fname='$fname', lname='$lname' WHERE username='$username'";

    // Send query
    mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    // Redirect to profile home
    header("Location: /~wcooley/metube/profile-home.php", true, 301);
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>MeTube Edit Profile</title>
        <link rel="icon" href="/~wcooley/metube/images/metube_new.svg">
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
            <li><a href="/~wcooley/metube/logout.php">Logout</a></li>
        </ul>
        <nav>
            <div class="nav-wrapper row teal lighten-2">
                <a href="/~wcooley/metube/browse.php" class="brand-logo left col-s1">MeTube</a>
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
        <div class="profile-info row">
            <form class="col s12" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                <div class="row">
                    <h4>Edit Profile</h4>
                </div>
                <div class="row">
                    <div class="input-field col s6">
                        <input id="first_name" name="first_name" type="text" class="validate">
                        <label for="first_name">First Name</label>
                    </div>
                    <div class="input-field col s6">
                        <input id="last_name" name="last_name" type="text" class="validate">
                        <label for="last_name">Last Name</label>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <i class="material-icons prefix">email</i>
                            <input id="email" name="email" type="email" class="validate">
                            <label for="email">Email</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <i class="material-icons prefix">account_circle</i>
                            <input id="username" name="username" type="text" class="validate">
                            <label for="username">Username</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <i class="material-icons prefix">lock</i>
                            <input id="password" name="password" type="password" class="validate">
                            <label for="password">Password</label>
                        </div>
                    </div>
                    <div class="row">
                        <input type="submit"
                            class="waves-effect waves-light btn col s12">
                            Submit</input>
                    </div>
                </div>
            </form>
        </div>
    </body>

    <script>
        $(document).ready(function() {
            M.updateTextFields();
            $('select').formSelect();
            $(".dropdown-trigger").dropdown();
        });
    </script>
</html>