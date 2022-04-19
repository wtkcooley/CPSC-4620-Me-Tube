<?php

header( "refresh:10; url=/~cguynup/metube/metube/login.php" );

?>
<!DOCTYPE html>
    <html>
    <head>
        <title>MeTube Missing Cookie</title>
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

    <body style="background-image: url('/~cguynup/metube/metube/images/main_bg.jpg');background-repeat: no-repeat">
        <div class="z-depth-5" style="color: #37474f; background-color: white; padding: 50px; border-radius: 15px 50px; margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); text-align: center;">
            <h4>Uh oh!</h4><br>
            <p>Looks like you tried to access a page that requires you to be logged in!</p>
            <p>You will be redirected to the login page shortly.</p>
            <a href="/~cguynup/metube/metube/login.php"> Click here if it did not load...</a>
        </div>
    </body>
    </html>