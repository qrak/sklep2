<?php
    
    // your page title
    $title = "Kościey";
    // site key & secret key configuration for google recaptcha
    $dataKey = "6Ld4FF4UAAAAAHAAeSN1Cto5Wk8gVbHDv90hBJcm";
    $secretKey = "6Ld4FF4UAAAAAA5Z06Z3qA054GoGxEFs_KNl9lct";
    // your bank account number and additional information used by customer to pay for your products
    $bankaccountnumber = array(
        "", // account number
        "", // your first name and last name
        "", // addres 1
        "" //addres 2
    );
    
    // shop email address
    $myemail = '';
    $myname = 'Kosciey Sklep';
    // data for your email account
    $hostname = '';
    $myemaillogin = "";
    $myemailpassword = "";
    
    
    // products image directory
    $directory = "../img/main/";
    
    // database configuration
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', '');
    define('DB_PASSWORD', '');
    define('DB_DATABASE', '');
    $create = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);
    // Check connection
    if ($create->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    // Create database
    $sql = "CREATE DATABASE cart";
    if ($create->query($sql) === TRUE) {
        echo "Database created successfully";
    }

    $conn = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);

?>