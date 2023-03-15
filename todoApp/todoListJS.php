<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<style>

</style>
<head>
    <title>Todo-Application JavaScript</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <script src="scripts/src/jquery-3.6.3.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="style/todoListStylesheet.css"
</head>

<body>
<main>
    <h2 id = "header">TODO-Application</h2>
    <div id = "input_container">
        <label for="task_input">Aufgabentitel<br></label>
        <input id="task_input" type="text" placeholder="" class="input_container_element">
        <br>
        <label for="task_note_input">Aufgabennotizen<br></label>
        <textarea id="task_note_input" rows="4" cols="30" class="input_container_element"></textarea>
        <br>
        <label for="task_due_time_input">Frist (optional)<br> </label>
        <input id="task_due_time_input" type="datetime-local" class="input_container_element">
        <br>
        <br>
        <button id="add_task_btn" onclick="add_task()" class="button input_container_element">
            Aufgabe erstellen
        </button>
    </div>

    <p>Deine aktuellen Aufgaben:</p>

    <div id="tasks_container"></div>

    <br>
    <a href="backend/logout.php">
        <button class = "button">Abmelden</button>
    </a>
    <br>
    <br>

    <a href="todoListPy.php">
        <button class = "button">Zur PyScript Version wechseln</button>
    </a>

</main>
</body>

<script src = "scripts/todoList.js" type="text/javascript"></script>

</html>