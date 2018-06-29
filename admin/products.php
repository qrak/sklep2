<?php  
    session_start();
    include_once "../config/config.php";
    $cookie = filter_input(INPUT_COOKIE, "user");
    if(!isset($_SESSION['username']) || empty($_SESSION['username']) || !isset($cookie)){
         echo "Najpierw musisz się zalogować.";
         echo "<script>setTimeout(\"location.href = 'admin.php';\",1500);</script>";
         die();
    }
    // kasowanie dodanego rekordu w sklepie
    if(filter_input(INPUT_GET, 'action') == 'deleteid'){
        if (isset($_GET['id']) && is_numeric($_GET['id'])){
            $id = $_GET['id'];
            $query = "DELETE FROM products WHERE id=$id";
            // delete the entry
            $result = mysqli_query($conn, $query);
            echo ("Wypierdolono produkt.");
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
        <div class="container-fluid" style="margin-top: 40px;">
             <div class="row">
              <?php

                    $sql = 'SELECT * FROM products ORDER by id ASC';
                    $stmt = mysqli_stmt_init($conn);
                    if(!mysqli_stmt_prepare($stmt, $sql)) {
                        echo "<h1>Brak produktów.</h1>";
                    } else {
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                    }
                        if ($result->num_rows > 0): 
                            while ($product = $result->fetch_assoc()): // mysqli_fetch_assoc - Pobierz wiersz wyniku jako tablicę w pętli dla każdego id
                                ?>
                                   <div class="col-md-4 col-sm-6 col-xs-6 text-center">
                                    <form method="post">
                                        <div class="products">
                                            <img src="<?php echo $product['image']; ?>" class="img-thumbnail" style="max-width: 100%; max-height: 100%;"/>
                                            <div class="opis">
                                                <h3><?php echo stripslashes($product['name']); ?></h3>
                                                <h6><?php echo stripslashes($product['opis']); ?></h6>
                                                <b><?php echo $product['price']; ?> zł</b>
                                            </div>
                                            <input type ="hidden" name="quantity" class="form-control" value="1" />
                                            <input type="hidden" name="name" value="<?php echo $product['name']; ?>" />
                                            <input type="hidden" name="price" value="<?php echo $product['price']; ?>" />
                                            <a href="products.php?action=deleteid&id=<?php echo $product['id']; ?>" class="btn btn-danger">Usuń</a>
                                            <a href="edit.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-info">Edytuj</a>
                                        </div>
                                    </form>
                                </div>
                                <?php
                            endwhile;
                        endif;

                    ?>
                </div>
                 <a href="cart.php" class="btn btn-primary">Wróć</a>
        </div>
    </body>
</html>