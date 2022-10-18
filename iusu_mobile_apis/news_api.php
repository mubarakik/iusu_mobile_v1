<?php 
 
 require_once 'connection.php';  
 
 //We will upload files to this folder
 //So one thing don't forget, also create a folder named uploads inside your project folder i.e. MyApi folder
 define('UPLOAD_PATH', 'news_images/');
  
 //An array to display the response
 $response = array();
 
 //if the call is an api call 
 if(isset($_GET['apicall'])){
 
 //switching the api call 
 switch($_GET['apicall']){
 
 //if it is an upload call we will upload the image
 case 'make_post':
 
 
 //first confirming that we have the image and tags in the request parameter
 if(isset($_FILES['pic']['name']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['guildOfficialId'])){
   
    $title=$_POST['title'];
    $description=$_POST['description'];
    $image=$_FILES['pic']['name'];
    $date=date("Y-m-d H:i:s");
    $category='News';
    $guildOfficialId=$_POST['guildOfficialId'];


 //uploading file and storing it to database as well 
 try{
 move_uploaded_file($_FILES['pic']['tmp_name'], UPLOAD_PATH . $_FILES['pic']['name']);

 $stmt = $conn->prepare("INSERT INTO post_tb (image,title,description,date_time,category,guild_official_id) VALUES (?,?,?,?,?,?)");
 $stmt->bind_param("ssssss", $_FILES['pic']['name'],$title,$description,$date,$category,$guildOfficialId);
 if($stmt->execute()){
 $response['error'] = false;
 $response['message'] = 'News posted successfully';
 }else{
 throw new Exception("Could not post news");
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
 
 //in this call we will fetch all the images 
 case 'getnews':
 
 $stmt = $conn->prepare("SELECT post.post_id, post.image, post.title, post.description, DATE_FORMAT(post.date_time,'%D %M,%Y'), post.category, post.guild_official_id, gp.title FROM post_tb post left outer join guild_official_tb go on post.guild_official_id=go.guild_official_id left outer join guild_posts_tb gp on go.guild_post_id=gp.guild_post_id WHERE post.category = 'News' order by post.post_id DESC ");
 
 $stmt->execute();
 $stmt->bind_result($id, $image, $title, $description, $date_time,$category,$guildOfficialId,$guildTitle);
 
 $images = array();
 
 //fetching all the images from database
 //and pushing it to array 
 while($stmt->fetch()){
 $temp = array();
 $temp['id'] = $id; 
 $temp['image'] = $siteurl. UPLOAD_PATH . $image; 
 $temp['title'] = $title; 
 $temp['description'] = $description; 
 $temp['date_Time'] = $date_time; 
 $temp['category'] = $category; 
 $temp['guildOfficialId'] = $guildOfficialId; 
 $temp['guildPostTitle'] = $guildTitle; 
 
 array_push($images, $temp);
 }
 
 //pushing the array in response 
// $response['images'] = $images;
echo json_encode($images);
 break; 


 case 'my_news':
  if(isset($_POST['guildOfficialId'])){
    $guildOfficialId = $_POST['guildOfficialId'];

  $stmt = $conn->prepare("SELECT post.post_id, post.image, post.title, post.description, DATE_FORMAT(post.date_time,'%D %M,%Y'), post.category, post.guild_official_id, gp.title FROM post_tb post left outer join guild_official_tb go on post.guild_official_id=go.guild_official_id left outer join guild_posts_tb gp on go.guild_post_id=gp.guild_post_id WHERE post.category = 'News' AND post.guild_official_id = ? order by post.post_id DESC ");
  $stmt->bind_param("s",$guildOfficialId);
  $stmt->execute();
  $stmt->bind_result($id, $image, $title, $description, $date_time,$category,$guildOfficialId,$guildTitle);
  
  $images = array();
  
  //fetching all the images from database
  //and pushing it to array 
  while($stmt->fetch()){
  $temp = array();
  $temp['id'] = $id; 
  $temp['image'] = $siteurl. UPLOAD_PATH . $image; 
  $temp['title'] = $title; 
  $temp['description'] = $description; 
  $temp['date_Time'] = $date_time; 
  $temp['category'] = $category; 
  $temp['guildOfficialId'] = $guildOfficialId; 
  $temp['guildPostTitle'] = $guildTitle; 
  
  array_push($images, $temp);
  }
  
 
 echo json_encode($images);

 
}else{
  $response['error'] = true;
  $response['message'] = "Required params not available";
  }
  break; 
 

 case 'update_news':
 
   if(isset($_POST['postId']) && ($_POST['title']) && isset($_POST['description']) && isset($_FILES['pic']['name'])){
     
  
      $postId=$_POST['postId'];
      $title=$_POST['title'];
      $description=$_POST['description'];
      $image=$_FILES['pic']['name'];
     
  
  
   
  
  //uploading file and storing it to database as well 
 try{
   move_uploaded_file($_FILES['pic']['tmp_name'], UPLOAD_PATH . $_FILES['pic']['name']);
  //  $stmt = $conn->prepare("INSERT INTO post_tb (title,description,image,date_time,category,go_id) VALUES (?,?,?,sysdate(),'News',?)");
  $stmt = $conn->prepare("UPDATE post_tb SET title=?,description=?, image=? where post_id=?");
   $stmt->bind_param("ssss", $title,$description,$image,$postId);
    
  
  if($stmt->execute()){
   $response['error'] = false;
   $response['message'] = 'News updated successfully';
   }else{
   throw new Exception("Could not update news");
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
   
 
  case 'latest_news':
 
 $stmt = $conn->prepare("SELECT post.post_id, post.image, post.title, post.description, DATE_FORMAT(post.date_time,'%D %M,%Y'), post.category, post.guild_official_id, gp.title FROM post_tb post left outer join guild_official_tb go on post.guild_official_id=go.guild_official_id left outer join guild_posts_tb gp on go.guild_post_id=gp.guild_post_id WHERE post.category = 'News' order by post.post_id DESC LIMIT 4 ");
 
 $stmt->execute();
 $stmt->bind_result($id, $image, $title, $description, $date_Time,$category,$guildOfficialId,$guildTitle);
 
 $images = array();
 
 while($stmt->fetch()){
 $temp = array();
 $temp['id'] = $id; 
 $temp['image'] = $siteurl . UPLOAD_PATH . $image; 
 $temp['title'] = $title; 
 $temp['description'] = $description; 
 $temp['date_Time'] = $date_Time; 
 $temp['category'] = $category; 
 $temp['guildOfficialId'] = $guildOfficialId; 
 $temp['guildPostTitle'] = $guildTitle; 
 
 array_push($images, $temp);
 }
 
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