<?php 
 
 require_once 'connection.php';  
 
 //We will upload files to this folder
 //So one thing don't forget, also create a folder named uploads inside your project folder i.e. MyApi folder
 define('UPLOAD_PATH', 'event_images/');
  
 //An array to display the response
 $response = array();
 
 //if the call is an api call 
 if(isset($_GET['apicall'])){
 
 //switching the api call 
 switch($_GET['apicall']){
 
 //if it is an upload call we will upload the image
 case 'create_event':
 
 
 //first confirming that we have the image and tags in the request parameter
 if(isset($_FILES['pic']['name']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['guildOfficialId']) && isset($_POST['date_Time'])&& isset($_POST['venue'])){
   
    $title=$_POST['title'];
    $description=$_POST['description'];
    $image=$_FILES['pic']['name'];
    $date=$_POST['date_Time'];
    $category='Event';
    $guildOfficialId=$_POST['guildOfficialId'];
    $venue=$_POST['venue'];


 //uploading file and storing it to database as well 
 try{
 move_uploaded_file($_FILES['pic']['tmp_name'], UPLOAD_PATH . $_FILES['pic']['name']);
//  $stmt = $conn->prepare("INSERT INTO post_tb (title,description,image,date_time,category,go_id) VALUES (?,?,?,sysdate(),'News',?)");
 $stmt = $conn->prepare("INSERT INTO posttb (image,title,description,date_Time,category,guildOfficialId,venue) VALUES (?,?,?,STR_TO_DATE(?, '%d %M, %Y %h:%i'),?,?,?)");
 $stmt->bind_param("sssssss", $_FILES['pic']['name'],$title,$description,$date,$category,$guildOfficialId,$venue);
 if($stmt->execute()){
 $response['error'] = false;
 $response['message'] = 'Event posted successfully';
 }else{
 throw new Exception("Could not upload event");
 }
 }catch(Exception $e){
 $response['error'] = true;
 $response['message'] = 'Could not upload file';
 }
 
 }else{
 $response['error'] = true;
 $response['message'] = "Required params not available";
 }
 
 break;


 case 'getevents':
 

 $stmt = $conn->prepare("SELECT post.postId, post.image, post.title, post.description, DATE_FORMAT(post.date_Time,'%D %M,%Y'), DATE_FORMAT(post.date_Time,'%H:%i'),post.category, post.venue,post.guildOfficialId, gp.title FROM posttb post left outer join guildofficialtb go on post.guildOfficialId=go.guildOfficialId left outer join guildposttb gp on go.guildPostId=gp.guildPostId WHERE post.category = 'Event' order by post.postId DESC ");
 $stmt->execute();
 $stmt->bind_result($id, $image, $title, $description, $date,$time,$category,$venue,$guildOfficialId,$guildTitle);
 
 $images = array();
 
 //fetching all the images from database
 //and pushing it to array 
 while($stmt->fetch()){
 $temp = array();
 $temp['id'] = $id; 
 $temp['image'] = $siteurl. UPLOAD_PATH . $image; 
 $temp['title'] = $title; 
 $temp['description'] = $description; 
 $temp['date'] = $date; 
  $temp['time'] = $time; 
 $temp['category'] = $category;
 $temp['venue'] = $venue;
 $temp['guildOfficialId'] = $guildOfficialId; 
 $temp['gptitle'] = $guildTitle; 
 
 array_push($images, $temp);
 }
 
 //pushing the array in response 
// $response['images'] = $images;
echo json_encode($images);
 break; 
 
 case 'update_event':
 
   if(isset($_POST['postId']) && ($_POST['title']) && isset($_POST['description']) && isset($_FILES['pic']['name']) && isset($_POST['venue'])&& isset($_POST['date_Time'])){
     
  
      $postId=$_POST['postId'];
      $title=$_POST['title'];
      $description=$_POST['description'];
      $image=$_FILES['pic']['name'];
      $venue=$_POST['venue'];
      $date_Time=$_POST['date_Time'];
     

 try{
   move_uploaded_file($_FILES['pic']['tmp_name'], UPLOAD_PATH . $_FILES['pic']['name']);
  //  $stmt = $conn->prepare("INSERT INTO post_tb (title,description,image,date_time,category,go_id) VALUES (?,?,?,sysdate(),'News',?)");
  $stmt = $conn->prepare("UPDATE posttb SET title=?,description=?, image=?, date_Time=? ,venue=? where postId=?");
   $stmt->bind_param("ssssss", $title,$description,$image,$date_Time,$venue,$postId);
    
  
  if($stmt->execute()){
   $response['error'] = false;
   $response['message'] = 'Event updated successfully';
   }else{
   throw new Exception("Could not update Event");
   }
   }catch(Exception $e){
   $response['error'] = true;
   $response['message'] = 'Could not upload file';
   }
   
   }else{
   $response['error'] = true;
   $response['message'] = "Required params not available";
   }
 break;  

 case 'latest_events':
 
 //getting server ip for building image url 
 $server_ip = gethostbyname(gethostname());
 
 //query to get images from database
 $stmt = $conn->prepare("SELECT post.post_id, post.image, post.title, post.description, DATE_FORMAT(post.date_time,'%D %M,%Y'), DATE_FORMAT(post.date_time,'%H:%i'),post.category, post.go_id, gp.gptitle FROM post_tb post left outer join guild_official_tb go on post.go_id=go.gpost_id left outer join guild_posts_tb gp on go.gpost_id=gp.gpost_id WHERE post.category = 'Event' order by post.post_id DESC LIMIT 4 ");
 $stmt->execute();
 $stmt->bind_result($id, $image, $title, $description, $date,$time,$category,$go_id,$guildTitle);
 
 $images = array();
 
 //fetching all the images from database
 //and pushing it to array 
 while($stmt->fetch()){
 $temp = array();
 $temp['id'] = $id; 
 $temp['image'] = 'http://' . 'iusuapp.000webhostapp.com' . '/iusu_app_conn_v4/'. UPLOAD_PATH . $image; 
 $temp['title'] = $title; 
 $temp['description'] = $description; 
 $temp['date'] = $date; 
  $temp['time'] = $time; 
 $temp['category'] = $category; 
 $temp['go_id'] = $go_id; 
 $temp['gptitle'] = $guildTitle; 
 
 array_push($images, $temp);
 }
 
 //pushing the array in response 
// $response['images'] = $images;
echo json_encode($images);
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