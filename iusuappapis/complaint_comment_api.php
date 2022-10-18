<?php 
 
 require_once 'connection.php';  
 
 $response = array();
 if(isset($_GET['apicall'])){
 switch($_GET['apicall']){
 
  
case 'make_comment_official':
 
if(isset($_POST['message']) && isset($_POST['complaintId']) && isset($_POST['guildPostId'])){
 
   
    $message=$_POST['message'];
    $complaintId=$_POST['complaintId'];
    
    $guildPostId=$_POST['guildPostId'];
    $date_Time=date("Y-m-d H:i:s");
   

 
 $stmt = $conn->prepare("INSERT INTO complaintcommenttb (message,date_Time,complaintId,guildPostId) VALUES (?,?,?,?)");
 $stmt->bind_param("ssss", $message,$date_Time,$complaintId,$guildPostId);
 
 if($stmt->execute()){
 $stmt->close();
 $response['error'] = false;
 $response['message'] = 'comment posted successfully';
 }
 
 }else{
 $response['error'] = true;
 $response['message'] = "Required params not available";
 }
 
 break;
 
case 'make_comment_student':
 
if(isset($_POST['message']) && isset($_POST['complaintId']) && isset($_POST['regNo'])){
 
   
    $message=$_POST['message'];
    $complaintId=$_POST['complaintId'];
    
    $regNo=$_POST['regNo'];
    $date_Time=date("Y-m-d H:i:s");
   

 
 $stmt = $conn->prepare("INSERT INTO complaintcommenttb (message,date_Time,complaintId,regNo) VALUES (?,?,?,?)");
 $stmt->bind_param("ssss", $message,$date_Time,$complaintId,$regNo);
 
 if($stmt->execute()){
 $stmt->close();
 $response['error'] = false;
 $response['message'] = 'comment posted successfully';
 }
 
 }else{
 $response['error'] = true;
 $response['message'] = "Required params not available";
 }
 
 break;
 
 //in this call we will fetch all the images 
 case 'get_comments':
 if (isset($_GET['complaintId'])){

    $complaintId1=$_GET['complaintId'];

 $stmt = $conn->prepare("SELECT comm.commentId, comm.message, DATE_FORMAT(comm.date_Time,'%D %M,%Y'), comm.complaintId,comm.regNo,stud.firstName,stud.lastName, comm.guildPostId, gp.title FROM complaintcommenttb comm left outer join studenttb stud on comm.regNo=stud.regNo left outer join guildposttb gp on comm.guildPostId = gp.guildPostId where complaintId=?");
 $stmt->bind_param('s',$complaintId1);
 $stmt->execute();
 $stmt->bind_result($commentId, $message, $date_Time,$complaintId, $regNo,$firstName,$lastName,$guildPostId,$gpTitle);
 
 $complaints = array();
 
 //fetching all the images from database
 //and pushing it to array 
 while($stmt->fetch()){
 $temp = array();
 $temp['commentId'] = $commentId; 
 $temp['message'] = $message; 
 $temp['date_Time'] = $date_Time; 
 $temp['complaintId'] = $complaintId; 
 $temp['regNo'] = $regNo; 
 $temp['firstName'] = $firstName;
 $temp['lastName'] = $lastName;
 $temp['guildPostId'] = $guildPostId; 
 $temp['gpTitle'] = $gpTitle; 
 

 
 array_push($complaints, $temp);
 }
 
 //pushing the array in response 
// $response['images'] = $images;
echo json_encode($complaints);

 }else{
    $response['error'] = true;
    $response['message'] = "Required params not available";
    }

 break; 

 
 default: 
 $response['error'] = true;
 $response['message'] = 'Invalid api call';
 }
 
 
 }else{
 header("HTTP/1.0 404 Not Found");
 echo "<h1>404 Not Found</h1>";
 echo "The page that you have requested could not be found.";
 exit();
 }
 
 //displaying the response in json 
// header('Content-Type: application/json');
 echo json_encode($response);

 ?>