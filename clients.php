<?php
    // KASOWANIE UŻYTKOWNIKA z bazy
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
    Klienci:<br />
    <div class="col-sm-8 col-md-8" style="margin:5px; border-style: solid;">
        <?php
            session_start();
            $connect = mysqli_connect('localhost', 'root', '', 'cart');
            $sql = "SELECT id, imie, nazwisko, email, adres, adres2, uwagi, cokupiono, totalsum, totalquantity FROM klient";
            $result = $connect->query($sql);
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
        ?>
                    <div style="margin:10px; padding: 5px; border-style: solid;">
        <?php
                        echo "<br /><b>Numer zamówienia: </b>" . $row["id"]. "<br /><b> Imie: </b>" . $row["imie"]. "<br /><b>Nazwisko: </b>" . $row["nazwisko"]. "<br /><b>E-mail: </b>" . $row["email"]. "<br /><b>Adres: </b>" . $row["adres"]. "<br /><b>Adres 2: </b>" . $row["adres2"]. "<br /><b>Uwagi: </b>" . $row["uwagi"]. "<br /><b>Co kupiono: </b>" . $row["cokupiono"]. "<br /><b>Suma wydatków: </b>" . $row["totalsum"]. " zł" . "<br /><b>Ilość prodkuktów: </b>" . $row["totalquantity"] . " sztuk";
        ?>
                        <form method="post" action="index.php?action=add&id=<?php echo $product['id']; ?>">
                            <a href="clients.php?action=deleteuser&id=<?php echo $row['id']; ?>">
                                <div class="btn-danger">Wypierdol</div>
                            </a>
                        </form>
                    </div>
        <?php
                }
                } else {
                    echo "0 klientów";
                }
        ?>
    </div>
    </body>
</html>