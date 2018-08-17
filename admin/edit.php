<?php
    session_start();
    include_once "../config/config.php";
    $cookie = filter_input(INPUT_COOKIE, "user");
    if(!isset($_SESSION['username']) || empty($_SESSION['username']) || !isset($cookie)){
         echo "Najpierw musisz się zalogować.";
         echo "<script>setTimeout(\"location.href = 'admin.php';\",1500);</script>";
         die();
    }
    $nazwa = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'nazwa'));
    $opis = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'opis'));
    $cena = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'cena'));
    $obrazek = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'obrazek'));      
    $id = filter_input(INPUT_GET, 'id');
    if(filter_input(INPUT_POST, "dodaj")){
        if( empty($nazwa) || empty($cena) || empty($obrazek)) {
            echo "Wypełnij wszystkie pola (opis nieobowiązkowy). Wracam do poprzedniej strony.";
            echo "<script>setTimeout(\"location.href = 'cart.php';\",1500);</script>";
            exit;
        }
        $sql = "UPDATE products SET name=?, opis=?, price=?, image=? WHERE id=?";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)) {
            echo "SQL Statement Error. Skontaktuj się z administratorem.";
        } else {
            mysqli_stmt_bind_param($stmt, "ssssi", $nazwa, $opis, $cena, $obrazek, $id);
            if(!mysqli_stmt_execute($stmt)) {
                echo "SQL Statement execute error. Skontaktuj się z administratorem.";
            }
            mysqli_stmt_close($stmt);
            echo "Zaktualizowano produkt. Wracam do poprzedniej strony.";
            echo "<script>setTimeout(\"location.href = 'products.php';\",1500);</script>";
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
        <div class="container">
             <?php
                
                $query = "SELECT * FROM products ORDER by id ASC";
                $stmt = mysqli_stmt_init($conn);
                if(!mysqli_stmt_prepare($stmt, $query)) {
                    echo "<h1>Brak produktów.</h1>";
                    exit();
                } else {
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                }

                if ('result'):
                    if (mysqli_num_rows($result) > 0):  // mysqli_num_rows - Zwróć liczbę wierszy w zestawie wyników
                        $product = mysqli_fetch_assoc($result);
                        if ($product){ // mysqli_fetch_assoc - Pobierz wiersz wyniku jako tablicę w pętli dla każdego id
                        ?>
                            <form method="post" action="edit.php?action=edit&id=<?php echo $product['id']; ?>" enctype="multipart/form-data">
                                <label>Nazwa towaru</label><br/><input class="form-control form-control-sm" type="text" name="nazwa" value="<?php echo htmlspecialchars(stripslashes($product['name'])); ?>"><br/>
                                <label>Opis towaru, można używać tagów html</label><br/><textarea class="form-control form-control-sm" type="text" name="opis" maxlength="500"><?php echo htmlspecialchars(stripslashes($product['opis'])); ?></textarea><br/>
                                <label>Obrazek (../img/main/nazwaobrazka.jpg)</label></br><input class="form-control form-control-sm" type="text" name="obrazek" value="<?php echo htmlspecialchars($product['image']); ?>"><br/><br/>
                                <label>Cena</label></br><input class="form-control form-control-sm" type="number" min="0" step="0.01" name="cena" value="<?php echo htmlspecialchars($product['price']); ?>"><br/><br/>
                                <input type="submit" name="dodaj" class="btn btn-primary" value="Zapisz">
                                <a href="products.php" class="btn btn-primary">Wróć</a>
                            </form>
                        <?php
                        }
                    endif;
                endif;
                ?>
        </div>
    </body>
</html>