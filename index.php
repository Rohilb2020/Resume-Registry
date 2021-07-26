<?php
    require_once "pdo.php";
    session_start();
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
        <h1>R B Resume Registry</h1>

        <?php 
            $flag=FALSE;
            if(isset($_SESSION['error'])){
                echo('<p style = "color: red;">'.$_SESSION['error'].'</p>');
                unset($_SESSION['error']);
            }
            
            if(isset($_SESSION['success'])){
                echo('<p style="color: green;">'.$_SESSION['success'].'</p>');
                unset($_SESSION['success']);
            }


            if(!isset($_SESSION['email'])){
                echo('<p><a href="login.php">Please log in</a></p>');
                echo('<p><a href="account.php">Create account</a></p>');
                // echo('<p>Attempt to <a href="add.php">add data</a> without logging in</p>');
                $stmt =$pdo->query("SELECT * FROM profile");
                // if($stmt->fetch(PDO::FETCH_ASSOC)){
                    echo "<table border = '2'>"."\n";
                    echo"<tr>";
                    echo "<th>Name</th> <th>Headline</th>";
                    echo"</tr>";
                    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                        $flag=TRUE;
                        echo "<tr><td>";
                        echo ('<a href="view.php?profile_id='.$row['profile_id'].'" >'.htmlentities($row['first_name']).'</a>');
                        echo "</td><td>";
                        echo (htmlentities($row['headline']));
                        echo "</td></tr>\n";
                    }
                echo "</table>\n";
                // }
                
                if($flag===FALSE){
                    echo "No rows found";
                }
            }

            else{
                
                $stmt =$pdo->query("SELECT * FROM profile");
                // if($stmt->fetch(PDO::FETCH_ASSOC)){
                    echo "<table border = '2'>"."\n";
                    echo"<tr>";
                    echo "<th>Name</th> <th>Headline</th> <th>Action</th>";
                    echo"</tr>";
                    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                        $flag=TRUE;
                        echo "<tr><td>";
                        echo ('<a href="view.php?profile_id='.$row['profile_id'].'" >'.htmlentities($row['first_name']).'</a>');
                        echo "</td><td>";
                        echo (htmlentities($row['headline']));
                        echo "</td>";
                        // checking which user is logged in and showing only the entries corresponding to the woner signed in
                        
                        echo "<td>";
                        if($row['user_id']===$_SESSION['user_id']){
                            echo '<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> /';
                            echo '<a href="delete.php?profile_id='.$row['profile_id'].'"> Delete </a>';    
                        }
                        echo "</td>";
                        echo "</tr>\n";
                    }
                    echo "</table>\n";
                // }
                

                if($flag===FALSE){
                    echo "No rows found";
                }
                echo "<br>";

                echo('<p><a href="add.php">Add New Entry</a></p>');
                echo('<p><a href="logout.php">Logout</a></p>');
                echo'<br>';
                echo('<p><a href="userinfo.php?user_id='.$_SESSION['user_id'].'">View profile</a></p>');
            }

            
        ?>

    </div>
</body>
</html>