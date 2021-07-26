<?php
    require_once "pdo.php";
    session_start();

    if(isset($_POST['cancel'])){
        header('Location: index.php');
        return;
    }

    if(isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['uname']) && isset($_POST['uid']) ){
        if(strlen($_POST['fname'])<1 || strlen($_POST['lname'])<1 || strlen($_POST['uname'])<1 ){
            $_SESSION['error']="All fields are required";
            header('Location: user_edit.php?user_id='.$_REQUEST['user_id']);
            return;
        }
        if(strpos($_POST['uname'],"@")===false){
            $_SESSION['error']= "Email address must contain @";
            header('Location: user_edit.php?user_id='.$_REQUEST['user_id']);
            return;
        }

        //updating the databse with new values
        $sql = "UPDATE users SET name=:fname, last_name=:lname, email=:em WHERE user_id=:ud";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':fname' => $_POST['fname'],
            ':lname' => $_POST['lname'],
            ':em'    => $_POST['uname'],
            ':ud'   => $_POST['uid']
        ));

        $_SESSION['success'] = "User Profile edited";
        header('Location: userinfo.php?user_id='.$_SESSION['user_id']);
        return;

    }

    if ( ! isset($_GET['user_id']) ) {
        $_SESSION['error'] = "Missing user_id";
        header('Location: userinfo.php?user_id='.$_SESSION['user_id']);
        return;
    }
      
    $stmt = $pdo->prepare("SELECT * FROM users where user_id = :uid");
    $stmt->execute(array(":uid" => $_SESSION['user_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( $row === false ) {
        $_SESSION['error'] = 'Bad value for user_id';
        header('Location: userinfo.php?user_id='.$_SESSION['user_id']);
        return;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit user profile</title>
    <?php "bootstrap.php"; ?>
</head>
<body>
    <div class="container">
        <h1>Editing user profile for <?= htmlentities($_SESSION['name'])?> </h1>
        <?php
    
             if ( isset($_SESSION['error']) ) {
            echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
            unset($_SESSION['error']);
            }
    
        ?>
        <form method="post">
            <p><label for="01">First name: </label>
            <input type="text" name="fname" id="01" value="<?=$row['name']?>"></p>
            <p><label for="02">Last name: </label>
            <input type="text" name="lname" id="02" value="<?=$row['last_name'] ?>"></p>
            <p><label for="03">Email: </label>
            <input type="text" name="uname" id="03" value="<?=$row['email'] ?>"></p>
            <p><input type="hidden" name="uid" value="<?=$row['user_id'] ?>">
            <input type="submit" value="Save" name="save">
            <input type="submit" value="Cancel" name="cancel"></p>
        </form>
    
    </div>
    
</body>
</html>