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
    function setMedia($querys, $mysqli) {
        $media = [];
        $querys = array_unique($querys);
        foreach($querys as $query) {
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
        }
        return array_unique($media);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $querys = [];
        if (isset($_GET['category']) && $_GET['category'] !== 0) {
            if (isset($_GET['search']) && $_GET['search'] !== "") {
                foreach($_GET['category'] as $category) {
                    $words = explode(' ', $_GET['search']);
                    foreach($words as $word) {
                        strtolower($word);
                        array_push($querys, "SELECT Media.mediaID, Media.mediaType, Media.title, Media.description, Media.path FROM ((Media
                        INNER JOIN Media_Category ON Media.mediaID = Media_Category.mediaID) INNER JOIN Media_Keyword ON Media.mediaID = Media_Keyword.mediaID)
                            WHERE (Media_Keyword.word = '$word') AND (Media_Category.categoryID = '$category')");
                    }
                }
            } else {
                foreach($_GET['category'] as $category) {
                    array_push($querys, "SELECT Media.mediaID, Media.mediaType, Media.title, Media.description, Media.path FROM (Media
                        INNER JOIN Media_Category ON Media.mediaID = Media_Category.mediaID) WHERE (Media_Category.categoryID = '$category')");
                }
            }
        } elseif (isset($_GET['search']) && $_GET['search'] !== "") {
            $words = explode(' ', $_GET['search']);
            foreach($words as $word) {
                strtolower($word);
                array_push($querys, "SELECT Media.mediaID, Media.mediaType, Media.title, Media.description, Media.path FROM (Media
                    INNER JOIN Media_Keyword ON Media.mediaID = Media_Keyword.mediaID) WHERE (Media_Keyword.word = '$word')");
            }
        } else {
            array_push($querys, "SELECT mediaID, mediaType, title, path, description FROM Media");
        }
        $media = setMedia($querys, $mysqli);
    }
    
?>

<!DOCTYPE html>
<html>
    <head>
        <title>MeTube Browse</title>
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
                <div class="col s12">
                    <div class="row">
                        <form class="col s12" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET">
                            <div class="input-field col s4">
                                <select id="category" name="category[]" multiple size=10>
                                <option value="1">Sports</option>
                                <option value="2">Family</option>
                                <option value="3">Comedy</option>
                                <option value="4">News</option>
                                <option value="5">Outdoors</option>
                                <option value="6">Drama</option>
                                <option value="7">Business</option>
                                <option value="8">Self-Care</option>
                                <option value="9">Hobbies</option>
                                </select>
                                <label>Categorys</label>
                            </div>
                            <div class="input-field col s4">
                                <input name="search" id="search" type="search">
                                <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                                <i class="material-icons">close</i>
                            </div>
                            <div class="input-field col s4">
                                <input type="submit" value="Submit" />
                            </div>
                        </form>
                    </div>
                </div>
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