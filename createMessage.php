<?php
  //had issues with the cached file automatically redirecting to the login page
  //hence include every header that will make sure the browser does not use cache to get the page
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Expires: Mon, 21 Aug 2000 12:00:00 GMT");
  header("Pragma: no-cache");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

  if (isset($_REQUEST['target'])){
    $target = trim($_REQUEST['target']);
  } else {
      $target = '';
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

  $errors = [];

  //if send was pressed, add the message to the db then re-query all messages
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = "SELECT username FROM User WHERE username='$target'";
    $results = mysqli_query($mysqli, $query);
    if(mysqli_num_rows($results) > 0) {
        $query = "INSERT INTO Message (sender, receiver, text, dateSent) VALUES ('{$_COOKIE['user']}', '$target', '{$_POST['text']}', NOW())";
        mysqli_query($mysqli, $query);
        header("Location: /~wcooley/metube/messageView.php?target=$target", true, 301);
        exit();
    } else {
        $errors['target'] = "There is no user with that username...";
    }
  }
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
        div.main {
          height: 100vh;
          width: 100%;
          display: flex;
          flex-direction: column;
          overflow: hidden;
          vertical-align: bottom;
        }
        div.header {
          max-height: 20%;
          width: 100%;
          text-align: center;
          color: white;
        }
        div.text-window {
          width: 100%;
          height: 80%;
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
  <body class="blue-grey darken-3">
    <div class="main">
    <div class="header row">
        <?php echo "<h3>Create Message</h3>"; ?>
    </div>
    <div class="text-window row">
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        <div class="row">
          <input type="text" name="target" id="target" class="textbox teal darken-3" required value="<?php echo htmlspecialchars($target); ?>">
          <?php
            if (array_key_exists('target', $errors)){
              echo "<span class='helper-text' style='color: red; text-align: left; clear: right;'>".$errors['target']."</span>"; 
            } else {
              echo "<span class='helper-text' style='text-align: left'>Recipient...</span>"; 
            }
          ?>
        </div>
        <div class="row">
          <input type="text" name="text" id="text" maxlength="240" class="textbox teal darken-3" required>
          <span class="helper-text" style="text-align: left; clear: right;">Message...</span>
          <button class="formbtn" type="submit"><i class="material-icons">send</i></button>       
       </div>
      </form>
    </div>
    </div>
  </body>
</html>