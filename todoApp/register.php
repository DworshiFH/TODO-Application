<?php

require_once "backend/res/config.php";

$usrName = $usrName_err = $usrName_hash = "";
$password = $password_err = "";
$confirm_password = $confirm_password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate Username
    if(empty(trim($_POST["usrName"]))){
        $usrName_err = "Bitte geben Sie einen Username ein.";
    } else{
        $usrName = trim($_POST["usrName"]);
    }

    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Bitte geben Sie ein Passwort ein.";
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Das Passwort muss mindestens 6 Zeichen lang sein.";
    } elseif(strcmp(trim($_POST["password"]), trim($_POST["usrName"])) == 0) {
        $password_err = "Username und Passwort dürfen nicht gleich sein.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Bitte bestätigen Sie Ihr Passwort.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Die Passwörter sind nicht identisch.";
        }
    }

    // Prepare the select statement

    $sql = "SELECT * FROM User WHERE UsrName_hash = ?";

    if($stmt = mysqli_prepare($link, $sql)){

        $usrName_hash = hash("sha256", $usrName, false);

        // Set parameters
        $param_usrName = $usrName_hash;

        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_usrName);

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            // store result
            mysqli_stmt_store_result($stmt);

            if(mysqli_stmt_num_rows($stmt) == 1){
                $usrName_err = "Dieser Username ist bereits in Verwendung.";
            }
        } else{
            echo "Es ist etwas schief gelaufen!";
        }

        // Close statement
        mysqli_stmt_close($stmt);

        // Check input errors before inserting in database
        if(empty($usrName_err) && empty($password_err) && empty($confirm_password_err)){

            $param_usrName = $usrName_hash;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            //insert into database
            $query = "INSERT INTO User (
                                UsrName_hash,
                                UsrPW_hash) 
                              VALUES ('$param_usrName', 
                                      '$param_password')";

            if(!mysqli_query($link, $query)) {

                echo "Es ist ein schwerwiegender Fehler aufgetreten.";
            } else {
                $log1 = "Sie haben sich erfolgreich registriert.";
                sleep(2);
                header("Location: login.php");
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Registrieren</title>

    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="style/todoListStylesheet.css"

</head>

<body>
<div class="wrapper">
    <h2>TODO-Application Registrierung</h2>

    <form class="input_container" method="post">
        <label for="usrName">Username:</label>
        <br>
        <input id="usrName" type="text" name="usrName" class="input_container_element">
        <span class="invalid-feedback"><?php echo $usrName_err; ?></span>
        <br>

        <label for="password">Passwort:</label>
        <br>
        <input id="password" type="password" name="password" value="" class="input_container_element">
        <span class="invalid-feedback"><?php echo $password_err; ?></span>
        <br>

        <label for="confirm_password">Passwort bestätigen:</label>
        <br>
        <input id="confirm_password" type="password" name="confirm_password" value="" class="input_container_element">
        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
        <br>

        <input type="submit" class="button" value="Registrieren">
    </form>
</div>
</body>
</html>