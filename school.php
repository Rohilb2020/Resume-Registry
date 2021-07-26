<?php
    require_once "pdo.php";
    $term = $_GET['term'];  //$_GET['term'] is an inbuilt variable in php that stores the data that we type in a serch box
    $sql = "SELECT * from institution where name LIKE :prefix";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute(array(':prefix'=>$term."%")); // returns all the strings preceded by the $term value
    $retval=array();
    
    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
        $retval[] = $row['name'];
    }

    echo(json_encode($retval,JSON_PRETTY_PRINT));
?>