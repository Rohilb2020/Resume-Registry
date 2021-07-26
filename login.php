<?php
session_start();
require_once "pdo.php";

if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}
$salt = "XyZzy12*_";

if (isset($_POST['email']) && isset($_POST['pass'])) {
    unset($_SESSION['email']);
    $check = hash('md5', $salt . $_POST['pass']);
    $stmt = $pdo->prepare("SELECT user_id,name FROM users WHERE email=:em AND password=:pw");
    $stmt->execute(array(
        ':em' => $_POST['email'],
        ':pw' => $check
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row !== FALSE) {
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        header('Location:index.php');
        return;
    } else {
        $_SESSION['error'] = "Incorrect password";
        header('Location:login.php');
        return;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <title>dc70227a</title>
    <?php require_once "bootstrap.php"; ?>
</head>

<!-- verifying that the entered credentials are valid using JS  -->
<script>
    function doValidate() {
        console.log('Validating...');
        try {
            addr = document.getElementById('username').value;
            pw = document.getElementById('password-field').value;
            console.log("Validating addr=" + addr + " pw=" + pw);
            if (addr == null || addr == "" || pw == null || pw == "") {
                alert("Both fields must be filled out");
                return false;
            }
            if (addr.indexOf('@') == -1) {
                alert("Email address must contain @");
                return false;
            }
            return true;
        } catch (e) {
            return false;
        }
        // return false;
    }
</script>

<body>
    <div class="container">
        <h1>Please Log In</h1>

        <?php
        if (isset($_SESSION['error'])) {
            echo ('<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n");
            unset($_SESSION['error']);
        }

        if (isset($_SESSION['success'])) {
            echo ('<p style="color: green;">' . $_SESSION['success'] . '</p>');
            unset($_SESSION['success']);
        }
        ?>

        <form method="post">
            <label for="username">Email </label>
            <input type="text" name="email" id="username">
            <br>
            <label for="password-field">Password</label>
            <input type="password" id="password-field" name="pass"><button type="button" onclick="viewPassword()"><i class="fas fa-eye" id="pass-status"></i></button>
            <br>
            <input type="submit" onclick="return doValidate();" value="Log In">
            <a href="index.php">Cancel</a>
            <br>
            <a href="forgot.php">Forgot your password</a>

        </form>
    </div>

    <script>
        function viewPassword() {
            var passwordInput = document.getElementById('password-field');
            var passStatus = document.getElementById('pass-status');

            if (passwordInput.type == 'password') {
                passwordInput.type = 'text';
                passStatus.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passStatus.className = 'fas fa-eye';
            }
        }
    </script>

</body>

</html>