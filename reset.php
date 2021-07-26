<?php
    session_start();
    if(!isset($_SESSION['uid'])){
        die("ACCESS DENIED");
    }
    if(isset($_POST['cancel'])){
        header('Location: login.php');
        return;
    }

    require_once "pdo.php";
    if(isset($_POST['pass']) && isset($_POST['cnfpass']) && isset($_POST['reset']) ){
        if(strlen($_POST['pass'])<1 || strlen($_POST['cnfpass'])<1 ){
            $_SESSION['error'] = "All the fields are required";
            header('Location: reset.php?user_id='.$_REQUEST['user_id']);
            return;
        }
        $pass = $_POST['pass'];
        $cnf  = $_POST['cnfpass'];
        if($pass!=$cnf){
            $_SESSION['error'] = "Password fields do not match.Please retype your password";
            header('Location: reset.php?user_id='.$_REQUEST['user_id']);
            return;
        }

        $salt="XyZzy12*_"; //for password 
        //encryption of the password
        $password = hash('md5',$salt.$pass);

        //updating the database with the new password
        $sql = "UPDATE users SET password=:pass WHERE user_id=:uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':pass'     => $password,
            ':uid'  => $_REQUEST['user_id']
        ));
        $_SESSION['success'] = "password successfully reset";
        header('Location: login.php');
        return;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset password</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <?php require_once "bootstrap.php" ?>
</head>
<body>
    <div class="container">
    <h3>Reset Password</h3>
    <br>
    <h5 style="color:blue;"><i>press cancel to go back to login screen</i></h5>
    <br>
    <!-- flash messages -->
    <?php
            if ( isset($_SESSION['error']) ) {
                echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
                unset($_SESSION['error']);
            }
            if ( isset($_SESSION['success']) ) {
                echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
                unset($_SESSION['success']);
            }
    ?>
        <form method="post">
            <p><label for="01">Password: </label>
            <input type="password" name="pass" id="01" size=40 placeholder="password"><button type="button" onclick="viewPassword();"><i class="fas fa-eye" id="pass-status"></i></button></p>
            <p><label for="02">Retype password: </label>
            <input type="password" name="cnfpass" id="02" size=40 placeholder="retype password"><button type="button" onclick="cnfviewPassword();"><i class="fas fa-eye" id="cnf_pass-status"></i></button></p>
            <p><input type="submit" name="reset" value="Reset">
            <input type="submit" name="cancel" value="Cancel"></p>
        </form>
    </div>

    <script>
        function viewPassword(){
        var passwordInput = document.getElementById('01');
        var passStatus = document.getElementById('pass-status');
        
        if (passwordInput.type == 'password'){
            passwordInput.type='text';
            passStatus.className='fas fa-eye-slash';
        }
        else{
            passwordInput.type='password';
            passStatus.className='fas fa-eye';
        }
    }
    function cnfviewPassword(){
        var cnf_passwordInput = document.getElementById('02');
        var cnf_passStatus = document.getElementById('cnf_pass-status');

        if (cnf_passwordInput.type == 'password'){
            cnf_passwordInput.type='text';
            cnf_passStatus.className='fas fa-eye-slash';
        }
        else{
            cnf_passwordInput.type='password';
            cnf_passStatus.className='fas fa-eye';
        }
    }
    </script>

</body>
</html>