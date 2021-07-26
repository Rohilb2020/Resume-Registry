<?php
    require_once "pdo.php";
    session_start();

    if(isset($_POST['cancel'])){
        header('Location: index.php');
        return;
    }

    if(isset($_POST['delete']) && isset($_POST['profile_id'])){
        $sql="DELETE FROM profile WHERE profile_id=:pid";
        $stmt=$pdo->prepare($sql);
        $stmt->execute(array(':pid'=>$_REQUEST['profile_id']));
        $_SESSION['success'] = 'Profile deleted';
        header( 'Location: index.php' ) ;
        return;           
    }

    if ( ! isset($_GET['profile_id']) ) {
        $_SESSION['error'] = "Missing profile_id";
        header('Location: index.php');
        return;
    }
      
    $stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :pid");
    $stmt->execute(array(":pid" => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( $row === false ) {
        $_SESSION['error'] = 'Bad value for profile_id';
        header( 'Location: index.php' ) ;
        return;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dc70227a</title>
</head>
<body>
    <?php require_once "bootstrap.php"?>
    <div class="container">
        <h1>Deleting Profile</h1>
        <p>First Name:<?=htmlentities($row['first_name'])?></p>
        <p>Last Name:<?=htmlentities($row['last_name'])?></p>
        <form method="post">

            <input type="hidden" name="profile_id" value="<?=htmlentities($row['profile_id']) ?>">
            <input type="submit" value="Delete" name="delete">
            <input type="submit" value="Cancel" name="cancel">
            
        </form>
    </div>
    
</body>
</html>