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
            echo ("Wypierdolono produkt.");
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
                                         <a href="products.php?action=deleteid&id=<?php echo $product['id']; ?>">
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