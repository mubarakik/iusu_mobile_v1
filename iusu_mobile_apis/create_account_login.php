<?php   
  require_once 'connection.php';
  define('UPLOAD_PATH', 'profileImages/');
  $response = array();  
  if(isset($_GET['apicall'])){  
      switch($_GET['apicall']){  
      case 'create_account':  
        if(isTheseParametersAvailable(array('regNo','firstName','lastName','gender','campus','phone','email','password'))){  
        $regNo = $_POST['regNo'];
        $firstName = $_POST['firstName'];   
        $lastName = $_POST['lastName']; 
        $profileImage='account_placeholder_img.png';
        $gender = $_POST['gender'];   
        $campus = $_POST['campus'];
        $phone = $_POST['phone'];   
        $email = $_POST['email'];
        $password = md5($_POST['password']); 
        

        $stmt = $conn->prepare("SELECT regNo FROM studenttb WHERE regNo = ?");  
    
        $stmt->bind_param("s", $email);  
        
        $stmt->execute();  
        $stmt->store_result(); 
        
      
        if($stmt->num_rows > 0){  
            $response['error'] = true;  
            $response['message'] = 'Reg no already registered';  
            $stmt->close();  
        }  
        
        else{  
            $stmt->close();
        
            $stmt = $conn->prepare("INSERT INTO studenttb (regNo,firstName,lastName,profileImage,gender,campus,phone, email,password) VALUES (?, ?, ?,?, ?,?,?,?,?)");  
            
            //$stmt = $conn->prepare("CALL student_procedure('insert',?,?,?,?,?,?,?,?,?,null); ");   
              
            $stmt->bind_param("sssssssss", $regNo, $firstName, $lastName, $profileImage,$gender, $campus, $phone, $email, $password);  
      
            // confusing why are there two ids?
            // 
            if($stmt->execute()){  
                $stmt = $conn->prepare("SELECT reg_no,first_name,last_name,profile_image,gender,campus,phone,email FROM student_tb  WHERE reg_no = ?");   
                $stmt->bind_param("s",$regNo);  
                $stmt->execute();  
                $stmt->bind_result( $regNo, $firstName,$lastName, $profileImage,$gender,$campus,$phone, $email);  
                $stmt->fetch();  
      
                $student = array(  
                'regNo'=>$regNo,   
                'firstName'=>$firstName,   
                'lastName'=>$lastName, 
                'profileImage'=>$siteurl. UPLOAD_PATH . $profileImage,
                'gender'=>$gender,
                'campus'=>$campus,   
                'phone'=>$phone,   
                'email'=>$email
                );  
      
                $stmt->close();  
      
                $response['error'] = false;   
                $response['message'] = 'Account created successfully';   
                $response['student'] = $student;   
            }  
        } 

      
      }else{  
        $response['error'] = true;   
      $response['message'] = 'required parameters are not available'; 
    }  
    break;   
    case 'login':  
      if(isTheseParametersAvailable(array('email', 'password'))){  
        $email = $_POST['email'];  
        $password = md5($_POST['password']);    
      
        $stmt = $conn->prepare("SELECT reg_no,first_name,last_name,profile_image,gender,campus,phone,email FROM student_tb  WHERE reg_no = ?  AND password=?");
      
        $stmt->bind_param("ss",$email, $password);  
        $stmt->execute();  
        $stmt->store_result();  
        if($stmt->num_rows > 0){  
          
        $stmt->bind_result($regNo, $regNo, $firstName, $lastName,$profileImage, $gender,$campus,$phone, $email);  
        $stmt->fetch();  
        $student = array(  
          'regNo'=>$regNo,   
          'firstName'=>$firstName,   
          'lastName'=>$lastName,  
          'profileImage'=>$siteurl. UPLOAD_PATH . $profileImage,
          'gender'=>$gender,
          'campus'=>$campus,   
          'phone'=>$phone,   
          'email'=>$email,
                      
            ); 
      
        $response['error'] = false;   
        $response['message'] = 'Login successfull';   
        $response['student'] = $student;   
    }  
    else{  
        $response['error'] = false;   
        $response['message'] = 'Invalid regNo or password';  
    }  
    }  
    break;   

    case 'update_profile':
        
        if(isset($_FILES['profileImage']['name']) && isTheseParametersAvailable(array('regNo','firstName','lastName','gender','campus','phone','email'))){  
        $regNo = $_POST['regNo'];
        $firstName = $_POST['firstName'];   
        $lastName = $_POST['lastName']; 
        $profileImage=$_FILES['profileImage']['name'];
        $gender = $_POST['gender'];   
        $campus = $_POST['campus'];
        $phone = $_POST['phone'];   
        $email = $_POST['email'];
        
        

        $stmt = $conn->prepare("SELECT reg_no FROM student_tb WHERE reg_no = ?");  
    
        $stmt->bind_param("s", $regNo);  
        
        $stmt->execute();  
        $stmt->store_result(); 
        
      
        if($stmt->num_rows < 0){  
            $response['error'] = true;  
            $response['message'] = 'User does not exit';  
            $stmt->close();  
        }  
        
        else{  
            $stmt->close();

        move_uploaded_file($_FILES['profileImage']['tmp_name'], UPLOAD_PATH . $_FILES['profileImage']['name']);
        $stmt = $conn->prepare("UPDATE student_tb SET first_name=?,last_name=?,profile_image=?,gender=?,campus=?,phone=?, email=? where reg_no=?");  
            
            $stmt->bind_param("ssssssss", $firstName, $lastName, $profileImage,$gender, $campus, $phone, $email,$regNo);  
      
            // confusing why are there two ids?
            // 
            if($stmt->execute()){  
                $stmt = $conn->prepare("SELECT reg_no,first_name,last_name,profile_image,gender,campus,phone,email FROM student_tb WHERE reg_no = ?");   
      
                $stmt->bind_param("s",$regNo);  
                $stmt->execute();  
                $stmt->bind_result($regNo, $firstName,$lastName, $profileImage,$gender,$campus,$phone, $email);  
                $stmt->fetch();  
      
                $student = array(  
                'regNo'=>$regNo,   
                'firstName'=>$firstName,   
                'lastName'=>$lastName, 
                'profileImage'=>$siteurl. UPLOAD_PATH . $profileImage,
                'gender'=>$gender,
                'campus'=>$campus,   
                'phone'=>$phone,   
                'email'=>$email);  
      
                $stmt->close();  
      
                $response['error'] = false;   
                $response['message'] = 'profile updated successfully';   
                $response['student'] = $student;   
            }  
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