<?php

session_start();
if (isset($_SESSION['user_id']) != "") {
    header("Location: todoListJS.html");
}

if (isset($_POST['login'])) {

    $usrName  = $_POST['usrName'];
    $usrName_hash = hash("sha256", $usrName, false);

    $password = $_POST['password'];
    if (empty($usrName)) {
        $usrName_error = "Bitte geben Sie einen Username ein.";
    }
    if (strlen($password) < 6) {
        $password_error = "Das Passwort muss mindestens 6 Zeichen lang sein.";
    }

    require_once "backend/res/config.php";

    $result = mysqli_query($link, "SELECT * FROM User WHERE UsrName_hash = '$usrName_hash'");
    if ($row = mysqli_fetch_array($result)) {
        //validate Password

        $hashed_password = $row["UsrPW_hash"];

        if(password_verify($password, $hashed_password)){

            $_SESSION['user_id'] = $usrName_hash;
            if( $_POST["login"] == "Anmelden und die JavaScript-Version verwenden" ){
                header("Location: todoListJS.php");

            } elseif ( $_POST["login"] == "Anmelden und die PyScript-Version verwenden" ){
                header("Location: todoListPy.php");
            }

        }
    } else {
        $error_message = "Falscher Username oder Passwort";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Login</title>
    <link rel="stylesheet" href="style/todoListStylesheet.css"
</head>
<body>

<h2 id = "header">TODO-Application Anmeldung</h2>

<form id = "input_container" name="input" action="" method="post">
    <label for="usrName">Username:</label>
    <br>
    <input class="input_container_element" type="text" value="<?= $_POST["usrName"] ?>" id="usrName" name="usrName"/>
    <br>

    <label for="password">Passwort:</label>
    <br>
    <input class="input_container_element" type="password" value="" id="password" name="password"/>
    <br>

    <div class="error"><?= $error_message ?></div>
    <br>

    <input class="button" type="submit" value="Anmelden und die JavaScript-Version verwenden" name="login"/>
    <br><br>
    <input class="button" type="submit" value="Anmelden und die PyScript-Version verwenden" name="login"/>
    <br>
    <br>
</form>

<div>
    Noch kein Konto? Dann kannst du dich <a href="register.php">hier</a> registrieren.
</div>

</body>
</html>