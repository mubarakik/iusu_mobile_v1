<?php 
 
 require_once 'connection.php';  
 
 define('UPLOAD_PATH', 'event_images/');
 //An array to display the response
 $response = array();
 
 //if the call is an api call 
 if(isset($_GET['apicall'])){
 
 //switching the api call 
 switch($_GET['apicall']){
 
 //if it is an upload call we will upload the image
 case 'add_to_favorite':
 
 
 //first confirming that we have the image and tags in the request parameter
 if(isset($_POST['regNo']) && isset($_POST['postId'])){
   
    $regNo=$_POST['regNo'];
    $postId=$_POST['postId'];
   
   
 //uploading file and storing it to database as well 
 try{

 $stmt = $conn->prepare("INSERT INTO favoritetb (reg_no,post_id) VALUES (?,?)");
 $stmt->bind_param("ss", $regNo,$postId);
 if($stmt->execute()){
 $response['error'] = false;
 $response['message'] = 'post added to favorites successfully';
 }else{
 throw new Exception("Could not add post to favorites");
 }
 }catch(Exception $e){
 $response['error'] = true;
 $response['message'] = 'Could not add post to favorites';
 }
 
 }else{
 $response['error'] = true;
 $response['message'] = "Required params not available";
 }
 
 break;
 
 //in this call we will fetch all the images 
 case 'get_favorites':
    if(isset($_GET['regNo'])){
        $_regNo=$_GET['regNo'];


        try{
        $stmt = $conn->prepare("SELECT fav.favorite_id, fav.reg_no, fav.post_id, post.image, post.title, post.description, DATE_FORMAT(post.date_time,'%D %M,%Y'),  DATE_FORMAT(post.date_time,'%H:%i'),post.category,post.venue, post.guild_official_id, gp.title FROM favoritetb fav left outer join  post_tb post on fav.post_id=post.post_id left outer join guild_official_tb go on post.guild_official_id=go.guild_official_id left outer join guild_posts_tb gp on go.guild_post_id=gp.guild_post_id WHERE post.category = 'Event' and fav.reg_no = ?  order by post.post_id DESC ");
        $stmt->bind_param('s',$_regNo);
        $stmt->execute();
        $stmt->bind_result($favoriteId,$regNo,$postId, $image, $title, $description, $date, $time,$category,$venue,$guildOfficialId,$guildTitle);
        
        $images = array();
        
        //fetching all the images from database
        //and pushing it to array 
        while($stmt->fetch()){
        $temp = array();
       
        $temp['favoriteId'] = $favoriteId; 
        $temp['regNo'] = $regNo; 
        $temp['postId'] = $postId; 
        $temp['image'] = $siteurl. UPLOAD_PATH . $image; 
        $temp['title'] = $title; 
        $temp['description'] = $description; 
        $temp['date'] = $date; 
         $temp['time'] = $time; 
        $temp['category'] = $category; 
          $temp['venue'] = $venue; 
        $temp['guildOfficialId'] = $guildOfficialId; 
        $temp['guildPostTitle'] = $guildTitle; 
        
        array_push($images, $temp);
        }
        
        //pushing the array in response 
       // $response['images'] = $images;
       echo json_encode($images);
    }catch(Exception $e){
        $response['error'] = true;
        $response['message'] = 'Could not fetch  favorites';
        }
    } else{
        $response['error'] = true;
        $response['message'] = "Required params not available";
        }
 break; 

 case 'remove_from_favorites':
 
   if(isset($_POST['favoriteId'])){
     
  
      $favoriteId=$_POST['favoriteId'];
        
  
  //uploading file and storing it to database as well 
 try{
  $stmt = $conn->prepare("DELETE FROM favoritetb where favorite_id=?");
   $stmt->bind_param("s", $favoriteId);
    
  
  if($stmt->execute()){
   $response['error'] = false;
   $response['message'] = 'Removed from favorites successfully';
   }else{
   throw new Exception("Could not remove from faorites");
   }
   }catch(Exception $e){
   $response['error'] = true;
   $response['message'] = 'Could not remove from faorites';
   }
   
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