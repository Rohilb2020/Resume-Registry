<?php
    require_once "pdo.php";
    session_start();

    if ( ! isset($_GET['user_id']) ) {
        $_SESSION['error'] = "Missing user_id";
        header('Location: index.php');
        return;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <?php require_once "bootstrap.php";?>
</head>
<body>
    <div class="container">
        <h1>User Info</h1>

        <?php
            if(isset($_SESSION['success'])){
                echo'<p style="color:green;">'.$_SESSION['success'].'<p>';
                unset($_SESSION['success']);
            }
        ?>
        <?php
            $sql = "SELECT * FROM users WHERE user_id=:uid";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':uid' => $_SESSION['user_id']
            ));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            //if the row with user_id does not exist
            if ( $row === FALSE ) {
                $_SESSION['error'] = 'Bad value for user_id';
                header( 'Location: index.php' );
                return;
            }

            echo'<p>First Name: '.htmlentities($row['name']).'</p>';
            echo'<p>Last Name: '.htmlentities($row['last_name']).'</p>';
            echo'<p>Email: '.htmlentities($row['email']).'</p>';
        ?>
        <br>
        <a href="index.php">Done</a>
        <br>
        <a href="user_del.php?user_id=<?=$row['user_id'];?>">Delete user profile</a>
        <br>
        <a href="user_edit.php?user_id=<?=$row['user_id'];?>">Edit user profile</a>
        <br>
        <a href="change_pass.php?user_id=<?=$row['user_id'];?>">Change password</a>
    </div>
    
</body>
</html>