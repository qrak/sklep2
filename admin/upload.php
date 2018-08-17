<?php
    session_start();
    $cookie = filter_input(INPUT_COOKIE, "user");
    if(!isset($_SESSION['username']) || empty($_SESSION['username']) || !isset($cookie)){
         echo "Najpierw musisz się zalogować.";
         echo "<script>setTimeout(\"location.href = 'admin.php';\",1500);</script>";
         die();
    }
    
    if(filter_input(INPUT_POST, "submit")){
        echo "<pre>";

        $targetFile = "../img/main/" . basename($_FILES['attachment']["name"]);
        $extension = pathinfo($targetFile, PATHINFO_EXTENSION);

    if($_FILES['attachment']['size'] > 500000){
        echo "Plik jest za duży. Max 500kb";
    }
    else if($extension != "jpeg" && $extension != "jpg" && $extension != "png"){
        echo "Tylko pliki o rozszerzeniu jpg, jpeg, png!";
    }
    else if(file_exists($targetFile)){
        echo "Plik już istnieje";
    }
    else if(move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)){
            echo "Plik wrzucony";
                }
    }

?>