<?php
    session_start();
    
    $captcha=filter_input(INPUT_POST, 'g-recaptcha-response');;
    if(!filter_input(INPUT_POST, 'g-recaptcha-response')){
        echo '<h4>Wypełnij captcha.</h4>';
        exit;
    }
    $secretKey = "6Ld4FF4UAAAAAA5Z06Z3qA054GoGxEFs_KNl9lct";
    $ip = filter_input(INPUT_SERVER, '@_SERVER');
    $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
    $responseKeys = json_decode($response,true);
    if(intval($responseKeys["success"]) !== 1) {
        echo '<h4>Źle wypełnione captcha.</h4>';
        exit;
    }
    use PHPMailer\PHPMailer\PHPMailer;
    require 'vendor/autoload.php';
    
    $server = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "cart";
    $connect = new mysqli($server, $user, $pass, $dbname);
    
    if(filter_input(INPUT_POST, "submit")){
        $imie = mysqli_real_escape_string($connect, filter_input(INPUT_POST, "imie"));
        $nazwisko = mysqli_real_escape_string($connect, filter_input(INPUT_POST, "nazwisko"));
        $email = mysqli_real_escape_string($connect, filter_input(INPUT_POST, "email"));
        $adres = mysqli_real_escape_string($connect, filter_input(INPUT_POST, "adres"));
        $adres2 = mysqli_real_escape_string($connect, filter_input(INPUT_POST, "adres2"));
        $uwagi = mysqli_real_escape_string($connect, filter_input(INPUT_POST, "uwagi"));
        $error_message;
        $cokupiono = array();
        foreach ($_SESSION['shopping_cart'] as $key => $product) {
            array_push($cokupiono, 'Nazwa: ' . $product['name'], 'Ilość: ' . $product['quantity'], 'Cena za sztuke: '. $product['price'] . ' zł');
        }

        $total = 0;
        $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
        if(!preg_match($email_exp,$email)) {
            $error_message .= 'Zły e-mail.<br />';
        }
        if(strlen($adres) < 2) {
            $error_message .= 'Zły adres.<br />';
        }
    $email_message = "Dane z formularza:\n\n<br/>";
    function clean_string($string) {
        $bad = array("content-type","bcc:","to:","cc:","href");
        return str_replace($bad,"",$string);
    }

    if($connect->connect_error){
        die("Connection failed:" . $conn->connect_error);
    }
    $totalsum = 0;
    foreach($_SESSION['shopping_cart'] as $key => $product) {
        $totalsum = $totalsum + ($product['quantity'] * $product['price']);
    }
    $totalquantity = 0;
    foreach($_SESSION['shopping_cart'] as $key => $product) {
        $totalquantity = $totalquantity + ($product['quantity']);
    }
    
    $konwertuj = implode(" ||| ", $cokupiono);
    $createtables = "CREATE TABLE klient (id int(6) NOT NULL auto_increment, imie varchar(15) NOT NULL, nazwisko varchar(15) NOT NULL, email varchar(30) NOT NULL, adres varchar(30) NOT NULL, adres2 varchar(30) NOT NULL, uwagi varchar(300) NOT NULL, cokupiono varchar(5000) NOT NULL, totalsum varchar(100) NOT NULL, totalquantity varchar(100) NOT NULL, PRIMARY KEY (id))";
    $insertvalues = "INSERT INTO klient (imie, nazwisko, email, adres, adres2, uwagi, cokupiono, totalsum, totalquantity) VALUES ('$imie', '$nazwisko', '$email', '$adres', '$adres2', '$uwagi', '$konwertuj', '$totalsum', '$totalquantity' )";
    

    $connect->query($createtables);
    if($connect->query($insertvalues) === FALSE) {
        echo "Error: " . $insertvalues . "<br>" . $connect->error;
    }
    $id = mysqli_insert_id($connect);


//Create a new PHPMailer instance
$mail = new PHPMailer;
//Tell PHPMailer to use SMTP
$mail->isSMTP();
$mail->CharSet = 'UTF-8';
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 0;
//Set the hostname of the mail server
$mail->Host = 'smtp.gmail.com';
// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6
//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 587;
//Set the encryption system to use - ssl (deprecated) or tls
$mail->SMTPSecure = 'tls';
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = "donqrakko@gmail.com";
//Password to use for SMTP authentication
$mail->Password = "rgkcbubeskhepzgr";
//Set who the message is to be sent from
$mail->setFrom('donqrakko@gmail.com', 'Piotr Kurnicki');
//Set an alternative reply-to address
$mail->addReplyTo('donqrakko@gmail.com', 'Piotr Kurnicki');
//Set who the message is to be sent to
$mail->addAddress($email);
//Set the subject line
$mail->Subject = 'Nowe zamówienie numer ' . $id . ' z kurakowego sklepu.';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->isHTML(true);





$mail->Body = 'Złożyłeś nowe zamówienie w sklepie kurakowym. Oto Twoje dane:<br/><br/>' .
            'Numer zamówienia: ' . $id . '<br />' .
            'Imie: ' . $imie . '<br />' .
            'Nazwisko: ' . $nazwisko . '<br />' .
            'E-mail: ' . $email . '<br />' .
            'Adres: ' . $adres . '<br />' .
            'Adres 2: ' . $adres2 . '<br />' .
            'Uwagi: ' . $uwagi . '<br />' .
            'Co kupiono: <br /><b>' . implode("<br /> ", $cokupiono) . '</b><br />' .
            'Łączna ilość przedmiotów: ' . $totalquantity . ' sztuk<br />'.
            'Do zapłacenia: ' . $totalsum . ' zł';


//Replace the plain text body with one created manually
$mail->AltBody = '';
//Attach an image file
$mail->setLanguage('pl', '/optional/path/to/language/directory/');
//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {

    //Section 2: IMAP
    //Uncomment these to save your message in the 'Sent Mail' folder.
    #if (save_mail($mail)) {
    #    echo "Message saved!";
    #}
}
//Section 2: IMAP
//IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
//Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
//You can use imap_getmailboxes($imapStream, '/imap/ssl') to get a list of available folders or labels, this can
//be useful if you are trying to get this working on a non-Gmail IMAP server.
    }
function save_mail($mail)
{
    //You can change 'Sent Mail' to any other folder or tag
    $path = "{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail";
    //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
    $imapStream = imap_open($path, $mail->Username, $mail->Password);
    $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
    imap_close($imapStream);
    return $result;
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>Sklepik kurakowy</title>
</head>
<body>
<div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
  <h1>Zamówienie złożone! Dalsze szczegóły otrzymasz na swoją skrzynkę e-mail którą podałeś/aś w zamówieniu.</h1>
  <div align="center">
    <a href="https://github.com/PHPMailer/PHPMailer/"><img src="img/phpmailer_mini.png" height="90" width="340" alt="PHPMailer rocks"></a>
  </div>
  <p>Gratuluje!</p>
  <p></p>
</div>
</body>
</html>