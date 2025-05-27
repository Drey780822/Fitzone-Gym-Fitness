<?php  
     include ("include/db.php");


     session_start();
include ("include/db.php");
include ("include/functions.php");

$userLogged = $_SESSION['user_email'];

$results=getUser($con,$userLogged);


 if(isset($_POST["export"]))  
 {  
    header('Content-Type: text/csv; charset=utf-8');  
    header('Content-Disposition: attachment; filename=data.csv');  
    $output = fopen("php://output", "w");  
    fputcsv($output, array('user_id','user_email','user_name','user_pass',	'user_weight','user_age','user_contact','package','user_surname','Trainser','Date','time'));  
    $query = "SELECT * from users WHERE user_email = '$userLogged'";  
    $result = mysqli_query($con, $query);  
    while($row = mysqli_fetch_assoc($result))  
    {  
         fputcsv($output, $row);  
    }  
    fclose($output); 
 }  
 ?>