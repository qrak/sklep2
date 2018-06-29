<?php
    session_start();
    include_once "config/config.php";
    

    if(!filter_input(INPUT_POST, 'submit')){
        echo '<h4>Wracam na strone sklepu...</h4>';
        echo "<script>setTimeout(\"location.href = 'index.php';\",1500);</script>";
        exit;
    }

    $captcha = filter_input(INPUT_POST, 'g-recaptcha-response');
    if(!filter_input(INPUT_POST, 'g-recaptcha-response')){
        echo '<h4>Wypełnij captcha.</h4>';
        exit;
    }

        $url = 'https://www.google.com/recaptcha/api/siteverify';
	$data = array(
		'secret' => $secretKey,
		'response' => $_POST["g-recaptcha-response"]
	);
	$options = array(
		'http' => array (
			'method' => 'POST',
			'content' => http_build_query($data)
		)
	);
	$context  = stream_context_create($options);
	$verify = file_get_contents($url, false, $context);
	$captcha_success=json_decode($verify);
	if ($captcha_success->success==false) {
		echo "<p>Jesteś botem, won!</p>";
                exit();
	}
    use PHPMailer\PHPMailer\PHPMailer;
    require 'vendor/autoload.php';
    
    if(filter_input(INPUT_POST, "submit")){
        $imie = mysqli_real_escape_string($conn, filter_input(INPUT_POST, "imie"));
        $nazwisko = mysqli_real_escape_string($conn, filter_input(INPUT_POST, "nazwisko"));
        $email = mysqli_real_escape_string($conn, filter_input(INPUT_POST, "email"));
        $adres = mysqli_real_escape_string($conn, filter_input(INPUT_POST, "adres"));
        $adres2 = mysqli_real_escape_string($conn, filter_input(INPUT_POST, "adres2"));
        $uwagi = mysqli_real_escape_string($conn, filter_input(INPUT_POST, "uwagi"));
        $error_message;
        $cokupiono = array();
        if(empty($imie) || empty($nazwisko) || empty($email) || empty($adres)){
            echo 'Wypełnij wszystkie pola.';
            exit;
        }
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

    if($conn->connect_error){
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
    
    $conn->query($createtables);
    $insertvalues = "INSERT INTO klient (imie, nazwisko, email, adres, adres2, uwagi, cokupiono, totalsum, totalquantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    //create prepared statement
    $stmt = mysqli_stmt_init($conn);
    //prepare statement
    if(!mysqli_stmt_prepare($stmt, $insertvalues)) {
        echo "SQL Statement Error. Skontaktuj się z administratorem.";
    } else {
        //bind paremeters
        mysqli_stmt_bind_param($stmt, "sssssssss", $imie, $nazwisko, $email, $adres, $adres2, $uwagi, $konwertuj, $totalsum, $totalquantity);
        //execute prepared statement
        if(!mysqli_stmt_execute($stmt)) {
            echo "SQL Statement execute error. Skontaktuj się z administratorem.";
        }
        mysqli_stmt_close($stmt);
    }
        $id = mysqli_insert_id($conn);
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
        $mail->Host = $hostname;
        // use
        // $mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = 465;
        //Set the encryption system to use - ssl (deprecated) or tls
        $mail->SMTPSecure = 'tls';
        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = $myemaillogin;
        //Password to use for SMTP authentication
        $mail->Password = $myemailpassword;
        //Set who the message is to be sent from
        $mail->setFrom($myemail, $myname);
        //Set an alternative reply-to address
        $mail->addReplyTo($myemail, $myname);
        //Set who the message is to be sent to
        $mail->addAddress($email);
        //Set the subject line
        $mail->Subject = 'Nowe zamówienie numer ' . $id . ' ze sklepu.';
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $mail->isHTML(true);
        $mail->Body = 'Złożyłeś nowe zamówienie w sklepie. Oto Twoje dane:<br/><br/>' .
                    'Numer zamówienia: ' . $id . '<br />' .
                    'Imie: ' . $imie . '<br />' .
                    'Nazwisko: ' . $nazwisko . '<br />' .
                    'E-mail: ' . $email . '<br />' .
                    'Adres: ' . $adres . '<br />' .
                    'Adres 2: ' . $adres2 . '<br />' .
                    'Uwagi: ' . $uwagi . '<br />' .
                    'Co kupiono: <br /><b>' . implode("<br /> ", $cokupiono) . '</b><br />' .
                    'Łączna ilość przedmiotów: ' . $totalquantity . ' sztuk<br />'.
                    'Do zapłacenia: ' . $totalsum . ' zł' . '<br />' .
                    'Dane do przelewu: <br />' .
                    'Numer konta: ' . $bankaccountnumber[0] . '<br />' .
                    'Imie i nazwisko: ' . $bankaccountnumber[1] . '<br />' .
                    'Adres: ' . $bankaccountnumber[2] . '<br />' .
                    'Adres 2: '. $bankaccountnumber[3] . '<br />' .
                    'Tytuł: Opłata za zamówienie nr ' . $id;
        //Replace the plain text body with one created manually
        $mail->AltBody = '';
        //Attach an image file
        $mail->setLanguage('pl', '/optional/path/to/language/directory/');
        //send the message, check for errors
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } 
        $mail->ClearAllRecipients();
        $mail->Subject = 'Masz nowe zamówienie do realizacji w Twoim sklepie.';
        $mail->addAddress($myemail);
        $mail->Body = 'Nowe zamówienie w Twoim sklepie, oto dane kupującego. Pamiętaj, że zarządzać klientami możesz w panelu administracyjnym.<br/><br/>' .
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
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } 
        session_destroy();
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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo($title); ?></title>
</head>
<body>
    <div class="container">
        <div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
            <h2>Zamówienie złożone! Dalsze szczegóły otrzymasz na swoją skrzynkę e-mail którą podałeś/aś w zamówieniu.</h2>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
    
</body>
</html>
