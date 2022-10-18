<?php   
  require_once 'connection.php';  
  $response = array();  
  if(isset($_GET['apicall'])){  
  switch($_GET['apicall']){  
  case 'add_guild_post':  
    if(isTheseParametersAvailable(array('title','description'))){  
    $title = $_POST['title'];
    $description = $_POST['description'];   
 
    

    $stmt = $conn->prepare("SELECT guild_post_id FROM guild_post_tb WHERE title = ?");  
    $stmt->bind_param("s", $title);  
    $stmt->execute();  
    $stmt->store_result();  
   
    if($stmt->num_rows > 0){  
        $response['error'] = true;  
        $response['message'] = 'Post already registered';  
        $stmt->close();  
    }  
    else{  
    
        $stmt = $conn->prepare("INSERT INTO guild_posts_tb (title,description) VALUES (?, ?)");  
        
        
        $stmt->bind_param("ss", $title, $description);  
   
       
        if($stmt->execute()){  
           
            $stmt->close();  
   
            $response['error'] = false;   
            $response['message'] = 'Post added successfully';   
            
        }  
    } 

  
  }else{  
    $response['error'] = true;   
   $response['message'] = 'required parameters are not available'; 
}  
break;   

case 'fetch_guild_posts':  
    $stmt = $conn->prepare("SELECT guild_post_id, title, description FROM guild_posts_tb");
    $stmt->execute();
    $stmt->bind_result($guildPostId, $title, $description);
    
    $guild_posts = array();
    
    //fetching all the images from database
    //and pushing it to array 
    while($stmt->fetch()){
    $temp = array();
    $temp['guildPostId'] = $guildPostId; 
    $temp['title'] = $title; 
    $temp['description'] = $description; 
        
    array_push($guild_posts, $temp);
    }
    
    //pushing the array in response 
   // $response['images'] = $images;
   echo json_encode($guild_posts);

break;   

case 'fetch_guild_posttitles':  
    $stmt = $conn->prepare("SELECT  title FROM guild_posts_tb");
    $stmt->execute();
    $stmt->bind_result($title);
    
    $guild_posttitles = array();
    
    //fetching all the images from database
    //and pushing it to array 
    while($stmt->fetch()){
    $temp = array();
 
   
    $temp['title'] = $title; 
        
    array_push($guild_posttitles, $temp);
    }
    
    //pushing the array in response 
   // $response['images'] = $images;
   echo json_encode($guild_posttitles);

break;


case 'update_guild_posts':  
    if(isTheseParametersAvailable(array('guildPostId','title','description'))){  
    $title = $_POST['title'];
    $description = $_POST['description']; 
    $guildPostId = $_POST['guildPostId']; 

 
      
        $stmt = $conn->prepare("UPDATE guild_posts_tb SET title=?,description=? WHERE guild_post_id=?");  
        
       $stmt->bind_param("sss", $title, $description,$guildPostId);  
   
        
        if($stmt->execute()){ 
            $stmt = $conn->prepare("SELECT guild_post_id, title, description FROM guild_posts_tb where guild_post_id=?");
            
            $stmt->bind_param("s",$guildPostId);  
            $stmt->execute();  
            $stmt->bind_result($guildPostId, $title, $description);  
            $stmt->fetch();  
   
            $gpost = array(  
            'guildPostId'=>$guildPostId,   
            'title'=>$title,   
            'description'=>$description,   
                        
            );  
           
            $stmt->close();  
   
            $response['error'] = false;   
            $response['message'] = 'Post updated successfully';   
            $response['gpost'] = $gpost;   
            
        }  
    

  
  }else{  
    $response['error'] = true;   
   $response['message'] = 'required parameters are not available'; 
}  
break; 


default:   
 $response['error'] = true;   
 $response['message'] = 'Invalid Operation Called';  
}  
}  
else{  
 $response['error'] = true;   
 $response['message'] = 'Invalid API Call';  
}  
echo json_encode($response);  
function isTheseParametersAvailable($params){  
foreach($params as $param){  
 if(!isset($_POST[$param])){  
     return false;   
  }  
}  
return true;   
}  
?>  