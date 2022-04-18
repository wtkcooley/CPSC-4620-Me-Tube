<?php
  //had issues with the cached file automatically redirecting to the login page
  //hence include every header that will make sure the browser does not use cache to get the page
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Expires: Mon, 21 Aug 2000 12:00:00 GMT");
  header("Pragma: no-cache");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

  //this is called in an iframe by message screen, so we can expect a target to be provided
  $target = $_REQUEST['target'];

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

  //if send was pressed, add the message to the db then re-query all messages
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = "INSERT INTO Message (sender, receiver, text, dateSent) VALUES ('{$_COOKIE['user']}', '$target', '{$_POST['text']}', NOW())";
    mysqli_query($mysqli, $query);
  }

  $query = "SELECT sender, text FROM Message WHERE (sender='{$_COOKIE['user']}' AND receiver='$target') OR (sender='$target' AND receiver='{$_COOKIE['user']}') ORDER BY dateSent ASC";
  $result = mysqli_query($mysqli, $query);
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

    <style>
        body {
            background-color: white;
        }
        div.main {
          height: 100vh;
          width: 100%;
          display: flex;
          flex-direction: column;
          overflow: hidden;
        }
        div.header {
          max-height: 5%;
          width: 100%;
          text-align: center;
          color: #37474f;
        }
        div.message-window {
          width: 100%;
          max-height: 70%;
          overflow-y: auto;
        }
        div.text-window {
          width: 100%;
          max-height: 20%;
          text-align: right;
        }
        div.sent {
            width: 40%;
            float: right;
            clear: both;
            color: white;
            word-wrap: break-word;
            margin: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        div.received {
            width: 40%;
            float: left;
            clear: both;
            color: white;
            word-wrap: break-word;
            margin: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        p.error {
          text-align: center;          
        }
        .formbtn {
          float: right;
          background: #37474f; 
          border: none; 
          color: white;
          vertical-align: bottom;
          text-decoration: none; 
          cursor: pointer; 
          border-radius: 5px;
          width: 10%;
          height: 100%;
          opacity: .9;
        }
        .formbtn:hover {
          opacity: 1;
        }
        input[type="text"] {
          float: right;
          clear: left;
          color: white;
          opacity: .5;
          padding: 5px;
          height: 100%;
          width: 90%;
          border: 0;
          border-radius: 5px;
        }
    </style>
  </head>
  <body>
    <div class="main">
    <div class="header row">
        <?php echo "Messages with $target"; ?>
    </div>
    <div class="message-window row">
        <?php
          if (mysqli_num_rows($result) < 1) {
            echo "<p2 class='error blue-grey darken-3'> No messages yet... </p>";
          } else {
            while($line = mysqli_fetch_array($result)) {
              if ($line['sender'] == $_COOKIE['user']) {
                echo "<div class='sent teal darken-1 z-depth-2'>";
                echo $line['text'];
                echo "</div>";
              } else {
                echo "<div class='received teal darken-3 z-depth-2'>";
                echo $line['text'];
                echo "</div>";
              }
              echo "<br>";
            }
          } 
          mysqli_free_result($result);
        ?>
      </div>
      <div class="text-window row">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
          <div class="row">
            <input type="hidden" name="target" id="target" value="<?php echo htmlspecialchars($target); ?>">
            <input type="text" name="text" id="text" maxlength="240" class="textbox blue-grey darken-3" required/>  
            <button class="formbtn" type="submit"><i class="material-icons">send</i></button>       
         </div>
        </form>
      </div>
    </div>
    <script>
      $(document).ready(function(){
      $('.message-window').scrollTop($('.message-window')[0].scrollHeight);
      });
    </script>
  </body>
</html>