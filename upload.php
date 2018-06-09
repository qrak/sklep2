<?php
    if(filter_input(INPUT_POST, "submit")){
        echo "<pre>";

        $targetFile = "img/" . basename($_FILES['attachment']["name"]);
        $extension = pathinfo($targetFile, PATHINFO_EXTENSION);

    if($_FILES['attachment']['size'] > 300000){
        echo "Plik jest za duży. Max 300kb";
    }
    else if($extension != "jpeg" && $extension != "jpg"){
        echo "Tylko pliki o rozszerzeniu jpg i jpeg!";
    }
    else if(file_exists($targetFile)){
        echo "Plik już istnieje";
    }
    else if(move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)){
            echo "Plik wrzucony";
                }
    }

?>