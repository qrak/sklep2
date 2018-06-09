<?php
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
 echo "Dodawanie zakończone";
}
else
{
 echo "Błąd" . $sql . "<br/>" . $conn->error;
}
$conn->close();
?>
