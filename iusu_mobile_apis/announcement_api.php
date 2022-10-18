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

 
 $stmt = $conn->prepare("INSERT INTO posttb (title,description,date_time,category,guild_official_id) VALUES (?,?,?,?,?)");
 $stmt->bind_param("sssss", $title,$description,$date,$category,$guildOfficialId);
 
 if($stmt->execute()){
 $stmt->close();
 $response['error'] = false;
 $response['message'] = 'Announcement created successfully';
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


 
 $stmt = $conn->prepare("UPDATE post_tb SET title=?,description=? where post_id=?");
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
 
  
 case 'getannouncements':

 $stmt = $conn->prepare("SELECT post.post_id, post.title, post.description, DATE_FORMAT(post.date_time,'%D %M,%Y'), post.category, post.guild_official_id, gp.title FROM post_tb post left outer join guild_official_tb go on post.guild_official_id=go.guild_official_id left outer join guild_posts_tb gp on go.guild_post_id=gp.guild_post_id WHERE post.category = 'Announcement'order by post.post_id DESC ");
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


 case 'my_announcements':

   if(isset($_POST['guildOfficialId'])){
      $guildOfficialId = $_POST['guildOfficialId'];

      $stmt = $conn->prepare("SELECT post.post_id, post.title, post.description, DATE_FORMAT(post.date_time,'%D %M,%Y'), post.category, post.guild_official_id, gp.title FROM post_tb post left outer join guild_official_tb go on post.guild_official_id=go.guild_official_id left outer join guild_posts_tb gp on go.guild_post_id=gp.guild_post_id WHERE post.category = 'Announcement' AND post.guild_official_id=? order by post.post_id DESC ");
      $stmt->bind_param("s",$guildOfficialId);
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

   }else{
   $response['error'] = true;
   $response['message'] = "Required params not available";
   }
   break; 

 case 'latest_announcements':
    
         $stmt = $conn->prepare("SELECT post.post_id, post.title, post.description, DATE_FORMAT(post.date_time,'%D %M,%Y'), post.category, post.guild_official_id, gp.title FROM post_tb post left outer join guild_official_tb go on post.guild_official_id=go.guild_official_id left outer join guild_post_tb gp on go.guild_post_id=gp.guild_post_id WHERE post.category = 'Announcement'order by post.post_id DESC LIMIT 4");
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