<?php
    session_start();
    include_once "config/config.php";
    if (empty($_SESSION['shopping_cart'])){
        echo 'Wracam do strony sklepu...';
        echo "<script>setTimeout(\"location.href = 'index.php';\",1500);</script>";
        exit;
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
    <script src='https://www.google.com/recaptcha/api.js'></script>
    </head>
    <body>

        <div class="container">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Nazwa</th>
                <th>Ilość</th>
                <th>Cena</th>
                <th>Suma</th>
                <th>Całkowita suma</th>
              </tr>
            </thead>
            <tbody>
            <?php 
             
            foreach ($_SESSION['shopping_cart'] as $key => $product): ?>
              <tr>
                <td><?php echo $product['name'];?></td>
                <td><?php echo $product['quantity'];?></td>
                <td><?php echo $product['price'];?> zł</td>
                <td><?php echo number_format($product['quantity'] * $product['price'], 2); ?> zł</td>  
              </tr>
            <?php endforeach ?>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <?php
                    $total = 0;
                    foreach($_SESSION['shopping_cart'] as $key => $product):
                        $total = $total + ($product['quantity'] * $product['price']);
                    endforeach
                ?>
                <?php echo number_format($total, 2); ?> zł
            </td>
            </tbody>
            </table>
            <form action="sendmail.php" method="post" enctype="multipart/form-data" style="margin: auto; max-width: 50%;">
                <div class="form-group">
                  <label for="imie">Imie</label>
                  <input type="text" class="form-control" name="imie" placeholder="Wpisz imię" required="">
                </div>
                <div class="form-group">
                  <label for="nazwisko">Nazwisko</label>
                  <input type="text" class="form-control" name="nazwisko" placeholder="Wpisz nazwisko" required="">
                </div>
                <div class="form-group">
                  <label for="email">E-mail</label>
                  <input type="email" class="form-control" name="email" placeholder="Wpisz e-mail" required="">
                </div>
                <div class="form-group">
                  <label for="adres">Adres</label>
                  <input type="text" class="form-control" name="adres" placeholder="Wpisz adres" required="">
                </div>
                <div class="form-group">
                  <label for="adres">Adres 2</label>
                  <input type="text" class="form-control" name="adres2" placeholder="Wpisz adres (nieobowiązkowe)">
                </div>
                <div class="form-group">
                    <label for="uwagi">Uwagi</label>
                    <textarea class="form-control" name="uwagi" rows="3" maxlength="300"></textarea>
                </div>
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="<?php echo $dataKey; ?>" style="margin: auto;"></div>
                </div>
                <input type="submit" name="submit" class="btn btn-primary">
                
            </form>
            
        </div>
    </body>
</html>