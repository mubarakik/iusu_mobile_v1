<?php 
 
 require_once 'connection.php';  
 
 $response = array();
 if(isset($_GET['apicall'])){
 switch($_GET['apicall']){
 
  
 case 'make_complaint':
 
if(isset($_POST['subject']) && isset($_POST['message']) && isset($_POST['regNo'])&& isset($_POST['guildPostId'])){
 
   
    $subject=$_POST['subject'];
    $message=$_POST['message'];
    $regNo=$_POST['regNo'];
    $guildPostId=$_POST['guildPostId'];
    $date_Time=date("Y-m-d H:i:s");
   

 
 $stmt = $conn->prepare("INSERT INTO complainttb (subject,message,date_Time,regNo,guildPostId) VALUES (?,?,?,?,?)");
 $stmt->bind_param("sssss", $subject,$message,$date_Time,$regNo,$guildPostId);
 
 if($stmt->execute()){
 $stmt->close();
 $response['error'] = false;
 $response['message'] = 'Complaint uploaded successfully';
 }
 
 }else{
 $response['error'] = true;
 $response['message'] = "Required params not available";
 }
 
 break;
 
 //in this call we will fetch all the images 
 case 'get_complaints':
 

 $stmt = $conn->prepare("SELECT comp.complaintId,comp.subject, comp.message, DATE_FORMAT(comp.date_Time,'%D %M,%Y'), comp.regNo,stud.firstName,stud.lastName, comp.guildPostId, gp.title FROM complainttb comp left outer join studenttb stud on comp.regNo=stud.regNo left outer join guildposttb gp on comp.guildPostId = gp.guildPostId");
 $stmt->execute();
 $stmt->bind_result($complaintId,$subject, $message, $date_Time, $regNo,$firstName,$lastName,$guildPostId,$gpTitle);
 
 $complaints = array();
 
 //fetching all the images from database
 //and pushing it to array 
 while($stmt->fetch()){
 $temp = array();
 $temp['complaintId'] = $complaintId; 
 $temp['subject'] = $subject; 
 $temp['message'] = $message; 
 $temp['date_Time'] = $date_Time; 
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
 break; 


case 'get_guildpost_complaints':
 
if(isset($_POST['guildPostId'])){

    $gpostId = $_POST['guildPostId'];

    $stmt = $conn->prepare("SELECT comp.complaintId,comp.subject, comp.message, DATE_FORMAT(comp.date_Time,'%D %M,%Y'), comp.regNo,stud.firstName,stud.lastName, comp.guildPostId, gp.title FROM complainttb comp left outer join studenttb stud on comp.regNo=stud.regNo left outer join guildposttb gp on comp.guildPostId = gp.guildPostId where comp.guildPostId=?");
    $stmt->bind_param('s',$gpostId);
    $stmt->execute();
    $stmt->bind_result($complaintId,$subject, $message, $date_Time, $regNo,$firstName,$lastName,$guildPostId,$gpTitle);
    
    $complaints = array();
    
    //fetching all the images from database
    //and pushing it to array 
    while($stmt->fetch()){
    $temp = array();
    $temp['complaintId'] = $complaintId; 
    $temp['subject'] = $subject; 
    $temp['message'] = $message; 
    $temp['date_Time'] = $date_Time; 
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

case 'get_student_complaints':
 
if(isset($_GET['regNo'])){

    $reg_no = $_GET['regNo'];

    $stmt = $conn->prepare("SELECT comp.complaintId,comp.subject, comp.message, DATE_FORMAT(comp.date_Time,'%D %M,%Y'), comp.regNo,stud.firstName,stud.lastName, comp.guildPostId, gp.title FROM complainttb comp left outer join studenttb stud on comp.regNo=stud.regNo left outer join guildposttb gp on comp.guildPostId = gp.guildPostId where comp.regNo=?");
    $stmt->bind_param('s',$reg_no);
    $stmt->execute();
    $stmt->bind_result($complaintId,$subject, $message, $date_Time, $regNo,$firstName,$lastName,$guildPostId,$gpTitle);
    
    $complaints = array();
    
    //fetching all the images from database
    //and pushing it to array 
    while($stmt->fetch()){
    $temp = array();
    $temp['complaintId'] = $complaintId; 
    $temp['subject'] = $subject; 
    $temp['message'] = $message; 
    $temp['date_Time'] = $date_Time; 
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

 case 'update_complaint':

    if(isset($_POST['complaintId']) && isset($_POST['subject']) && isset($_POST['message']) && isset($_POST['guildPostId'])){
    $complaintId=$_POST['complaintId'];

    $stmt = $conn->prepare("UPDATE complainttb set subject=?, message=?, guildPostId=? WHERE complaintId=?");
    $stmt->bind_param('ssss',$subject, $message,$guildPostId, $complaintId);
   
   
    if($stmt->execute()){
        $stmt->close();
        $response['error'] = false;
        $response['message'] = 'Complaint updated successfully';
        }
   
    
    
    }
    
    else{
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