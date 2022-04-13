<?php
  //keep track of our errors
  $form_errors = [];
  //check every input
  $inputs = ['fname', 'lname', 'email', 'username', 'password'];
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
  if ($resultcount > 0) {
    $form_errors['username'] = 'Username Taken!';
  }

  if (empty($form_errors)) {
    $query = "INSERT INTO User (username, password, email, fname, lname) VALUES 
      ('{$values["username"]}', '{$values["password"]}', '{$values["email"]}','{$values["fname"]}', '{$values["lname"]}')";
    mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
    exit;
  }
}
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

    <body style="background-image: url('/metube/main_bg.jpg');background-repeat: no-repeat">
        <div style="margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); text-align: center;">
            <form class="z-depth-5" style="color:#37474f;background-color: white; padding: 50px; border-radius: 15px 50px;" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" class="col s12">
                <div class="row">
                    <h4 style="color: #37474f">Create Account</h4>
                </div>
                <div class="row">
                    <div class="input-field col s6">
                        <i class="material-icons prefix">person</i>
                        <input name="fname" id="fname" type="text" class="validate" value="<?php echo htmlspecialchars($values['fname']);?>" required>
                        <label for="fname">First Name</label>
                    </div>
                    <div class="input-field col s6">
                        <i class="material-icons prefix">person</i>
                        <input name="lname" id="lname" type="text" class="validate" value="<?php echo htmlspecialchars($values['lname']);?>" required>
                        <label for="lname">Last Name</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">email</i>
                        <input name="email" id="email" type="email" class="validate" value="<?php echo htmlspecialchars($values['email']);?>" required validate>
                        <label for="email">Email</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">account_circle</i>
                        <input name="username" id="username" type="text" class="validate" value="<?php echo htmlspecialchars($values['username']);?>" required>
                        <label for="username">Username</label>
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
                        <input name="password" id="password" type="password" class="validate" required>
                        <label for="password">Password</label>
                    </div>
                </div>
                <div class="row">
                    <input class="z-depth-5" style="background-color: #37474f; border: none; color: white; padding: 16px 32px; text-decoration: none; margin: 4px 2px; cursor: pointer; border-radius: 3px;" type="submit" value="Create Account" />
                    <!--<a type="submit" class="modal-close waves-effect waves-light btn col s12">Create Account</a>-->
                </div>
            </form>
        </div>
    </body>

    </html>