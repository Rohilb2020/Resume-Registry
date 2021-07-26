<?php
    session_start(); //not required 
    require_once "pdo.php";
                   

    if ( isset($_POST['cancel'] ) ) {
        header("Location: index.php");
        return;
    }

    $salt="XyZzy12*_"; //for password 
    if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['cnf_pass']) ){
        
        if(strlen($_POST['first_name'])<1 || strlen($_POST['last_name'])<1 || strlen($_POST['email'])<1 || strlen($_POST['pass'])<1 || strlen($_POST['cnf_pass'])<1){
            $_SESSION['error'] = "All fields are required";
            header("Location: account.php");
            return;
        }
        if(strpos($_POST['email'],"@")===false){
            $_SESSION['error']= "Email address must contain @";
            header('Location: account.php');
            return;
        }
        $sql = "SELECT * FROM users where email=:email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':email'=>$_POST['email']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row!=FALSE){
            $_SESSION['error'] = "Email already exists,try different one";
            header('Location: account.php');
            return;
        }
        $pass = $_POST['pass'];
        $cnf = $_POST['cnf_pass'];
        if($pass!=$cnf){
            $_SESSION['error'] = "Password fields do not match. Please retype your password";
            header('Location: account.php');
            return;
        }

        //creating the hashed password
        $password = hash('md5',$salt.$pass);

        //adding the records to the database
        
        $sql = "INSERT INTO users(name,last_name,email,password)
                 VALUES(:name,:lname,:em,:pw)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':name' =>$_POST['first_name'],
            ':lname'=>$_POST['last_name'],
            ':em'   =>$_POST['email'],
            ':pw'   =>$password
        ));
        
        //success message
        $_SESSION['success'] = "Account created successfully";
        header('Location: index.php');
        return;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <title>Create Account</title>
    <?php require_once "bootstrap.php";?>
</head>

<body>
    <div class="container">
    <h1>Create Account</h1>
    <br>
    <h5 style="color:blue;"><i>Please provide the following details</i></h5>
    <br>

    <?php
            if ( isset($_SESSION['error']) ) {
                echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
                unset($_SESSION['error']);
            }
    ?>
    <form method="POST">
        <p><label for="fname">First Name: </label>
        <input type="text" name="first_name" id="fname" size=60></p>
        <br>
        <p><label for="lname">Last Name: </label>
        <input type="text" name="last_name" id="lname" size=60></p>
        <br>
        <p><label for="user">Email: </label>
        <input type="text" name="email" id="user" size=60></p>
        <br>
        <p><label for="password-field">Password: </label>
        <input type="password" name="pass" id="password-field" size=60><button type="button" onclick="viewPassword();"><i class="fas fa-eye" id="pass-status"></i></button></p>
        <br>
        <p><label for="cnf_password-field">Confirm password: </label>
        <input type="password" name="cnf_pass" id="cnf_password-field" size=60><button type="button" onclick="cnfviewPassword();"><i class="fas fa-eye" id="cnf_pass-status"></i></button></p>
        <br>
        <p><input type="submit" value="Create" >
        <input type="submit"  name="cancel" value="Cancel" ></p>

    </form>
</div>
    
    <script>
        function viewPassword(){
        var passwordInput = document.getElementById('password-field');
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
        var cnf_passwordInput = document.getElementById('cnf_password-field');
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