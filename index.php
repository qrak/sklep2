<?php
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
        unset($_SESSION['shopping_cart']);
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
</script>

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
        
            <div class="col-md-auto">
                <a href="#" id="koszykbutton" class="btn btn-info" onclick="toggle_visibility('foo');">Koszyk</a>
            </div>
            <div class="container" id="foo" style="display: none;">
                <div class="row justify-content-center">

                    <div class="col">
                        <div class="panel">
                            <div class="content">
                                <div style="clear:both"></div>  
                                    <br />  
                                    <div class="table-responsive">  
                                    <table class="table">  
                                        <tr><th colspan="5"><h3>Szczegóły zamówienia</h3></th></tr>   
                                    <tr>  
                                         <th width="40%">Nazwa produktu</th>  
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

                                           <a href="index.php?action=delete&id=<?php echo $product['id']; ?>">
                                                <div class="btn-danger">Usuń</div>
                                           </a>
                                       </td>  
                                    </tr>  
                                    <?php  
                                              $total = $total + ($product['quantity'] * $product['price']);  
                                         endforeach;  
                                    ?>  
                                    <tr>  
                                         <td colspan="3" align="right">Suma</td>  
                                         <td align="right"><?php echo number_format($total, 2); ?> zł</td>  
                                         <td></td>  
                                    </tr>  
                                    <tr>
                                        <!-- Show checkout button only if the shopping cart is not empty -->
                                        <td colspan="5">
                                         <?php 
                                            if (isset($_SESSION['shopping_cart'])):
                                            if (count($_SESSION['shopping_cart']) > 0):
                                         ?>
                                            <a href="checkout.php" class="button">Do kasy</a>
                                            <a href="index.php?action=deleteall">
                                                <div class="btn-danger">Usuń wszystkie</div>
                                           </a>
                                         <?php endif; endif; ?>
                                        </td>
                                    </tr>
                                    <?php  
                                    endif;
                                    ?>  
                                    </table>  
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="container">
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
                            <div class="col-sm-6 col-md-6">
                                <form method="post" action="index.php?action=add&id=<?php echo $product['id']; ?>">
                                    <div class="products">
                                        <img src="<?php echo $product['image']; ?>" class="img-responsive"  style="max-width: 255px; max-height: 255px;"/>
                                        <h4 class="text-info"><?php echo $product['name']; ?></h4>
                                        <h4><?php echo $product['price']; ?> zł</h4>
                                        <input type ="text" name="quantity" class="form-control" value="1" />
                                        <input type="hidden" name="name" value="<?php echo $product['name']; ?>" />
                                        <input type="hidden" name="price" value="<?php echo $product['price']; ?>" />
                                        <input type="submit" name="add_to_cart" onclick="alert('Dodano do koszyka');" class="btn btn-info" id="btn-id" value="dodaj do koszyka" data-loading-text="Dodano" style="margin-top: 5px;"/>
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
    

         <!-- Optional JavaScript -->
         
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
    
    <script>
$(document).ready(function(){
    $('[data-toggle="popover"]').popover(); 
});

$(document).ready(function(){
  $('#hideshow').on('click', function(event) {        
     $('.content').toggle('show');
  });
});

function change() // no ';' here
{
    if (this.value==="Rozwiń koszyk") this.value = "Zwiń koszyk";
    else this.value = "Zwiń koszyk";
}
</script>

 </body>
 </html>
