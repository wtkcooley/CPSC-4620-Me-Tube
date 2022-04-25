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
        header("Location: /~cguynup/metube/missingcookie.php", true, 301);
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
    $password = $_POST['password'];
    $email = $_POST['email'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];

    // Set up query
    $query = "UPDATE User SET password='$password', email='$email', fname='$fname', lname='$lname' WHERE username='{$_COOKIE['user']}'";

    // Send query
    mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    // Redirect to profile home
    header("Location: /~cguynup/metube/profile-home.php", true, 301);
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>MeTube Edit Profile</title>
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
    <style>
        .formbtn {
            border: none; 
            color: white; 
            padding: 16px 32px; 
            text-decoration: none; 
            margin: 4px 2px; 
            cursor: pointer; 
            border-radius: 3px;
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
        <div class="profile-info row" style="text-align: center; color: white;">
        <form class="col s12" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                <div class="row">
                    <h4 style="color: white;">Edit Profile</h4>
                </div>
                <div class="row">
                    <div class="input-field col s6">
                        <i class="material-icons prefix">person</i>
                        <input name="fname" pattern="[a-zA-Z -]{1,20}" maxlength="20" id="fname" type="text" class="validate" required>
                        <label for="fname">First Name</label>
                        <span class='helper-text'>Names can use letters, spaces, and dashes.</span>
                    </div>
                    <div class="input-field col s6">
                        <i class="material-icons prefix">person</i>
                        <input name="lname" pattern="[a-zA-Z -]{1,20}" maxlength="20" id="lname" type="text" class="validate" required>
                        <label for="lname">Last Name</label>
                        <span class='helper-text'>Names can use letters, spaces, and dashes.</span>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">email</i>
                        <input name="email" pattern="[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-zA-Z]{2,4}" maxlength="35" id="email" type="email" class="validate" required validate>
                        <label for="email">Email</label>
                        <span class='helper-text'>Emails must be in xxx@xxx.xxx format.</span>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">lock</i>
                        <input name="password" maxlength="20" id="password" type="password" class="validate" required>
                        <label for="password">Password</label>
                    </div>
                </div>
                <div class="row">
                    <input class="z-depth-5 formbtn teal lighten-2" type="submit" value="Update Profile" />
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