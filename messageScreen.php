<?php
  /*****************************************************************************************************
   * $_POST 'routine': (view, create)
   * $_POST 'target': user of interest
  ******************************************************************************************************/

  function getFrame($routine, $target) {
    if($routine == 'view') {
      if($target == '') {
        return "/~wcooley/metube/createMessage.php";
      } else {
        return "/~wcooley/metube/messageView.php?target=$target";
      }
    } else {
      if($target == '') {
        return "/~wcooley/metube/createMessage.php";
      } else {
        return "/~wcooley/metube/createMessage.php?target=$target";
      }
    }
  }

  //had issues with the cached file automatically redirecting to the login page
  //hence include every header that will make sure the browser does not use cache to get the page
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Expires: Mon, 21 Aug 2000 12:00:00 GMT");
  header("Pragma: no-cache");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

  //if a user is not logged in
  if(!isset($_COOKIE["user"])){
    header("Location: /~wcooley/metube/missingcookie.php", true, 301);
    exit;
  }

  $inbox = []; //will hold the users that have sent a message to the user ost recently
  $outbox = []; //will hold the users that have been sent a message by the user most recently

  if(isset($_POST['target'])) {
    $target = $_POST['target'];
  } else {
    $target = '';
  }
  if(isset($_POST['routine'])) {
    $routine = $_POST['routine'];
  } else {
    $routine = 'view';
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

  //this query will return the most recent sent and received messages between the current user and any other user
  //this is double the values that we need, but they are ordered so the first one found is the most recent
  $query = "SELECT DISTINCT ordered.sender, ordered.receiver, ordered.text , ordered.time FROM 
            (SELECT sender, receiver, text, dateSent as time FROM Message WHERE 
            sender='{$_COOKIE['user']}' or receiver='{$_COOKIE['user']}' ORDER BY dateSent DESC) AS ordered";
  $result = mysqli_query($mysqli, $query);

  while($line = mysqli_fetch_array($result)) {
    //check is the current user is in the sender or receiver
    //the query only returns 1 receieved and one sent, so just make sure the received wasn't found first
    if($line['sender'] == $_COOKIE['user'] && !array_key_exists(trim($line['receiver']), $outbox) && !array_key_exists(trim($line['receiver']), $inbox)){
      $outbox[trim($line['receiver'])] = $line['text'];
    }
    //check that the sent message wasn't found first
    else if($line['receiver'] == $_COOKIE['user'] && !array_key_exists(trim($line['sender']), $outbox) && !array_key_exists(trim($line['sender']), $inbox)){
      $inbox[trim($line['sender'])] = $line['text'];
    }
  }
  
  mysqli_free_result($result);
?>

    <!DOCTYPE html>
    <html>

    <head>
        <title>MeTube Message</title>
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
      body{
        overflow: hidden;
      }
    div.mainwrap {
      width: 100%;
      height: calc(100vh - 64px);
      display: flex;
    }
    div.leftwindow {
      width: 20%;
      height: 100%;
      max-height: 100%;
      float: left;
      border-right: solid;
      border-width: 1px;
    }
    div.newmessage {
      height: 10%;
      width: 100%;
      text-align: center;
      vertical-align: center;
    }
    div.boxtable {
      height: 90%;
      width: 100%;
      overflow-y: scroll;
    }
    div.rightwindow {
      width: 80%;
      min-height: 100%;
      float: left;
      clear: right;
    }
    td.boxes {
      vertical-align: top;
      text-align: left;
      padding: 15px;
      width: 20%;
      height: 100%;
      word-wrap: break-word;
      word-break: break-word;
      border-right-style: solid;
      border-width: 0 1px 0 0;
      color: white;
    }
    td.messageFrame {
      padding: 15px;
      text-align: center;
      width: 80%;
      height: 100%;
    }
    iframe.messagebox {
      width: 100%;
      height: 100%;
      border: 0;
    }
    .formbtn {
      border: none; 
      color: white; 
      padding: 16px 32px; 
      text-decoration: none; 
      margin: 4px 2px; 
      cursor: pointer; 
      border-radius: 3px;
    }
    .boxbtn {
      border: none; 
      color: white; 
      padding: 5px 10px; 
      text-decoration: none; 
      margin: 2px 1px; 
      cursor: pointer; 
      border-radius: 3px;
    }
  </style>
    </head>

    <body class="blue-grey darken-3">
    <ul id="page" class="dropdown-content">
        <li><a href="/~wcooley/metube/profile.php">Profile</a></li>
        <li><a href="/~wcooley/metube/edit-profile.php">Edit Profile</a></li>
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
        <div class="mainwrap">
          <div class="leftwindow">
            <div class="newmessage">    
              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                <input type="hidden" name="routine" value="create"/>
                <input class="z-depth-5 formbtn teal darken-2" type="submit" value="New Message" />
              </form>
            </div>
            <div class="boxtable">
              <table>
                <tr><td class="teal darken-3 z-depth-3" style="text-align: center; color: white; opacity: .5;">INBOX</td></tr>
                  <?php
                    foreach($inbox as $user => $message) {
                      echo "<tr><td style='height: 100px; width: 100%; overflow: hidden;>";
                      echo "<div class='row'><form action='".htmlspecialchars($_SERVER["PHP_SELF"])."' method='post'><input type='hidden' name='routine' value='view'/><input type='hidden' name='target' value='$user'/><input class='boxbtn teal darken-2' type='submit' value='$user'/></form></div>";
                      echo "<div class='row' style='color: white; opacity: .6;'>$message</div>";
                      echo "</td></tr>";
                    }
                  ?>
                <tr><td class="teal darken-3 z-depth-3" style="text-align: center; color: white; opacity: .5;">OUTBOX</td></tr>
                  <?php
                    foreach($outbox as $user => $message) {
                      echo "<tr><td style='height: 100px; width: 100%; overflow: hidden;>";
                      echo "<div class='row'><form action='".htmlspecialchars($_SERVER["PHP_SELF"])."' method='post'><input type='hidden' name='routine' value='view'/><input type='hidden' name='target' value='$user'/><input class='boxbtn teal darken-2' type='submit' value='$user'/></form></div>";
                      echo "<div class='row' style='color: white; opacity: .6;'>$message</div>";
                      echo "</td></tr>";
                    }
                  ?>
              </table>
            </div>
          </div>
          <div class="rightwindow">
            <iframe class="messagebox" src="<?php echo getFrame($routine, $target); ?>"></iframe>
          </div>
        </div>
        <script>
        $(document).ready(function(){
            $('select').formSelect();
            $(".dropdown-trigger").dropdown();
        });
        </script>
    </body>

    </html>

    