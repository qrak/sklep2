<?php
    
    // your page title
    $title = "Kościey";
    // site key & secret key configuration for google recaptcha
    $dataKey = "6Ld4FF4UAAAAAHAAeSN1Cto5Wk8gVbHDv90hBJcm";
    $secretKey = "6Ld4FF4UAAAAAA5Z06Z3qA054GoGxEFs_KNl9lct";
    // your bank account number and additional information used by customer to pay for your products
    $bankaccountnumber = array(
        "50 1020 5558 1111 1389 1450 0019", // account number
        "Piotr Kurnicki",
        "50-539 Wrocław",
        "Jabłeczna 26/26"
    );
    
    // shop email address
    $myemail = 'donqrakko@gmail.com';
    $myname = 'Piotr Kurnicki';
    // data for your email account
    $myemaillogin = "donqrakko@gmail.com";
    $myemailpassword = "rgkcbubeskhepzgr";
    
    // products image directory
    $directory = "../img/main/";
    
    // database configuration
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_DATABASE', 'cart');
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