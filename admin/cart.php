<?php
    session_start();
    include_once "../config/config.php";
    $cookie = filter_input(INPUT_COOKIE, "user");
    if(!isset($_SESSION['username']) || empty($_SESSION['username']) || !isset($cookie)){
         echo "Najpierw musisz się zalogować.";
         echo "<script>setTimeout(\"location.href = 'admin.php';\",1500);</script>";
         die();
    }
    if(filter_input(INPUT_GET, "file")){
        $filename = filter_input(INPUT_GET, "file");
        echo("Usunięto plik.");
        unlink($filename);
    }
    
    if(filter_input(INPUT_POST, "logout")){
        // Unset all of the session variables
        $_SESSION = array();
 
        // Destroy the session.
        session_destroy();
 
        // Redirect to login page
        header("location: admin.php");
        exit;
    }
    if(filter_input(INPUT_POST, "dodaj")){
        
        $nazwa = mysqli_real_escape_string($conn, filter_input(INPUT_POST, "nazwa"));
        $opis = mysqli_real_escape_string($conn, filter_input(INPUT_POST, "opis"));
        $cena = mysqli_real_escape_string($conn, filter_input(INPUT_POST, "cena"));
        $obrazek = mysqli_real_escape_string($conn, $directory . filter_input(INPUT_POST, "obrazek"));
        
        if( empty($nazwa) || empty($cena) || empty($obrazek)) {
            echo "Wypełnij wszystkie pola (opis nieobowiązkowy). Wracam do poprzedniej strony.";
            echo "<script>setTimeout(\"location.href = 'cart.php';\",1500);</script>";
            exit;
        }

        //Checking connection

        if($conn->connect_error){
         die("Connection failed:" . $conn->connect_error);
        }

        $createtables = "CREATE TABLE products (id int NOT NULL PRIMARY KEY auto_increment, name varchar(100) NOT NULL, opis varchar(300) NOT NULL, price varchar(30) NOT NULL, image varchar(100) NOT NULL);";
        
        if($conn->query($createtables) === FALSE) {
                
        }
        $sql = "INSERT INTO products (name, opis, price, image) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)) {
            echo "SQL Statement Error. Skontaktuj się z administratorem.";
        } else {
            mysqli_stmt_bind_param($stmt, "ssss", $nazwa, $opis, $cena, $obrazek);
            if(!mysqli_stmt_execute($stmt)) {
                echo "SQL Statement execute error. Skontaktuj się z administratorem.";
            }
            mysqli_stmt_close($stmt);
            echo "Dodawanie zakończone. Wracam do poprzedniej strony.";
            echo "<script>setTimeout(\"location.href = 'cart.php';\",1500);</script>";
            exit;
        }
        
    }
?>

<!DOCTYPE HTML>
<html lang="en">

    <head>
        <title><?php echo($title); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link rel="stylesheet" href="cart.css" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    </head>
    <body>
        Zalogowany jako: <?php echo $_SESSION['username']; ?><br>
        <form action="cart.php" method="post" enctype="multipart/form-data" style="margin-top: 10px;">
            <input type="submit" name="logout" class="btn btn-primary" value="Logout" >
        </form>
        
        <div class="container">
            <div class="row" style="background-color: lightgrey; padding: 20px; border-radius: 10px;">
                <div class="col">
                    <form action="cart.php" method="post" enctype="multipart/form-data">
                    <label>Nazwa towaru</label><br/><input class="form-control form-control-sm" type="text" name="nazwa" id="nazwa"><br/>
                    <label>Opis towaru</label><br/><input class="form-control form-control-sm" type="text" name="opis" id="opis"><br/>
                    <label>Obrazek (nazwaobrazka.jpg)</label></br><input class="form-control form-control-sm" type="text" name="obrazek" id="obrazek"><br/><br/>
                    <label>Cena</label></br><input class="form-control form-control-sm" type="number" min="0" step="0.01" name="cena" id="cena"><br/><br/>
                    <input type="submit" name="dodaj" class="btn btn-primary" value="Wyślij">
                    </form>
                </div>
                <div class="col">
                    Lista obrazków:<br>
                    <?php
                    foreach (glob( $directory. "*.jpg") as $filename) {
                    ?>
                        <a href="<?php echo $filename;?>"
                        <img src="<?php echo $filename;?>" style="width:42px;height:42px;border:0;" target="_BLANK">
                        <?php echo $filename; ?></a>
                    <a href="cart.php?file=<?php echo $filename; ?>" class="btn btn-primary btn-xs">Usuń</a><br />
                    <?php              
                    }
                    ?>
                    <?php
                    foreach (glob($directory. "*.jpeg") as $filename) {
                    ?>
                        <a href="<?php echo $filename;?>"
                        <img src="<?php echo $filename;?>" style="width:42px;height:42px;border:0;" target="_BLANK">
                        <?php echo $filename; ?></a>
                    <a href="cart.php?file=<?php echo $filename; ?>" class="btn btn-primary btn-xs">Usuń</a><br />
                    <?php              
                    }
                    ?>
                     <form action="upload.php" method="post" enctype="multipart/form-data" style="margin-top: 10px;">
                         Wgraj obrazek (jpg):<br />
                        <input type="file" name="attachment">
                        <input type="submit" name="submit" class="btn btn-primary" value="Upload" >
                    </form>
                   
                </div>
            </div>
      </div>
            <div class="container" style="background-color: lightgrey; padding: 20px; margin-top: 20px; border-radius: 10px;">
                 <a href="clients.php" class="btn btn-info">Klienci</a>
                 <a href="products.php" class="btn btn-info">Produkty</a>
                 <a href="../index.php" class="btn btn-info" target="_BLANK">Strona główna sklepu</a>
            </div>
    </body>  
</html> 

