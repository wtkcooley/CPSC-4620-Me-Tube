<?php
  //keep track of our errors
  $form_errors = [];
  //check every input
  $inputs = ['username', 'password'];
  $values = [];
  foreach ($inputs as $input) {
    $values[$input] = '';
  }
  //get the values of the inputs
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //checks for missing values in the form
    foreach ($inputs as $input) {
      $values[$input] = $_POST[$input];
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

  $query = "SELECT * FROM User WHERE username = '{$values["username"]}'";
  $sqlsearch = mysqli_query($mysqli, $query);
  $resultcount = mysqli_num_rows($sqlsearch);
  //if username doesn't exist
  if ($resultcount < 1) {
    $form_errors['username'] = 'Unknown Username!';
    //if user does exist
  } else {
    $query = "SELECT * FROM User WHERE username = '{$values["username"]}' AND password = '{$values["password"]}'";
    $sqlsearch = mysqli_query($mysqli, $query);
    $resultcount = mysqli_num_rows($sqlsearch);
    //if the user and pass word don't match
    if ($resultcount < 1){
      $form_errors['password'] = 'Invalid Password!';
    }
  }

  if (empty($form_errors)) {
    setcookie("user", $values['username'], time() + 86400, "/~wcooley/metube/"); //set a cookie that holds the current user; 86400 = 1 day
    header("Location: /~wcooley/metube/profile-home.php", true, 301); //redirect to the profile home
    exit;
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>MeTube Login</title>
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
      background-image: url('/~wcooley/metube/images/main_bg.jpg');
      background-repeat: no-repeat;
    }
    div.centerform {
      margin: 0; 
      position: absolute; 
      top: 50%; 
      left: 50%; 
      -ms-transform: translate(-50%, -50%); 
      transform: translate(-50%, -50%); 
      text-align: center;
    }
    form.main {
      color: #37474f;
      background-color: white; 
      padding: 50px; 
      border-radius: 15px 50px; 
    }
    .formbtn {
      background-color: #37474f; 
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
<body>
  <div class="centerform">
    <form class="z-depth-5 main col s12" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
      <div class="row">
          <h4>Login</h4>
      </div>
      <div class="row">
        <div class="input-field col s12">
          <i class="material-icons prefix">account_circle</i>
            <input name ="username" id="username" type="text" class="validate" value="<?php echo htmlspecialchars($values['username']);?>" required>
            <label for="username">username</label>
            <?php
              if (array_key_exists('username', $form_errors)){
                echo "<span class='helper-text' style='color: red; text-align: left'>".$form_errors['username']."</span>"; 
              }
            ?>
          </div>
        </div>
        <div class="row">
          <div class="input-field col s12">
            <i class="material-icons prefix">lock</i>
            <input name="password" id="password" type="password" class="validate" value="<?php echo htmlspecialchars($values['password']);?>" required>
            <label for="password">Password</label>
            <?php
              if (array_key_exists('password', $form_errors)){
                echo "<span class='helper-text' style='color: red; text-align: left'>".$form_errors['password']."</span>"; 
              }
            ?>
          </div>
        </div>
        <div class="row">
          <input class="z-depth-5 formbtn"  type="submit" value="Login" />
          <a class="z-depth-5 formbtn" href="/~wcooley/metube/createaccnt.php">Create Account</a>
        </div>
        <div class="row">
          <a href="/~wcooley/metube/browse.php">
            <p6>Return home...</p6>
          </a>
        </div>
      </form>
    </div>
</body>
</html>