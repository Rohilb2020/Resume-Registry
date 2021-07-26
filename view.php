<?php
    require_once "pdo.php";
    session_start();

    if ( ! isset($_GET['profile_id']) ) {
        $_SESSION['error'] = "Missing profile_id";
        header('Location: index.php');
        return;
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dc70227a</title>
    <?php require_once "bootstrap.php"; ?>
</head>
<body>
    <div class="container">
        <h1>Profile Information</h1>
        
        <?php
            $stmt=$pdo->prepare("SELECT * FROM profile WHERE profile_id=:pid");
            $stmt->execute(array(":pid"=>$_GET['profile_id']));
            $row=$stmt->fetch(PDO::FETCH_ASSOC);

            //if the row with profile_id does not exist
            if ( $row === FALSE ) {
                $_SESSION['error'] = 'Bad value for profile_id';
                header( 'Location: index.php' ) ;
                return;
            }
        

        echo'<p>First Name: '.htmlentities($row['first_name']).'</p>';
        echo'<p>Last Name: '.htmlentities($row['last_name']).'</p>';
        echo'<p>Email: '.htmlentities($row['email']).'</p>';
        echo'<p>Headline: <br>'.htmlentities($row['headline']).'</p>';
        echo'<p>Summary: <br>'.htmlentities($row['summary']).'</p>';

        //adding the education to the view
        echo '<p>Education: </p>';
        echo '<ul>';
        $stmt=$pdo->prepare("SELECT * FROM education join institution on education.institution_id=institution.institution_id   
                                where profile_id=:pid order by rank ");  //inner join operation is used to exploit the connection between the two tables and retrieve the necessary values
        $stmt->execute(array(
            ':pid'=>$_GET['profile_id']
        ));
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            echo '<li>'.htmlentities($row['year']).':'.htmlentities($row['name']).'</li>';
        }
        echo '</ul>';

        // adding the position to the view 
        echo'<p>Position: </p><br>';
        echo'<ul>';
        $stmt2=$pdo->prepare("SELECT * FROM position WHERE profile_id =:pid2");
        $stmt2->execute(array(":pid2"=>$_GET['profile_id']));
        while($pos=$stmt2->fetch(PDO::FETCH_ASSOC)){
            echo'<li>'.htmlentities($pos['year']).':'.htmlentities($pos['description']).'</li>';
        }
        echo'</ul>';
        

        ?>

        <br>
        <a href="index.php">Done</a>
        

    </div>
</body>
</html>