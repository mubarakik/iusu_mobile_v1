<?php  
 $servername = "localhost";  
 $username = "id19030620_iusu_mobile";  
 $password = "<oGb[j5VJO99_*Sz";  
 $database = "id19030620_iusu_mobile_db";  

//$servername = "127.0.0.1";  
//$username = "iusu";  
//$password = "iusu";  
//$database = "iusuappdb";  
//$port = "3306";
//to be use when fetching images
$siteurl ='https://' . 'iusuapp.000webhostapp.com' . '/iusu_mobile_apis/';


$conn = new mysqli($servername, $username, $password, $database);  
if ($conn->connect_error) {  
    die("Connection failed: " . $conn->connect_error);  
}  
?>