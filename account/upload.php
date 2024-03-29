<?php
    $host='localhost';
    $username='root';
    $password='';
    $dbname = "myproject";
    $conn=mysqli_connect($host,$username,$password,"$dbname");
    if(!$conn)
        {
          die('Could not Connect MySql Server:' .mysql_error());
        }
?>

<?php

$statusMsg = '';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Check if file was uploaded without errors
    if(isset($_FILES["anyfile"]) && $_FILES["anyfile"]["error"] == 0){
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["anyfile"]["name"];
        $filetype = $_FILES["anyfile"]["type"];
        $filesize = $_FILES["anyfile"]["size"];
    
        // Validate file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");
    
        // Validate file size - 10MB maximum
        $maxsize = 10 * 1024 * 1024;
        if($filesize > $maxsize) die("Error: File size is larger than the allowed limit.");
    
        // Validate type of the file
        if(in_array($filetype, $allowed)){
            // Check whether file exists before uploading it
            if(file_exists("uploads/" . $filename)){
                $statusMsg = $filename . " is already exists.";
            } else{
                if(move_uploaded_file($_FILES["anyfile"]["tmp_name"], "uploads/" . $filename)){

                    $sql="UPDATE users SET profile_pic = '$filename' WHERE id = '$_SESSION[id]'";
                    
                    mysqli_query($conn,$sql);

                    $statusMsg = "Your file was uploaded successfully.";
                }else{

                    $statusMsg = "File is not uploaded";
                }
                
            } 
        } else{
            $statusMsg = "Error: There was a problem uploading your file. Please try again."; 
        }
    } else{
        $statusMsg = "Error: " . $_FILES["anyfile"]["error"];
    }
}
?>