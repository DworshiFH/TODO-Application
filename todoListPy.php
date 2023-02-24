<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>TODO Application PyScript</title>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />

    <link rel="stylesheet" href="https://pyscript.net/latest/pyscript.css" />
    <script defer src="https://pyscript.net/latest/pyscript.js"></script>

    <py-config>
        [[fetch]]
        files = ["/utils.py"]
    </py-config>

    <link rel="stylesheet" href="style/todoListStylesheet.css"

</head>

<body id = "body">

<py-script src = "scripts/todoList.py"></py-script>

<main>
    <h2 id = "header">TODO-Application</h2>
    <div id = "input_container">
        <label for="task_title_input">Aufgabentitel<br></label>
        <input id="task_title_input" class="input_container_element" type="text">
        <br>
        <label for="task_note_input">Aufgabennotizen<br></label>
        <textarea id="task_note_input" rows="4" cols="30" class="input_container_element"></textarea>
        <br>
        <label for="task_due_time_input">Frist (optional)<br> </label>
        <input id="task_due_time_input" type="datetime-local" class = "input_container_element">
        <br>
        <br>
        <button id="add_task_btn" class="button input_container_element" py-click="add_task()">
            Aufgabe erstellen
        </button>
        <p>Deine aktuellen Aufgaben: </p>
    </div>

    <div id="tasks_container"></div>

    <br>
    <a href="backend/logout.php">
        <button class = "button">Abmelden</button>
    </a>
    <br>
    <br>

    <a href="todoListJS.php">
        <button class = "button">Zur JavaScript Version wechseln</button>
    </a>

    <template id="task_template">

        <div class="task py-li-element">
            <details class = "task_details">
                <summary class = "task_summary"></summary>
                <divNote></divNote>
                <divDue></divDue>
                <divLabel></divLabel>
                <label><input type="checkbox"></label>
                <button class="py-button"></button>
            </details>
        </div>

        <br class = "br_template">
    </template>
</main>
</body>
</html>