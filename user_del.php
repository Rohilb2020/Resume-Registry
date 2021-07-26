<?php
    require_once "pdo.php";
    session_start();

    if(isset($_POST['cancel'])){
        header('Location: userinfo.php?user_id='.$_SESSION['user_id']);
        return;
    }

    if(isset($_POST['delete']) && isset($_POST['user_id'])){
        $sql="DELETE FROM users WHERE user_id=:uid";
        $stmt=$pdo->prepare($sql);
        $stmt->execute(array(':uid'=>$_SESSION['user_id']));
        // $_SESSION['success'] = 'User deleted';
        header( 'Location: logout.php' ) ;
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
    <title>Delete User</title>
</head>
<body>
    <?php require_once "bootstrap.php"?>
    <div class="container">
        <h1>Deleting User</h1>
        <p>First Name:<?=htmlentities($row['name'])?></p>
        <p>Last Name:<?=htmlentities($row['last_name'])?></p>
        <form method="post">

            <input type="hidden" name="user_id" value="<?=htmlentities($row['user_id']) ?>">
            <input type="submit" value="Delete" name="delete">
            <input type="submit" value="Cancel" name="cancel">
            
        </form>
    </div>
    
</body>
</html>