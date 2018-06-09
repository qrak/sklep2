
<?php
    if(filter_input(INPUT_POST, "dodaj")){
        
        $nazwa = filter_input(INPUT_POST, 'nazwa');
        $cena = filter_input(INPUT_POST, 'cena');
        $obrazek = filter_input(INPUT_POST, 'obrazek');
        if( empty($nazwa) || empty($cena) || empty($obrazek)) {
            echo "Wypełnij wszystkie pola. Wracam do poprzedniej strony.";
            echo "<script>setTimeout(\"location.href = 'cart.php';\",1500);</script>";
            die();
        }
        $server = "localhost";
        $user = "root";
        $pass = "";
        $dbname = "cart";

        //Creating connection for mysqli

        $conn = new mysqli($server, $user, $pass, $dbname);
        $directory = "img/";
        //Checking connection

        if($conn->connect_error){
         die("Connection failed:" . $conn->connect_error);
        }

        $nazwa = mysqli_real_escape_string($conn, filter_input(INPUT_POST, "nazwa"));
        $cena = mysqli_real_escape_string($conn, filter_input(INPUT_POST, "cena"));
        $obrazek = mysqli_real_escape_string($conn, $directory . filter_input(INPUT_POST, "obrazek"));

        $sql = "INSERT INTO products (name, price, image) VALUES ('$nazwa', '$cena', '$obrazek')";

        if($conn->query($sql) === TRUE){
            echo "Dodawanie zakończone. Wracam do poprzedniej strony.";
            echo "<script>setTimeout(\"location.href = 'cart.php';\",1500);</script>";
            die();
        }
        else
        {
         echo "Błąd" . $sql . "<br/>" . $conn->error;
        }
        $conn->close();
    }
?>

<!DOCTYPE HTML>
<html lang="en">

    <head>
        <title>Sklepik</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link rel="stylesheet" href="cart.css" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    </head>
    <body>

      <div class="container-fluid">
            <div class="row">
                <div class="col-sm-4 col-md-4">
                    <form action="cart.php" method="post" enctype="multipart/form-data">
                    <label>Nazwa towaru</label><br/><input class="form-control form-control-sm" type="text" name="nazwa" id="nazwa"><br/>
                    <label>Obrazek (nazwaobrazka.jpg)</label></br><input class="form-control form-control-sm" type="text" name="obrazek" id="obrazek"><br/><br/>
                    <label>Cena</label></br><input class="form-control form-control-sm" type="number" min="0" step="0.01" name="cena" id="cena"><br/><br/>
                    <input type="submit" name="dodaj" value="Wyślij">
                    </form>
                </div>
                <div class="col-sm-4 col-md-4">
                    Lista obrazków:<br>
                    <?php
                    foreach (glob("img/*.jpg") as $filename) {
                    ?>
                        <a href="<?php echo $filename;?>"
                        <img src="<?php echo $filename;?>" style="width:42px;height:42px;border:0;" target="_BLANK">
                        <?php echo $filename."<br />"; ?></a>
                    <?php              
                    }
                    ?>
                    <?php
                    foreach (glob("img/*.jpeg") as $filename) {
                    ?>
                        <a href="<?php echo $filename;?>"
                        <img src="<?php echo $filename;?>" style="width:42px;height:42px;border:0;" target="_BLANK">
                        <?php echo $filename."<br />"; ?></a>
                    <?php              
                    }
                    ?>
                     <form action="upload.php" method="post" enctype="multipart/form-data" style="margin-top: 10px;">
                         Wgraj obrazek (jpg):<br />
                        <input type="file" name="attachment">
                        <input type="submit" name="submit" value="Upload" >
                    </form>
                   
                </div>
            </div>
      </div>
            <div class="container-fluid" style="margin-top: 40px;">
                 <a href="clients.php" class="btn btn-info" target="_BLANK">Klienci</a>
                 <a href="products.php" class="btn btn-info" target="_BLANK">Produkty</a>
                 <a href="index.php" class="btn btn-info" target="_BLANK">Spierdalaj na strone główną sklepu</a>
            </div>
    </body>  
</html> 

