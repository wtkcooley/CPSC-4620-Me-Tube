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

    $media = "";
    $i = 0;

    // takes in an array of querys and pushes a media element for each non duplicated resulting row
    function setMedia($querys) {
        $querys = array_unique($querys);
        foreach($querys as $query) {
            $results = mysqli_query($mysqli, $query);
            $results = array_unique($result);
            $resultcount = mysqli_num_rows($sqlsearch);
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                $i++;

                if ($row['mediaType'] == "IMAGE") {
                    $media .= '
                        <div class="col s3">
                            <div class="row">
                                <image src="' . $row['path'] . '" class="col s12">
                                </image>
                                <div class="col s12">
                                    <h4>' . $row['title'] . '</h4>
                                    <p>' . $row['description'] . '</p>
                                </div>
                            </div>
                        </div>
                    ';
                } else {
                    $media .= '
                        <div class="col s3">
                            <div class="row">
                                <image src="/metube/media/video.png" class="col s12">
                                </image>
                                <div class="col s12">
                                    <h4>' . $row['title'] . '</h4>
                                    <p>' . $row['description'] . '</p>
                                </div>
                            </div>
                        </div>
                    ';
                }
                
                if($i == 4) {
                    $media .= "<div class='row'>";
                    $i = 0;
                }
            }
        }
    }

    
    $querys = ["SELECT mediaID, mediaType, mediaTitle, `description` FROM Media"];
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (isset($_GET['category']) && $_GET['category'] !== 0) {
            if (isset($_GET['search']) && $_GET['search'] !== "") {
                foreach($_GET['category'] as $category) {
                    foreach($word as $word) {
                        array_push($querys, "SELECT Media.mediaID, Media.mediaType, Media.mediaTitle, Media.description FROM
                            Media INNER JOIN (Media_Category INNER JOIN Category ON (Media_Category.CategoryID = Category.CategoryID))
                            ON (Media.mediaID = Category.mediaID INNER JOIN Media_Keyword ON (Media.mediaID = Media_Keyword.mediaID))
                            WHERE (Media_Keyword.word = {$word}) & (Category.categoryValue = {$category})");
                    }
                }
            } else {
                foreach($_GET['category'] as $category) {
                    array_push($querys, "SELECT Media.mediaID, Media.mediaType, Media.mediaTitle, Media.description FROM Media
                        INNER JOIN (Media_Category INNER JOIN Category ON (Media_Category.CategoryID = Category.CategoryID))
                        ON Media.mediaID = Category.mediaID WHERE (Category.categoryValue = {$category})");
                }
            }
        } elseif (isset($_GET['search']) && $_GET['search'] !== "") {
            $words = explode(' ', $_GET['search']);
            foreach($word as $word) {
                array_push($querys, "SELECT Media.mediaID, Media.mediaType, Media.mediaTitle, Media.description FROM Media
                    INNER JOIN Media_Keyword ON Media.mediaID = Media_Keyword.mediaID WHERE (Media_Keyword.word = {$word})");
            }
        }
    }
    setMedia($querys);
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
        <nav>
            <div class="nav-wrapper row teal lighten-2">
                <a href="/metube/index.html" class="brand-logo left col-s1">Logo</a>
                <ul id="nav-mobile" class="right">
                    <li><a class="waves-effect waves-light" href="/profile.html">Edit Profile</a></li>
                    <li><a class="waves-effect waves-light btn teal darken-3 modal-trigger" href="/metube/login.html">Login</a></li>
                </ul>
                <form class="col s4 offset-s4">
                    <div class="input-field">
                        <input id="search" type="search" required>
                        <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                        <i class="material-icons">close</i>
                    </div>
                </form>
            </div>
        </nav>
        <div class="profile-home row">
            <div class="row">
                <div class="col s12">
                    <div class="row">
                        <form class="col s12" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET">
                            <div class="input-field col s4">
                                <select id="category" name="category" multiple size=10>
                                <option value="0" disabled selected>Choose your categorys</option>
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
                            <div class="input-field">
                                <input id="search" type="search">
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
                
                </div>
            </div>
        </div>
    </body>
    <script>
        $(document).ready(function(){
            $('select').formSelect();
        });
    </script>
</html>