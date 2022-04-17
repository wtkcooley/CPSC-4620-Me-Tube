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

    $userID = $_COOKIE['user'];
    
    $query = `SELECT fname, lname, email FROM User WHERE username=$userID`;
    $result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
    $array = sql_fetch_row($result);
    $name = $array['fname'] . $array[$lname];
    $email = $array['email'];
?>
<div class="profile-home row">
    <div class="row">
        <div class="col s12">
            <div class="row">
                <h5 class="col s4">Name: 
                    <?php echo $name; ?>
                </h5>
                <h5 class="col s4">Username: <?php echo $userID; ?></h5>
                <h5 class="col s4">Email: <?php echo $email; ?></h5>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col s12">
            <h4>My Media:</h4>
            <div class="row">
            </div>
        </div>
    </div>
    
</div>