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

    $channelID = -1;
    if (isset($_GET['channelID'])) {
        $mediaID = $_GET['channelID'];
    } else {
        die("Could not get channelID! Is it valid?");
    }
    $userID = $_COOKIE['user'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $query = "INSERT INTO Subscription (subscriber, subscribee) VALUES 
        ('{$channelID}', '{$userID}')";
        mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
    }

    $query = "SELECT * FROM Subscription WHERE subscrbee=$channelID AND subscriber=$userID";
    $result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    $subed = FALSE;
    if($result->num_rows >= 1)
        $subed = TRUE;

?>
<div class="profile-home row">
    <div class="row">
        <div class="col s12">
            <div class="row">
                <h5 class="col s6">
                    <?php
                        echo $channelID;
                    ?>
                </h5>
                <form class="col s6" method="POST">
                    <p>
                      <label>
                        <?php
                            if ($subed) {
                                echo "<input type='checkbox' checked='checked' onchange='this.form.submit()'/>
                                <span>Subscribed</span>";
                            } else {
                                echo "<input type='checkbox' onchange='this.form.submit()'/>
                                <span>Subscribed</span>";
                            }
                        ?>
                      </label>
                    </p>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col s12">
            <div class="row">
                
            </div>
        </div>
    </div>
    
</div>