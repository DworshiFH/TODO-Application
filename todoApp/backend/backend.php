<?php

session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$Task_err = $TaskNote_err = "";
require "res/config.php";

$param_UsrName_hash = $_SESSION['user_id'];

if($_SERVER["REQUEST_METHOD"] == "GET"){

    // Set parameters
    $sql = "SELECT * FROM Task WHERE UsrName_hash = '$param_UsrName_hash'";

    $query = mysqli_query($link, $sql);

    //create a data array to store the data from the DB
    $data = array();

    //fetch the data as an array
    while($row = mysqli_fetch_array($query)){

        //retreive the data array's rows and decrypt the encrypted entries
        $data_TaskID = $row["TaskID"];
        $data_Task = openssl_decrypt($row["Task"], $ciphering, $encryption_key, $options, $encryption_iv);
        $data_TaskNote = openssl_decrypt($row["TaskNote"], $ciphering, $encryption_key, $options, $encryption_iv);
        $data_IsCompleted = $row["IsCompleted"];
        $data_DueTime = $row["DueTime"];

        //create a new array containing the row's data
        $data_row = array($data_TaskID, $data_Task, $data_TaskNote, $data_IsCompleted, $data_DueTime);
        //write them into the data array
        $data[] = $data_row;
    }

    //encode the data array as JSON and echo it.
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    echo "here?";

    $param_Task = openssl_encrypt($_POST["Task"], $ciphering, $encryption_key, $options, $encryption_iv);
    $param_TaskNote = openssl_encrypt($_POST["TaskNote"], $ciphering, $encryption_key, $options, $encryption_iv);

    if (strlen($param_Task) > 65535) {
        $Task_err = "Dieser Text ist zu lang für die Datenbank.";
    }
    if (strlen($param_TaskNote) > 65535) {
        $TaskNote_err = "Dieser Text ist zu lang für die Datenbank.";
    }

    if( empty($Task_err) && empty($TaskNote_err) ) { //in this case, the client does NOT KNOW a Task's ID, and wants to post a task

        $param_IsCompleted = 0;



        if (strlen($param_Task) > 65535) {
            $Task_err = "Dieser Text ist zu lang für die Datenbank.";
        }
        if (strlen($param_TaskNote) > 65535) {
            $TaskNote_err = "Dieser Text ist zu lang für die Datenbank.";
        }

        if(isset($_POST["DueTime"]) && !empty($_POST["DueTime"])){
            $param_DueTime = $_POST["DueTime"];
            $query = "INSERT INTO Task (UsrName_hash, Task, TaskNote, IsCompleted, DueTime) 
                              VALUES ('$param_UsrName_hash',
                                      '$param_Task',
                                      '$param_TaskNote',
                                      '$param_IsCompleted',
                                      '$param_DueTime')";
        } else {
            $query = "INSERT INTO Task (UsrName_hash, Task, TaskNote, IsCompleted) 
                              VALUES ('$param_UsrName_hash',
                                      '$param_Task',
                                      '$param_TaskNote',
                                      '$param_IsCompleted')";
        }

        if(!mysqli_query($link, $query)) {
            echo "Es ist ein schwerwiegender Fehler aufgetreten.";
        } else {
            echo "Task erfolgreich in die Datenbank eingetragen. "
                . "Task Titel: " . $_POST["Task"]
                . " "
                . "Task Notiz: " . $_POST["TaskNote"];
        }

    } else {
        echo "Falsche Daten, bitte probieren Sie es neu.";
        echo "Aufgabentext Fehler: " . $Task_err;
        echo "Aufgabennotiz Fehler: ".$TaskNote_err;
    }
}

if($_SERVER["REQUEST_METHOD"] == "PUT") {

    parse_str(file_get_contents('php://input'), $_PUT);

    $param_Task = openssl_encrypt($_PUT["Task"], $ciphering, $encryption_key, $options, $encryption_iv);
    $param_TaskNote = openssl_encrypt($_PUT["TaskNote"], $ciphering, $encryption_key, $options, $encryption_iv);

    if (strlen($param_Task) > 65535) {
        $Task_err = "Dieser Text ist zu lang für die Datenbank.";
    }
    if (strlen($param_TaskNote) > 65535) {
        $TaskNote_err = "Dieser Text ist zu lang für die Datenbank.";
    }

    if (isset($_PUT["TaskID"])
        && !empty($_PUT["TaskID"])
        && empty($Task_err)
        && empty($TaskNote_err)) { //in this case, the client knows a Task's ID, and wants to update it

        $param_IsCompleted = $_PUT["IsCompleted"];

        $sql = "SELECT * FROM Task WHERE TaskID = ?";

        $param_TaskID = $_PUT["TaskID"];

        if (isset($_PUT["DueTime"])) {
            $param_DueTime = $_PUT["DueTime"];
            $query = "UPDATE Task
                  SET Task = '$param_Task',
                      TaskNote = '$param_TaskNote',
                      IsCompleted = '$param_IsCompleted',
                      DueTime = '$param_DueTime'
                  WHERE TaskID = '$param_TaskID' AND UsrName_hash = '$param_UsrName_hash' ";
        } else {
            $query = "UPDATE Task
                  SET Task = '$param_Task',
                      TaskNote = '$param_TaskNote',
                      IsCompleted = '$param_IsCompleted'
                  WHERE TaskID = '$param_TaskID' AND UsrName_hash = '$param_UsrName_hash' ";
        }

        if (!mysqli_query($link, $query)) {
            echo "Es ist ein schwerwiegender Fehler aufgetreten.";
        }
    }
}

if($_SERVER["REQUEST_METHOD"] == "DELETE"){

    parse_str(file_get_contents('php://input'), $_DELETE);

    if( !empty($_DELETE["TaskID"]) && !empty($_SESSION['user_id']) ) { //in this case, the client knows a Task's ID, and wants to update it

        require "res/config.php";

        // Set parameters
        $param_TaskID = $_DELETE["TaskID"];

        $sql = "DELETE FROM Task WHERE taskID='$param_TaskID' AND UsrName_hash = '$param_UsrName_hash'";

        $query = mysqli_query($link, $sql);
    }
}