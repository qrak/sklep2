<?php
// Include config file
require_once '../config/config.php';
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
// Processing form data when form is submitted
if(filter_input(INPUT_POST, "login")){
    // Check if username is empty
    if(empty(trim(filter_input(INPUT_POST,"username")))){
        $username_err = 'Wpisz swój login';
    } else{
        $username = trim(filter_input(INPUT_POST,"username"));
    }
    // Check if password is empty
    if(empty(trim(filter_input(INPUT_POST,"password")))){
        $password_err = 'Wpisz swoje hasło.';
    } else{
        $password = trim(filter_input(INPUT_POST,"password"));
    }
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT username, password FROM users WHERE username = ?";
        $stmt = mysqli_stmt_init($conn);
        if(mysqli_stmt_prepare($stmt, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            // Set parameters
            $param_username = $username;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            /* Password is correct, so start a new session and
                            save the username to the session */
                            session_start();
                            $_SESSION['username'] = $username;
                            $cookie_name = "user";
                            $cookie_value = $username;
                            setcookie($cookie_name, $cookie_value, time() + (3600 * 30), "/"); // 3600 = 1 hour
                            header("location: cart.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = 'Złe hasło';
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = 'Podany użytkownik nie istnieje';
                }
            } else{
                echo "Błąd. Skontaktuj się z administratorem.";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }
    // Close connection
    mysqli_close($conn);
}
?>
<!doctype html>
<html lang="en">
    <head>
    <title><?php echo($title); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link rel="stylesheet" href="cart.css" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta charset="UTF-8">
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <link href="signin.css" rel="stylesheet">
    </head>
    <body>
        <div class="wrapper" style="margin: auto; max-width: 50%;">
            <h2>Panel admina</h2>
            <p>Wpisz swoje dane.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="text-center">
                <div class="form-group <?php echo (!empty($username_err)) ? 'błąd' : ''; ?>">
                    <label>Login</label>
                    <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
                    <span class="help-block"><?php echo $username_err; ?></span>
                </div>    
                <div class="form-group <?php echo (!empty($password_err)) ? 'błąd' : ''; ?>">
                    <label>Hasło</label>
                    <input type="password" name="password" class="form-control">
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" name="login" class="btn btn-primary" value="Login">
                    <a href="../index.php" class="btn btn-primary">Zabłądziłem</a>
                </div>
            </form>
        </div>    
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
    </body>
</html>