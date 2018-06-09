<?php  
    // kasowanie dodanego rekordu w sklepie
    if(filter_input(INPUT_GET, 'action') == 'deleteid'){
        if (isset($_GET['id']) && is_numeric($_GET['id'])){
            // get id value
            $connect = mysqli_connect('localhost', 'root', '', 'cart');
            $id = $_GET['id'];
            $query = "DELETE FROM products WHERE id=$id";
            // delete the entry
            $result = mysqli_query($connect, $query);
        }
    }
    if(filter_input(INPUT_GET, 'action') == 'deleteuser'){
        if (isset($_GET['id']) && is_numeric($_GET['id'])){
            // get id value
            $connect = mysqli_connect('localhost', 'root', '', 'cart');
            $id = $_GET['id'];
            $query = "DELETE FROM klient WHERE id=$id";
            // delete the entry
            $result = mysqli_query($connect, $query);
        }
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

      <div class="container">
            <div class="row">
                <div class="col-sm-4 col-md-4">
                    <form action="insert.php" method="post">
                    <label>Nazwa towaru</label><br/><input class="form-control form-control-sm" type="text" name="nazwa" id="nazwa"><br/>
                    <label>Obrazek (nazwaobrazka.jpg)</label></br><input class="form-control form-control-sm" type="text" name="obrazek" id="obrazek"><br/><br/>
                    <label>Cena</label></br><input class="form-control form-control-sm" type="number" min="0" step="0.01" name="cena" id="cena"><br/><br/>
                    <input type="submit" value="Wyślij">
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
                <a href="clients.php" class="btn btn-info" target="_BLANK">Wyświetl klientów</a>
            </div>
      </div>
            <div class="container-fluid" style="margin-top: 40px;">
            <div class="row">
          <?php
                $connect = mysqli_connect('localhost', 'root', '', 'cart');
                $query = 'SELECT * FROM products ORDER by id ASC';
                //mysqli_query — Performs a query on the database
                $result = mysqli_query($connect, $query);

                if ('result'):
                    if (mysqli_num_rows($result) > 0):  // mysqli_num_rows - Zwróć liczbę wierszy w zestawie wyników
                        while ($product = mysqli_fetch_assoc($result)): // mysqli_fetch_assoc - Pobierz wiersz wyniku jako tablicę w pętli dla każdego id
                            ?>
                            <div class="col-sm-4 col-md-4" style="margin:5px;">
                                <form method="post" action="index.php?action=add&id=<?php echo $product['id']; ?>">
                                    <div class="products">
                                        <img src="<?php echo $product['image']; ?>" class="img-responsive" style="max-width: 255px; max-height: 255px;"/>
                                        <h4 class="text-info"><?php echo $product['name']; ?></h4>
                                        <h4><?php echo $product['price']; ?> zł</h4>
                                        <input type ="text" name="quantity" class="form-control" value="1" />
                                        <input type="hidden" name="name" value="<?php echo $product['name']; ?>" />
                                        <input type="hidden" name="price" value="<?php echo $product['price']; ?>" />
                                         <a href="cart.php?action=deleteid&id=<?php echo $product['id']; ?>">
                                                <div class="btn-danger">Usuń</div>
                                           </a>
                                    </div>
                                </form>
                            </div>
                            <?php
                        endwhile;
                    endif;
                endif;
                ?>
            </div>
            </div>
    </body>  
</html> 

  