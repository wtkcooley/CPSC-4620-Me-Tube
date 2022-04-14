<?php
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
//   if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     if($_POST['status']=='accept'){
//       $query = "UPDATE Relation SET status='{$constant(ACCEPT_VAL)}', dateModified=curdate() WHERE uname1='{$_POST['user1']}' AND uname2='{$_POST['user2']}'";
//       mysqli_query($mysqli, $query);
//     } else if($_POST['status']=='deny'){
//       $query = "UPDATE Relation SET status='{$constant(DENY_VAL)}', dateModified=curdate() WHERE uname1='{$_POST['user1']}' AND uname2='{$_POST['user2']}'";
//       mysqli_query($mysqli, $query);
//     } else if($_POST['status']=='remove'){
//       $query = "DELETE FROM Relation WHERE uname1='{$_POST['user1']}' AND uname2='{$_POST['user2']}'";
//       mysqli_query($mysqli, $query);
//     }
//   }

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
    <body style="text-align: center">
        <h5>Incoming Requests...</h5><br>
        <div style="margin: auto; right: 50%;">
        <?php
          if(empty($requests)){
            echo "<p1>No friend requests yet.</p1><br>";
          } else {
            echo "<table width='100%'>";
            foreach($requests as $user => $request){
              echo "<tr>";
              echo "<td style='text-align: left' width='75%'>".$user."</td>";
              echo "<td style='text-align: right;' width='12.5%'>";
              echo "<form style='margin: aut0; right: 50%;' action='<?php echo htmlspecialchars(\$_SERVER['PHP_SELF']);?>' method='post'>";
              echo "<input type='hidden' name='user1' value=".$request['uname1'].">";
              echo "<input type='hidden' name='user2' value=".$request['uname2'].">";
              echo "<input type='hidden' name='status' value='accept'>";
              echo "<input style='width: 100%' id='abtn' type='submit' name='submit' value='Accept'>";
              echo "</form>";
              echo "</td>";
              echo "<td style='text-align: right;' width='12.5%'>";
              echo "<form style='margin: aut0; right: 50%;' action='<?php echo htmlspecialchars(\$_SERVER['PHP_SELF']);?>' method='post'>";
              echo "<input type='hidden' name='user1' value=".$request['uname1'].">";
              echo "<input type='hidden' name='user2' value=".$request['uname2'].">";
              echo "<input type='hidden' name='status' value='deny'>";
              echo "<input style='width: 100%' id='dbtn' type='submit' name='submit' value='Deny'>";
              echo "</form>";
              echo "</td>";
              echo "</tr>";
            }
            echo "</table>";
          }
        ?>
        </div>
        <h5>Outgoing Requests...</h5><br>
        <div style="margin: auto; right: 50%;">
        <?php
          if(empty($sentrequests)){
            echo "<p1>No pending friend requests.</p1><br>";
          } else {
            echo "<table width='100%'>";
            foreach($sentrequests as $user => $request){
              echo "<tr>";
              echo "<td style='text-align: left' width='75%'>".$user."</td>";
              echo "<td style='text-align: right;' width='12.5%'></td>";
              echo "<td style='text-align: right;' width='12.5%'>";
              echo "<form style='margin: aut0; right: 50%;' action='<?php echo htmlspecialchars(\$_SERVER['PHP_SELF']);?> method='post'>";
              echo "<input type='hidden' name='user1' value=".$request['uname1'].">";
              echo "<input type='hidden' name='user2' value=".$request['uname2'].">";
              echo "<input type='hidden' name='status' value='remove'>";
              echo "<input style='width: 100%' id='cbtn' type='submit' name='submit' value='Cancel'>";
              echo "</form>";
              echo "</td>";
              echo "</tr>";
            }
            echo "</table>";
          }
        ?>
        </div>
        <h5>Friends...</h5><br>
        <div style="margin: auto; right: 50%;">
        <?php
          if(empty($friends)){
            echo "<p1>No friends yet.</p1><br>";
          } else {
            echo "<table width='100%'>";
            foreach($friends as $user => $relation){
              echo "<tr>";
              echo "<td style='text-align: left' width='75%'>".$user."</td>";
              echo "<td style='text-align: right;' width='12.5%'></td>";
              echo "<td style='text-align: right;' width='12.5%'>";
              echo "<form style='margin: aut0; right: 50%;' action='<?php echo htmlspecialchars(\$_SERVER['PHP_SELF']);?>' method='post'>";
              echo "<input type='hidden' name='user1' value=".$relation['uname1'].">";
              echo "<input type='hidden' name='user2' value=".$relation['uname2'].">";
              echo "<input type='hidden' name='status' value='remove'>";
              echo "<input style='width: 100%' id='cbtn' type='submit' name='submit' value='Remove'>";
              echo "</form>";
              echo "</td>";
              echo "</tr>";
            }
            echo "</table>";
          }
        ?>
        </div>
    </body>
    </html>