<?php   
  require_once 'connection.php';  
  define('UPLOAD_PATH', 'profileImages/');
  $response = array();  
  if(isset($_GET['apicall'])){  
  switch($_GET['apicall']){  
  case 'add_guild_official':  
    if(isTheseParametersAvailable(array('academicYear','regNo','guildPostId'))){  
    $regNo = $_POST['regNo'];
    $academicYear = $_POST['academicYear'];   
    $guildPostId = $_POST['guildPostId'];   
 
    // $stmt = $conn->prepare("SELECT stud.reg_no,stud.reg_no,stud.first_name,stud.last_name,stud.gender,
    // stud.campus,stud.faculty,stud.phone,stud.email,stud.password,go.go_id,
    // go.c,gp.gptitle,gp.role FROM student_tb stud left outer join guild_official_tb go 
    // on stud.reg_no=go.reg_no left outer join guild_posts_tb gp on go.gpost_id=gp.gpost_id WHERE stud.email = ?");   
   

    $stmt = $conn->prepare("SELECT guild_official_id FROM guild_official_tb WHERE regNo = ?");  
    $stmt->bind_param("s", $regNo);  
    $stmt->execute();  
    $stmt->store_result();  

    $stmt2 = $conn->prepare("SELECT guild_official_id FROM guild_official_tb WHERE guild_post_id = ?");  
    $stmt2->bind_param("s", $guildPostId);  
    $stmt2->execute();  
    $stmt2->store_result();  

   
    if($stmt->num_rows > 0){  
        $response['error'] = true;  
        $response['message'] = 'RegNo already already registered to a post';  
        $stmt->close();  
    } elseif($stmt2->num_rows > 0){  
      $response['error'] = true;  
      $response['message'] = 'Guild office already assigned to another student';  
      $stmt->close();  
  }
    else{  
    
        $stmt = $conn->prepare("INSERT INTO guild_official_tb (reg_no,academic_year,guild_post_id) VALUES (?,?,?)");  
        
        $stmt->bind_param("sss", $regNo, $academicYear,$guildPostId);  
   
   
        if($stmt->execute()){  
           
            $stmt->close();  
   
            $response['error'] = false;   
            $response['message'] = 'Official registered successfully';   
            
        }  
    } 

  
  }else{  
    $response['error'] = true;   
   $response['message'] = 'required parameters are not available'; 
}  
break;   

case 'fetch_guild_officials':  
   
    $stmt = $conn->prepare("SELECT stud.first_name,stud.last_name,stud.campus,stud.phone,stud.email,stud.profile_image,go.guild_official_id,go.academic_year,gp.title,gp.description FROM student_tb stud right outer join guild_official_tb go on stud.reg_no=go.reg_no right outer join guild_posts_tb gp on go.guild_post_id=gp.guild_post_id");   
   
    $stmt->execute();
    $stmt->bind_result($firstName,$lastName, $campus, $phone,$email,$profileImage,$guildOfficialId,$academicYear,$title,$description);
    
    $guild_officials = array();
    
  
    while($stmt->fetch()){
    $temp = array();
    $temp['firstName'] = $firstName; 
    $temp['lastName'] = $lastName; 
    $temp['campus'] = $campus; 
    $temp['phone'] = $phone; 
    $temp['email'] = $email; 
    $temp['profile_image'] = $siteurl . UPLOAD_PATH . $profile_image; 
    $temp['academicYear'] = $academicYear; 
    $temp['title'] = $title; 
    $temp['description'] = $description; 
        
    array_push($guild_officials, $temp);
    }
    
    //pushing the array in response 
   // $response['images'] = $images;
   echo json_encode($guild_officials);

break;   



case 'update_guild_officials':  
    if(isTheseParametersAvailable(array('guildOfficialId','academicYear','regNo','guildPostId'))){  
    $guildOfficialId = $_POST['guildOfficialId'];
    $academicYear = $_POST['academicYear']; 
    $regNo = $_POST['regNo']; 
    $guildPostId = $_POST['guildPostId']; 

 
      
        $stmt = $conn->prepare("UPDATE guild_official_tb SET academic_year=?,reg_no=?,guild_post_id=? WHERE guild_official_id=?");  
        
       $stmt->bind_param("ssss", $academicYear, $regNo,$guildPostId,$guildOfficialId);  
   
        
        if($stmt->execute()){ 
            $stmt = $conn->prepare("SELECT academic_year, reg_no, guild_post_id FROM guild_official_tb where guild_post_id=?");
            
         
            $stmt->bind_param("s",$guildOfficialId);  
            $stmt->execute();  
            $stmt->bind_result($academicYear, $regNo, $guildPostId);  
            $stmt->fetch();  
   
            $guild_official = array(  
            'academicYear'=>$academicYear,   
            'regNo'=>$regNo,   
            'guildPostId'=>$guildPostId,   
                        
            );  
           
            $stmt->close();  
   
            $response['error'] = false;   
            $response['message'] = 'Official updated successfully';   
            $response['guild_official'] = $guild_official;   
            
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