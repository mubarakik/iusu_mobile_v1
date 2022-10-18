<?php 
 
 require_once 'connection.php';  
 
 $response = array();
 if(isset($_GET['apicall'])){
 switch($_GET['apicall']){
 
 case 'make_announcement':
 
 if(isset($_POST['title']) && isset($_POST['description']) && isset($_POST['guildOfficialId'])){
   
    $title=$_POST['title'];
    $description=$_POST['description'];
    $date=date("Y-m-d H:i:s");
    $category='Announcement';
    $guildOfficialId=$_POST['guildOfficialId'];

 
 $stmt = $conn->prepare("INSERT INTO posttb (title,description,date_Time,category,guildOfficialId) VALUES (?,?,?,?,?)");
 $stmt->bind_param("sssss", $title,$description,$date,$category,$guildOfficialId);
 
 if($stmt->execute()){
 $stmt->close();
 $response['error'] = false;
 $response['message'] = 'Announcement posted successfully';
 }
 
 }else{
 $response['error'] = true;
 $response['message'] = "Required params not available";
 }
 
 break;
 
 //in this call we will fetch all the images 
 case 'update_announcement':
 
 if(isset($_POST['postId']) && ($_POST['title']) && isset($_POST['description'])){
   

    $postId=$_POST['postId'];
    $title=$_POST['title'];
    $description=$_POST['description'];
    $date=date("Y-m-d H:i:s");
    $category='Announcement';


 
 $stmt = $conn->prepare("UPDATE posttb SET title=?,description=? where postId=?");
 $stmt->bind_param("sss", $title,$description,$postId);
 
 if($stmt->execute()){
 $stmt->close();
 $response['error'] = false;
 $response['message'] = 'Announcement updated successfully';
 }
 
 }else{
 $response['error'] = true;
 $response['message'] = "Required params not available";
 }
 
 break;
 
 //in this call we will fetch all the images 
 case 'getannouncements':

 $stmt = $conn->prepare("SELECT post.postId, post.title, post.description, DATE_FORMAT(post.date_time,'%D %M,%Y'), post.category, post.guildOfficialId, gp.title FROM posttb post left outer join guildofficialtb go on post.guildOfficialId=go.guildOfficialId left outer join guildposttb gp on go.guildPostId=gp.guildPostId WHERE post.category = 'Announcement'order by post.postId DESC ");
 $stmt->execute();
 $stmt->bind_result($id, $title, $description, $date_time,$category,$guildOfficialId,$guildTitle);
 
 $announcements = array();
 
 
 while($stmt->fetch()){
 $temp = array();
 $temp['id'] = $id; 
 $temp['title'] = $title; 
 $temp['description'] = $description; 
 $temp['date_Time'] = $date_time; 
 $temp['category'] = $category; 
 $temp['guildOfficialId'] = $guildOfficialId; 
 $temp['guildPostTitle'] = $guildTitle; 
 
 array_push($announcements, $temp);
 }
 

echo json_encode($announcements);
 break; 

 case 'latest_announcements':
    
         $stmt = $conn->prepare("SELECT post.postId, post.title, post.description, DATE_FORMAT(post.date_time,'%D %M,%Y'), post.category, post.guildOfficialId, gp.title FROM posttb post left outer join guildofficialtb go on post.guildOfficialId=go.guildOfficialId left outer join guildposttb gp on go.guildPostId=gp.guildPostId WHERE post.category = 'Announcement'order by post.postId DESC LIMIT 4");
         $stmt->execute();
         $stmt->bind_result($id, $title, $description, $date_time,$category,$guildOfficialId,$guildTitle);
         
         $announcements = array();
         
        
         while($stmt->fetch()){
         $temp = array();
         $temp['id'] = $id; 
         $temp['title'] = $title; 
         $temp['description'] = $description; 
         $temp['date_Time'] = $date_time; 
         $temp['category'] = $category; 
         $temp['guildOfficialId'] = $guildOfficialId; 
         $temp['guildPostTitle'] = $guildTitle; 
         
         array_push($announcements, $temp);
         }
         
         //pushing the array in response 
    
         echo json_encode($announcements);

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
 //header('Content-Type: application/json');
 echo json_encode($response);

 ?>