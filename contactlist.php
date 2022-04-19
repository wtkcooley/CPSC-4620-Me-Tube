<?php

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

  //define the values of staus db table
  define("REQ_VAL", "1");
  define("ACCEPT_VAL", "2");
  define("DENY_VAL", "3");
  $constant = 'constant';

  //declare our arrays to hold the different relations
  $sentrequests = [];
  $requests = [];
  $friends = [];

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

  //if the form posted back to itself, do the request
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if($_POST['status']=='accept'){
      $query = "UPDATE Relation SET status='{$constant('ACCEPT_VAL')}', dateModified=NOW() WHERE uname1='{$_POST['user1']}' AND uname2='{$_POST['user2']}'";
      mysqli_query($mysqli, $query);
    } else if($_POST['status']=='deny'){
      $query = "UPDATE Relation SET status='{$constant('DENY_VAL')}', dateModified=NOW() WHERE uname1='{$_POST['user1']}' AND uname2='{$_POST['user2']}'";
      mysqli_query($mysqli, $query);
    } else if($_POST['status']=='remove'){
      $query = "DELETE FROM Relation WHERE uname1='{$_POST['user1']}' AND uname2='{$_POST['user2']}'";
      mysqli_query($mysqli, $query);
    }
  }

  if(isset($_COOKIE['user'])){
    //find all the updated relations that the current user has
    $query = "SELECT uname1, uname2, status FROM  Relation WHERE uname1 = '{$_COOKIE['user']}' UNION SELECT uname1, uname2, status FROM Relation WHERE uname2 = '{$_COOKIE['user']}'";
    $result = mysqli_query($mysqli, $query);
    //parse through each row
    while($line = mysqli_fetch_array($result)){
      //check to see if the current user is in the first or second user
      //first user
      if($line['uname1'] == $_COOKIE['user']){
        //check the status and store in correct array
        if ($line['status'] == ACCEPT_VAL){
          $friends[$line['uname2']] = array("uname1" => $line['uname1'], "uname2" => $line['uname2']);
        } else if ($line['status'] == REQ_VAL){
          $sentrequests[$line['uname2']] = array("uname1" => $line['uname1'], "uname2" => $line['uname2']);
        }
      }
      //second user
      if($line['uname2'] == $_COOKIE['user']){
        //check the status and store in correct array
        if ($line['status'] == ACCEPT_VAL){
          $friends[$line['uname1']] = array("uname1" => $line['uname1'], "uname2" => $line['uname2']);
        } else if ($line['status'] == REQ_VAL){
          $requests[$line['uname1']] = array("uname1" => $line['uname1'], "uname2" => $line['uname2']);
        }
      }
    }
    mysqli_free_result($result);
  }
  mysqli_close($mysqli);
?>

    <!DOCTYPE html>
    <html>

    <head>
        <title>MeTube Contact List</title>
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
        
        :root {
          --main-background-color: #37474f;
        }
        body {
          background-color: var(--main-background-color);
          text-align: center;
          vertical-align: center;
        }
        div {
          background-color: white; 
          width: 75%;
          margin: auto;
          left: 50%;
          border-radius: 10px;
        }
        div.main {
          padding: 10px;
          margin-top: 20px;
        }
        div.titles {
          color: white;
          background-color: var(--main-background-color);
        }
        table {
          color: white;
          background-color: #00796b;
          border-radius: 5px;
          width: 90%;
          margin: auto;
          left: 50%;
        }
        form.main {
          color: var(--main-background-color);
        }
        .formbtn {
          background-color: var(--main-background-color); 
          border: none; 
          color: white; 
          text-decoration: none; 
          cursor: pointer; 
          border-radius: 5px;
          padding: 5px;
        }
        td.name {
          width: 80%;
          padding-left: 5%;
        }
        td.requests {
          width: 10%;
          padding: 5px;
        }
      </style>
    </head>
    <body>
      <div class='main'>
        <div class="row z-depth-2 titles">
        <h5>Incoming Requests...</h5>
        </div>
        <div class="row">
        <?php
          if(empty($requests)){
            echo "<p1>No friend requests yet.</p1><br>";
          } else {
            echo "<table>";
            foreach($requests as $user => $request){
              echo "<tr>";
              echo "<td class='name'>".$user."</td>";
              echo "<td class='requests'>";
              echo "<form class='main' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>";
              echo "<input type='hidden' name='user1' value=".$request['uname1'].">";
              echo "<input type='hidden' name='user2' value=".$request['uname2'].">";
              echo "<input type='hidden' name='status' value='accept'>";
              echo "<input class='formbtn' id='abtn' type='submit' name='submit' value='Accept'>";
              echo "</form>";
              echo "</td>";
              echo "<td class='requests'>";
              echo "<form class='main' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>";
              echo "<input type='hidden' name='user1' value=".$request['uname1'].">";
              echo "<input type='hidden' name='user2' value=".$request['uname2'].">";
              echo "<input type='hidden' name='status' value='deny'>";
              echo "<input class='formbtn' id='dbtn' type='submit' name='submit' value='Deny'>";
              echo "</form>";
              echo "</td>";
              echo "</tr>";
            }
            echo "</table>";
          }
        ?>
        </div>
        <div class="row z-depth-2 titles">
        <h5>Outgoing Requests...</h5>
        </div>
        <div class="row">
        <?php
          if(empty($sentrequests)){
            echo "<p1>No pending friend requests.</p1><br>";
          } else {
            echo "<table width='100%'>";
            foreach($sentrequests as $user => $request){
              echo "<tr>";
              echo "<td class='name'>".$user."</td>";
              echo "<td class='requests'></td>";
              echo "<td class='requests'>";
              echo "<form class='main' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>";
              echo "<input type='hidden' name='user1' value=".$request['uname1'].">";
              echo "<input type='hidden' name='user2' value=".$request['uname2'].">";
              echo "<input type='hidden' name='status' value='remove'>";
              echo "<input class='formbtn' id='cbtn' type='submit' name='submit' value='Cancel'>";
              echo "</form>";
              echo "</td>";
              echo "</tr>";
            }
            echo "</table>";
          }
        ?>
        </div>
        <div class="row z-depth-2 titles">
        <h5>Friends...</h5>
        </div>
        <div class="row">
        <?php
          if(empty($friends)){
            echo "<p1>No friends yet.</p1><br>";
          } else {
            echo "<table width='100%'>";
            foreach($friends as $user => $relation){
              echo "<tr>";
              echo "<td class='name'>".$user."</td>";
              echo "<td class='requests'></td>";
              echo "<td class='requests'>";
              echo "<form class='main' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>";
              echo "<input type='hidden' name='user1' value=".$relation['uname1'].">";
              echo "<input type='hidden' name='user2' value=".$relation['uname2'].">";
              echo "<input type='hidden' name='status' value='remove'>";
              echo "<input class='formbtn' id='cbtn' type='submit' name='submit' value='Remove'>";
              echo "</form>";
              echo "</td>";
              echo "</tr>";
            }
            echo "</table>";
          }
        ?>
        </div>
      </div> 
    </body>
    </html>