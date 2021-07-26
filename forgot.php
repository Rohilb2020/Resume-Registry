<?php

    session_start();
    if(isset($_POST['cancel'])){
        header('Location: login.php');
        return;
    }

    require_once "pdo.php";

    if(isset($_POST['email']) && isset($_POST['send'])){
        if(strlen($_POST['email'])<1){
            $_SESSION['error'] = "Email Id required";
            header('Location: forgot.php');
            return;
        }
        if(strpos($_POST['email'],"@")===false){
            $_SESSION['error']= "Email address must contain @";
            header('Location: forgot.php');
            return;
        }
        $sql = "SELECT * FROM users where email=:email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':email'=>$_POST['email']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row===FALSE){
            $_SESSION['error'] = "Invalid Email";
            header('Location: forgot.php');
            return;
        }
        $uid=$row['user_id'];
        $email=$_POST['email'];

        //OTP generation
        $otp = rand(100000,999999);
        $_SESSION['otp'] = $otp;
        
        // //emailing the password using the php mail() function
        $headers = "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $msg = '<html><body>';
        // $msg.= 'Click this link to reset the password ';
        // $msg.= '<a href="http://localhost/webdev/profile_pos/reset.php?user_id='.$uid.'">reset password</a>';
        $msg.= '<p>Your OTP is: <b>'.$_SESSION['otp'].'</b></p>';
        $msg.= '</body></html>'; 
        if(mail("$email","password recovery",$msg,$headers)){
            $_SESSION['uid'] = $uid;
            // $_SESSION['success'] = "password mailed successfully ";
            header('Location: otp.php');
            return;
        }
        else{
            $_SESSION['error'] = "Failed to send the mail";
            header('Location: forgot.php');
            return;
        }
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once "bootstrap.php"; ?>
    <title>Forgot password</title>
</head>
<body>
<div class="container">
    <h3>Forgot Password</h3>
    <br>
    <h5 style="color:blue;"><i>Please enter your Email Id</i></h5>

    <?php
            if ( isset($_SESSION['error']) ) {
                echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
                unset($_SESSION['error']);
            }
    ?>
    <form method="post">
        <p><label for="mail">Email: </label>
        <input type="text" name="email" id="mail" size=40 placeholder="Type your email"></p>
        <p><input type="submit" name="send" value="Send">
        <input type="submit" name="cancel" value="Cancel"></p>
    </form>

</div>
    
</body>
</html>