<?php
    require_once "pdo.php";
    session_start();

    if(isset($_POST['cancel'])){
        header('Location: index.php');
        return;
    }

    if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ){
        if(strlen($_POST['first_name'])<1 || strlen($_POST['last_name'])<1 || strlen($_POST['email'])<1 || strlen($_POST['headline'])<1 || strlen($_POST['summary'])<1){
            $_SESSION['error_2']="All fields are required";
            header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
            return;
        }

        if(strpos($_POST['email'],"@")===false){
            $_SESSION['error_2']= "Email address must contain @";
            header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
            return;
        }

        //validating position field
        for($i=1;$i<=9;$i++){
            if(!isset($_POST['year'.$i])) continue;
            if(!isset($_POST['desc'.$i])) continue;

            $year= $_POST['year'.$i];
            $desc= $_POST['desc'.$i];
            if(strlen($year)==0 || strlen($desc)==0){
                $_SESSION['error_2']="All fields are required"; 
                header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
                return;
            }
            if(!is_numeric($year)){
                $_SESSION['error_2'] = "Position year must be numeric";
                header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
                return;
            }
        
        }
        //validating education field
        for($i=1;$i<=9;$i++){
            if(!isset($_POST['edu_year'.$i])) continue;
            if(!isset($_POST['edu_school'.$i])) continue;

            $edu_year= $_POST['edu_year'.$i];
            $edu_school= $_POST['edu_school'.$i];
            if(strlen($edu_year)==0 || strlen($edu_school)==0){
                $_SESSION['error_2'] = "All fields are required";
                header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
                return;
            }
            if(!is_numeric($edu_year)){
                $_SESSION['error_2'] = "Education year must be numeric";
                header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
                return;
            }            
        }

        $sql = "UPDATE profile SET first_name=:fn, last_name=:ln, email=:em, headline=:he, summary=:su WHERE profile_id=:pid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
        // ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'],
        ':pid'=> $_POST['profile_id'])
        );
        // $profile_id = $pdo->lastInsertId();
                
        $stmt = $pdo->prepare('DELETE FROM position
                WHERE profile_id=:pid');
        $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));
        
        // Insert the position entries
        $rank = 1;
        for($i=1; $i<=9; $i++) {
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;
            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];
        
            $stmt = $pdo->prepare('INSERT INTO position
                    (profile_id, rank, year, description)
                    VALUES ( :pid, :rank, :year, :desc)');
            $stmt->execute(array(
                ':pid' => $_REQUEST['profile_id'],
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc)
                );
                $rank++;
            }

        $stmt = $pdo->prepare('DELETE FROM education
            WHERE profile_id=:pid');
        $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

        //insering into the education table
        $edu_rank = 1;
        for($i=1;$i<=9;$i++){
            if(!isset($_POST['edu_year'.$i])) continue;
            if(!isset($_POST['edu_school'.$i])) continue;
            $edu_year= $_POST['edu_year'.$i];
            $edu_school= $_POST['edu_school'.$i];

            $institute_id = false;
            $sql = "SELECT institution_id from institution where name=:name";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(':name'=> $edu_school));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row!=false){
                $institute_id = $row['institution_id'];
            }
            if($institute_id===false){
                $sql = "INSERT into institution(name) VALUES(:name)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array(':name'=>$edu_school));
                $institute_id = $pdo->lastInsertId();
            }

            $sql = "INSERT INTO education(profile_id,institution_id,rank,year)
                    VALUES(:pid,:ist_id,:rk,:yr)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':pid'      =>$_REQUEST['profile_id'],
                ':ist_id'   =>$institute_id,
                ':rk'       =>$edu_rank,
                ':yr'       =>$edu_year
            ));
            $edu_rank++;
        }
            $_SESSION['success'] = "Profile edited";
                header('Location: index.php');
                return;
    }

    if ( ! isset($_REQUEST['profile_id']) ) {
        $_SESSION['error'] = "Missing profile_id";
        header('Location: index.php');
        return;
    }
      
    $stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :pid");
    $stmt->execute(array(":pid" => $_REQUEST['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( $row === false ) {
        $_SESSION['error'] = 'Bad value for profile_id';
        header( 'Location: index.php' ) ;
        return;
    }
    
$fn=    $row['first_name'];
$ln=    $row['last_name'];
$em=    $row['email'];
$he=    $row['headline'];
$su=    $row['summary'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>dc70227a</title>
    <?php require_once "bootstrap.php"; ?>

</head>
<body>
<div class="container">

    <h1>Editing Profile for <?= htmlentities($_SESSION['name'])?></h1>
    <?php
    
    if ( isset($_SESSION['error_2']) ) {
        
        echo('<p style="color: red;">'.htmlentities($_SESSION['error_2'])."</p>\n");
        unset($_SESSION['error_2']);
    }
    
?>
    <form method="post">
        <p><label for="fname">First Name:</label>
        <input type="text" name="first_name" id="fname" size=60 value="<?= $fn ?>"></p>
        <br>
        <label for="lname">Last Name:</label>
        <input type="text" name="last_name" id="lname" size=60 value="<?= $ln ?>"></p>
        <br>
        <p><label for="mail">Email:</label>
        <input type="text" name="email" id="mail" size=60 value="<?= $em ?>"></p>
        <br>
        <p><label for="hline">Headline:</label>
        <input type="text" name="headline" id="hline" size=60 value="<?= $he ?>"></p>
        <br>
        <p><label for="about">Summary:</label>
        <br>
        <textarea name="summary" id="about" rows=8 cols=80 ><?= $su ?></textarea> </p>

        <p>Education: <input type="submit" id="addedu" value="+"></p>
        <div id="education_field">
        <?php
            #displaying the old values of education
            $stmt = $pdo->prepare("SELECT * FROM education JOIN institution ON 
                        education.institution_id=institution.institution_id WHERE profile_id =:pid");
            $stmt->execute(array(':pid'=>$_REQUEST['profile_id']));
            $count_edu=0;
            while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                $count_edu++;
                echo '<div id="edu'.$count_edu.'">';
                echo '<p>Year: <input type="text" name="edu_year'.$count_edu.'" value="'.htmlentities($row['year']).'">';
                echo '&nbsp<input type="button" value="-" onclick="$(\'#edu'.$count_edu.'\').remove(); return false;">';
                echo '</p>';
                echo '<p>School: <input type="text" name="edu_school'.$count_edu.'" class="school" size="80" value="'.htmlentities($row['name']).'"></p>';
                echo '</div>';
            }
        ?>
        </div>
        <br>

        <p>Position: <input type="submit" id="addpos" value="+"></p>
        <div id="positions_field">
        <?php 
            
            //displaying the old values of positions 
            $stmt2=$pdo->prepare("SELECT * FROM position WHERE profile_id =:pid2");
            $stmt2->execute(array(":pid2"=>$_REQUEST['profile_id']));
            $count_pos=0;
            while($pos=$stmt2->fetch(PDO::FETCH_ASSOC)){
                $count_pos++;
                echo'<div id= "position'.$count_pos.'">';
                echo'<p>Year: ';
                echo'<input type="text" name="year'.$count_pos.'" value="'.htmlentities($pos['year']).'"/>';
                echo'&nbsp<input type="button" value="-"onclick="$(\'#position'.$count_pos.'\').remove();return false;"></p>';
                echo'<textarea id="desc" name="desc'.$count_pos.'" rows=8 cols=80>'.htmlentities($pos['description']).'</textarea><br>';
                echo'</div><br>';
            }
            // echo nl2br("\n");
        ?>
        </div>

        <p><input type="hidden" name="profile_id" value="<?=htmlentities($_REQUEST['profile_id']) ?>">
        <input type="submit" value="Save" name="save">
        <input type="submit" name="cancel" value="Cancel"></p>


    </form>

    <script>
        count=<?=$count_pos?>;
        count_edu=<?=$count_edu?>;
        $(document).ready(function(){
            window.console && console.log('Document ready');
            $('#addpos').click(function(event){
                event.preventDefault();          //helps to prevent the default action of the submit button i.e.preventing any post or REQUEST REQUEST
                if(count>=9){
                    alert("Maximum of nine position entries exceeded");
                    return;
                }
                count++
                window.console && console.log('Adding position'+count);
                $('#positions_field').append(
                    '<div id= "position'+count+'">\
                    <p>Year: <input type="text" name="year'+count+'"value=""/>\
                    <input type="button" value="-"\
                        onclick="$(\'#position'+count+'\').remove();return false;"></p>\
                    <textarea name="desc'+count+'"rows="8" cols="80"></textarea>\
                    </div><br>');
            });

            // adding script for education entries
            
            $('#addedu').click(function(event){
                event.preventDefault();
                if(count_edu>=9){
                    alert("Maximum of nine education entries exceeded");
                    return;
                }
                count_edu++;
                window.console && console.log('Adding education'+count_edu);
                $('#education_field').append(
                    '<div id="edu'+count_edu+'">\
                        <p>Year: <input type="text" name="edu_year'+count_edu+'">\
                            <input value="-" type="button" onclick="$(\'#edu'+count_edu+'\').remove() ;return false;"></p>\
                        <p><label for="schl">School: </label><input type="text" name="edu_school'+count_edu+'" size=80 class="school" id="schl" value=""></p>\
                    </div>');
                $('.school').autocomplete({
                    source: 'school.php'
                });  

            });
        });
    </script>
    
    <!-- autocomplete function for already filled school fields in the editing mode  -->
    <script>
    $(document).ready(function(){
        $('.school').autocomplete({
                    source: 'school.php'
                });
    });
    </script>

</div>
</body>
</html>