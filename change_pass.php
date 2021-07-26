<?php
    require_once "pdo.php";
    session_start();

    if(isset($_POST['cancel'])){
        header('Location: userinfo.php?user_id='.$_SESSION['user_id']);
        return;
    }

    $salt="XyZzy12*_"; //for password 

    if(isset($_POST['oldpass']) && isset($_POST['pass']) && isset($_POST['cnfpass']) && isset($_POST['uid']) ){
        if(strlen($_POST['oldpass'])<1 || strlen($_POST['pass'])<1 || strlen($_POST['cnfpass'])<1 ){
            $_SESSION['error']="All fields are required";
            header('Location: change_pass.php?user_id='.$_REQUEST['user_id']);
            return;
        }
        $stmt = $pdo->prepare("SELECT * FROM users where user_id = :uid");
        $stmt->execute(array(":uid" => $_SESSION['user_id']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(hash('md5',$salt.$_POST['oldpass'])!==$row['password']){
            $_SESSION['error']="Please enter the correct password";
            header('Location: change_pass.php?user_id='.$_REQUEST['user_id']);
            return;
        }
        $pass = $_POST['pass'];
        $cnf = $_POST['cnfpass'];
        if($pass!=$cnf){
            $_SESSION['error'] = "Password fields do not match. Please retype your password";
            header('Location: change_pass.php?user_id='.$_REQUEST['user_id']);
            return;
        }
        $password = hash('md5',$salt.$pass);

        //updating the database with new password
        $sql = "UPDATE users SET password=:pass WHERE user_id=:ud";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':pass' => $password,
            ':ud'   => $_POST['uid']
        ));

        //success message
        $_SESSION['success'] = "Password changed successfully";
        header('Location: login.php');
        return;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <?php require_once "bootstrap.php";?>
</head>
<body>
    <div class="container">
        <h1>Change Password</h1>
        <?php
    
             if ( isset($_SESSION['error']) ) {
            echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
            unset($_SESSION['error']);
            }
    
        ?>
        <form method="post">
            </p><label for="01">Old Password: </label>
            <input type="password" name="oldpass" id="01"></p>
            </p><label for="02">New Password: </label>
            <input type="password" name="pass" id="02"></p>
            </p><label for="03">Confirm Password: </label>
            <input type="password" name="cnfpass" id="03"></p>
            <p><input type="hidden" name="uid" value="<?=$_SESSION['user_id'] ?>">
            <input type="submit" value="Change password" name="chgpass">
            <input type="submit" value="Cancel" name="cancel"></p>
        </form>
    </div>
</body>
</html>