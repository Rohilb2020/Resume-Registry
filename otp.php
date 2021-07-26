<?php
    require_once "pdo.php";
    session_start();
    // $_SESSION['count'] = 0;

    if(isset($_POST['cancel'])){
        header('Location: forgot.php');
        return;
    }
    // echo $_SESSION['otp'];

    if(isset($_POST['otp1']) ){
        if(strlen($_POST['otp1'])<1){
            $_SESSION['error'] = "Please enter the OTP";
            header('Location: otp.php');
            return;
        }
        $OTP = $_POST['otp1'];
        if($OTP == $_SESSION['otp']){
            $_SESSION['success'] = "OTP verified";
            header('Location: reset.php?user_id='.$_SESSION['uid']);
            return;
        }
        else{
            // $_SESSION['count']++;
            // if($_SESSION['count']>3){
            //     $_SESSION['error'] = "OTP has expired"
            //     header('Location: forgot.php');
            //     return; 
            // }
            $_SESSION['error'] = "The OTP is incorrect";
            header('Location: otp.php');
            return;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP</title>
    <?php "bootstrap.php "?>
</head>
<body>
    <div class="container">
    <h1>OTP verification</h1>
    <h4 style="color:blue;"><i>Please check your email for OTP</i></h4>

    <?php
            if ( isset($_SESSION['error']) ) {
                echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
                unset($_SESSION['error']);
            }
    ?>
        <form method="post">
            <p><label for="OTP">OTP: </label>
            <input type="text" name="otp1" id="OTP"></p>
            <p><input type="submit" name="submit" value="Verify">
            <input type="submit" value="Cancel" name="cancel"></p>
        </form>
    </div>
    
</body>
</html>