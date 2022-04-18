<!DOCTYPE html>

<!--IMPORT MATERIALIZE AND FONTS-->

<head>
    <title>Upload Media</title>
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
    <!--NAV BAR-->
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

    <div class="media-upload row">
        <form class="col s12" action="/metube/upload.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <h4>Upload Media</h4>
            </div>
            <!--FILE PATH-->
            <div class="row">
                <div class="col s12">
                    <div class="file-field input-field">
                        <div class="btn">
                            <span>File</span>
                            <input type="file" name="fileToUpload" id="fileToUpload">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text">
                        </div>
                    </div>
                </div>
            </div>

            <!--DESCRIPTION-->
            <div class="row">
                <div class="col s12">
                    <div class="input-field col s6">
                        <input name="title" id="title" type="text" class="validate">
                        <label for="title">Title</label>
                    </div>
                    <div class="input-field col s6">
                        <textarea id="description" class="materialize-textarea"></textarea>
                        <label for="description">Description</label>
                    </div>
                </div>
            </div>

            <!--METADATA-->
            <div class="row">
                <div class="col s12">
                    <!--CATEGORY-->
                    <div class="input-field col s6">
                        <select multiple id="category" name="category[]">
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
                        <label>Category</label>
                    </div>

                    <!--TYPE-->
                    <div class="input-field col s6">
                        <select id="mediaType" name="mediaType">
                            <option value="0" disabled selected>Choose your media type</option>
                            <option value="VIDEO">Video</option>
                            <option value="IMAGE">Image or GIF</option>
                        </select>
                        <label>Media Type</label>
                    </div>

                    <!--KEYWORDS-->
                    <div class="input-field col s6">
                        <textarea id="keywords" class="materialize-textarea"></textarea>
                        <label for="keywords">Keywords (seperated by commas)</label>
                    </div>
                </div>
            </div>
            <!--SUBMIT-->
            <div class="row">
                <div class="row">
                    <input type="submit" class="waves-effect waves-light btn col s6 offset-s3" value="submit" name="submit">
                    </input>
                </div>
            </div>
        </form>
    </div>

    <!--tbh idk what this does but it was here so im leaving it-->
    <script>
        $(document).ready(function() {
            $('select').formSelect();
        });
    </script>
</body>