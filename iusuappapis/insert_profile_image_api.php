<?php 
  
  //We will upload files to this folder
 //So one thing dont forget, also create a folder named uploads inside your project folder i.e. MyApi folder
 define('UPLOAD_PATH', 'profile_images/');
 require_once 'connection.php'; 
 
 
 
 //An array to display the response
 $response = array();
 
 //if the call is an api call 
 if(isset($_GET['apicall'])){
 
 //switching the api call 
 switch($_GET['apicall']){
 
        //if it is an upload call we will upload the image
        case 'uploadpic':
        
         //getting server ip for building image url 
 $server_ip = gethostbyname(gethostname());
        
        //first confirming that we have the image and tags in the request parameter
        if(isset($_FILES['pic']['name']) && isset($_POST['reg_no'])){
        
        //uploading file and storing it to database as well 
        try{
        move_uploaded_file($_FILES['pic']['tmp_name'], UPLOAD_PATH . $_FILES['pic']['name']);
        $stmt = $conn->prepare("UPDATE student_tb set profile_image=? where reg_no = ?");
        $stmt->bind_param("ss", $_FILES['pic']['name'],$_POST['reg_no']);
        if($stmt->execute()){
            
             $stmt = $conn->prepare("SELECT profile_image FROM student_tb where reg_no=?");   
   
         
            $stmt->bind_param("s",$_POST['reg_no']);  
            $stmt->execute();  
            $stmt->bind_result($profile_image);  
            $stmt->fetch();  
   
            $stmt->close(); 
        $response['error'] = false;
        $response['pic']=$siteurl. UPLOAD_PATH . $profile_image; 
        
        $response['message'] = 'File uploaded successfully';
        }else{
        throw new Exception("Could not upload file");
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
        case 'getpics':
        
        //getting server ip for building image url 
        $server_ip = gethostbyname(gethostname());
        
        //query to get images from database
        $stmt = $conn->prepare("SELECT id, image, tags FROM images");
        $stmt->execute();
        $stmt->bind_result($id, $image, $tags);
        
        $images = array();
        
        //fetching all the images from database
        //and pushing it to array 
        while($stmt->fetch()){
        $temp = array();
        $temp['id'] = $id; 
        $temp['image'] = $siteurl. UPLOAD_PATH . $image; 
        $temp['tags'] = $tags; 
        
        array_push($images, $temp);
        }
        
        //pushing the array in response 
        $response['error'] = false;
        $response['images'] = $images; 
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
 header('Content-Type: application/json');
 echo json_encode($response);
 ?>