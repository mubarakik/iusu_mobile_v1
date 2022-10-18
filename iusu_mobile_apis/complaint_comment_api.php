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
   

 
 $stmt = $conn->prepare("INSERT INTO complaint_comment_tb (message,date_time,complaint_id,guild_post_id) VALUES (?,?,?,?)");
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
   

 
 $stmt = $conn->prepare("INSERT INTO complaint_comment_tb (message,date_time,complaint_id,reg_no) VALUES (?,?,?,?)");
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

 $stmt = $conn->prepare("SELECT comm.comment_id, comm.message, DATE_FORMAT(comm.date_time,'%D %M,%Y'), comm.complaint_id,comm.reg_no,stud.first_name,stud.last_name, comm.guild_post_id, gp.title FROM complaint_comment_tb comm left outer join student_tb stud on comm.reg_no=stud.reg_no left outer join guild_posts_tb gp on comm.guild_post_id = gp.guild_post_id where complaint_id=?");
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