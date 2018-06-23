<?php
include_once "config/config.php";
session_start();
$product_ids = array();
//session_destroy();
//check if add cart button has been submitted
   
if(filter_input(INPUT_POST, "add_to_cart")){
    if(isset($_SESSION['shopping_cart'])){
        
        //keep track of how many producs are in shopping cart
        $count = count($_SESSION['shopping_cart']);
        
        //create sequential array for matching keys to products ids
        
        //array_column — Return the values from a single column in the input array
        $product_ids = array_column($_SESSION['shopping_cart'], 'id');
        
        //filter_input — Gets a specific external variable by name and optionally filters it
        //in_array — Checks if a value exists in an array
        if(!in_array(filter_input(INPUT_GET, 'id'), $product_ids)){
             $_SESSION['shopping_cart'][$count] = array
                (
                'id' => filter_input(INPUT_GET, 'id'),
                'name' => filter_input(INPUT_POST, 'name'),
                'opis' => filter_input(INPUT_POST, 'opis'),
                'price' => filter_input(INPUT_POST, 'price'),
                'quantity' => filter_input(INPUT_POST, 'quantity')
                );
        }
        else { //product already exists, increase quantity
            //match array key to id of the product being added to the cart
            for ($i = 0; $i < count($product_ids); $i++){
                if($product_ids[$i] == filter_input(INPUT_GET, 'id')){
                    //add item quantity to existing product in the array
                    $_SESSION['shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST, 'quantity');
                }
            }
        }
    }
    else{ //if shopping cart doesnt exists create array with array key 0
        // create array using submitted data, start from key 0 and fill it with values
        $_SESSION['shopping_cart'][0] = array
                (
                'id' => filter_input(INPUT_GET, 'id'),
                'name' => filter_input(INPUT_POST, 'name'),
                'opis' => filter_input(INPUT_POST, 'opis'),
                'price' => filter_input(INPUT_POST, 'price'),
                'quantity' => filter_input(INPUT_POST, 'quantity')
                );
        }

}    
    if(filter_input(INPUT_GET, 'action') == 'delete'){
        //loop through all products in the shopping cart until it matches with GET id variable
        foreach($_SESSION['shopping_cart'] as $key => $product){
            if($product['id'] == filter_input(INPUT_GET, 'id')){
                //remove product from the shopping cart when it matches with the GET id
                unset($_SESSION['shopping_cart'][$key]);
            }
        }
        //reset session array keys so they match with $product_ids numeric array
        $_SESSION['shopping_cart'] = array_values($_SESSION['shopping_cart']);
    }
    
     if(filter_input(INPUT_GET, 'action') == 'deleteall'){
         foreach($_SESSION['shopping_cart'] as $key => $product){
             unset($_SESSION['shopping_cart'][$key]);
         }
     }
     
 
?>

<script type="text/javascript">

    function toggle_visibility(id) {
       var e = document.getElementById(id);
       if(e.style.display === 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
    }
    function backtopage() {
        setTimeout(\"location.href = 'index.php';\",1500);
    }
</script>

<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo($title); ?></title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Kaushan+Script' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700' rel='stylesheet' type='text/css'>

    <!-- Custom styles for this template -->
    <link href="css/agency.min.css" rel="stylesheet">

  </head>

  <body id="page-top">

         <!-- Modal -->

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Twój koszyk</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <table class="table">  
                        <tr><th colspan="7"><h3>Szczegóły zamówienia</h3></th></tr>   
                        <tr>  
                            <th width="40%">Nazwa</th>  
                            <th width="10%">Ilość</th>  
                            <th width="20%">Cena</th>  
                            <th width="15%">Suma</th>  
                            <th width="5%">Akcja</th>  
                        </tr>  
                        <?php   
                        if(!empty($_SESSION['shopping_cart'])):  
                            $total = 0;  
                            foreach($_SESSION['shopping_cart'] as $key => $product): 
                        ?>  
                            <tr>  
                                <td><?php echo $product['name']; ?></td>  
                                <td><?php echo $product['quantity']; ?></td>  
                                <td><?php echo $product['price']; ?> zł</td>  
                                <td><?php echo number_format($product['quantity'] * $product['price'], 2); ?> zł</td>  
                                <td>
                                    <a href="index.php?action=delete&id=<?php echo $product['id']; ?>#plyty">
                                        <div class="btn btn-danger btn-sm">Usuń</div>
                                    </a>
                                </td>  
                            </tr>  
                        <?php  
                            $total = $total + ($product['quantity'] * $product['price']);  
                            endforeach;  
                        ?>  
                            <tr>  
                                <td colspan="4" align="right"><b>Razem:</b></td>  
                                <td><b><?php echo number_format($total, 2); ?> zł</b></td>  
                                <td></td>  
                            </tr>  
                            <tr>
                                <!-- Show checkout button only if the shopping cart is not empty -->
                                <td colspan="4">
                        <?php 
                                    if (isset($_SESSION['shopping_cart'])):
                                        if (count($_SESSION['shopping_cart']) > 0):
                        ?>
                                            <a href="checkout.php" class="btn btn-success">Do kasy</a>
                                            <a href="index.php?action=deleteall" class="btn btn-danger">Usuń wszystkie</a>
                        <?php endif; endif; ?>
                                </td>
                            </tr>
                        <?php  
                        endif;
                        ?>  
                    </table>  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav" style="color: #6c757d;">
             

        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          Menu
          <i class="fa fa-bars"></i>
        </button>
                   <?php
              if(isset($_SESSION['shopping_cart'])){
        $total = 0;
        foreach($_SESSION['shopping_cart'] as $key => $product) {
            $total = $total + $product['quantity'];  
        }    
            echo '<div class="item-count">' . $total . '</div>';
    }
    ?>
        <button type="button" data-toggle="modal" data-target="#exampleModal" style="box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.19);">
            <img src="img/cart.png" style="max-width: 64px; max-height: 64px;" style="border-style: none; background-color: transparent;" class="shopping-cart"/>
        </button>
      
      <div class="container">
 
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav text-uppercase ml-auto">
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#plyty">Płyty</a>
            </li>
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#kontakt">Kontakt</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Header -->
    <header class="masthead">
      <div class="container">
        <div class="intro-text">
          <div class="intro-lead-in">Kościey</div>
          <div class="intro-heading text-uppercase">Straight outta gaj.</div>
          <a class="btn btn-primary btn-xl text-uppercase js-scroll-trigger" href="#plyty">Kup płyty</a>
        </div>
      </div>
    </header>

    <!-- Services -->
    <section id="plyty" style="background-image: url('img/vinyl-background.jpg'); background-repeat: no-repeat; background-position: center;">
      <div class="container">
        <div class="row">
           <?php
                $sql = 'SELECT * FROM products ORDER by id ASC';
                //mysqli_query — Performs a query on the database
                $stmt = mysqli_stmt_init($conn);
                if(!mysqli_stmt_prepare($stmt, $sql)) {
                    echo "<h1>Brak produktów.</h1>";
                    exit();
                } else {
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                }
                

                if ('result'):
                    if (mysqli_num_rows($result) > 0):  // mysqli_num_rows - Zwróć liczbę wierszy w zestawie wyników
                        while ($product = mysqli_fetch_assoc($result)): // mysqli_fetch_assoc - Pobierz wiersz wyniku jako tablicę w pętli dla każdego id
                            ?>
                            <div class="col-md-4 text-center">
                                <form method="post" action="index.php?action=add&id=<?php echo $product['id']; ?>#plyty">
                                    <div class="products">
                                        <img src="<?php echo substr($product['image'], 3); ?>" class="img-thumbnail" style="max-width: 100%; max-height: 100%;"/>
                                        <div class="opis">
                                            <h3><?php echo stripslashes($product['name']); ?></h3>
                                            <h6><?php echo stripslashes($product['opis']); ?></h6>
                                            <b><?php echo $product['price']; ?> zł</b>
                                        </div>
                                        <input type ="hidden" name="quantity" class="form-control" value="1" />
                                        <input type="hidden" name="name" value="<?php echo $product['name']; ?>" />
                                        <input type="hidden" name="price" value="<?php echo $product['price']; ?>" />
                                        <div class="container"><input type="submit" name="add_to_cart" class="btn btn-info" id="btn-id" value="Dodaj do koszyka" data-loading-text="Dodano" style="margin-top: 5px;"/></div>
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
        </div>
      </div>
    </section>

    <!-- Portfolio Grid -->
    <section class="bg-light" id="kontakt">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 text-center">
            <h2 class="section-heading text-uppercase">Kontakt</h2>
            <h3 class="section-subheading text-muted"><a href="https://pl-pl.facebook.com/Kosciey/" target="_BLANK">Kosciey</a></h3>
          </div>
        </div>
       
        </div>
    </section>

    <!-- Footer -->
    <footer>
<footer>
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            
          </div>
          <div class="col-md-4">
            <ul class="list-inline social-buttons">
  
              <li class="list-inline-item">
                <span class="copyright">Copyright &copy; Piotr Kurnicki <?php echo date("Y"); ?></span>
              </li>
    
            </ul>
          </div>
          <div class="col-md-4">
            <ul class="list-inline quicklinks">
              <li class="list-inline-item">
                
              </li>
            </ul>
          </div>
        </div>
      </div>
    </footer>

    

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Contact form JavaScript -->
    <script src="js/jqBootstrapValidation.js"></script>
    <script src="js/contact_me.js"></script>

    <!-- Custom scripts for this template -->
    <script src="js/agency.min.js"></script>
    
  </body>

</html>
