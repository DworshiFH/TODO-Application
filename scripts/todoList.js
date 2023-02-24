let html_task_container = document.getElementById("tasks_container")
let html_task_input = document.getElementById("task_input")
let html_task_note_input = document.getElementById("task_note_input")
let html_task_due_time_input = document.getElementById("task_due_time_input")
let html_debug_out = document.getElementById("debug_out")

//sends a get request to retrieveTasks.php and calls the generate_task method
//for every row in the response
function get_tasks_and_generate_task_list(){
    html_task_container.innerHTML = ""
    $.ajax({
        dataType: "json",
        url: "backend/backend.php",
        type: "GET",
        success: function (tasks_json){
            for(let i = 0; i < tasks_json.length; i++) {
                let task = tasks_json[i]
                //TaskID, task, taskNote, IsCompleted, DueTime
                generate_task(task[0], task[1], task[2], task[3], task[4])
            }
        }
    })
}

//takes a task's content and posts it to backend.php
//task: String, task_note: String
function post_task_to_backend(task, task_note, task_due_time){

    if(task_due_time){
        let task_due_time_prepared_for_backend;
        //format task_due_time: YYYY-MM-DDTHH:MI
        //format DueTime in Backend: YYYY-MM-DD HH:MI:SS
        //task_due_time needs to be split at the "T", and instead a whitespace has to be inserted
        //instead of explicit seconds, only ":00" are inserted.
        task_due_time_prepared_for_backend = task_due_time.split("T")[0] + " " + task_due_time.split("T")[1] + ":00"

        $.ajax({
            url: "backend/backend.php",
            type: "POST",
            data: {
                Task: task,
                TaskNote: task_note,
                DueTime: task_due_time_prepared_for_backend
            }
        })
    } else {
        $.ajax({
            url: "backend/backend.php",
            type: "POST",
            data: {
                Task: task,
                TaskNote: task_note
            }
        })
    }
}

//takes all possible task contents and posts them to backend.php
//task_id: String, task: String, task_note: String, is_completed: boolean
function put_task_to_backend(task_id, task, task_note, is_completed){
    let is_completed_string
    if(is_completed === true){
        is_completed_string = "1"
    } else {
        is_completed_string = "0"
    }
    $.ajax({
        url: "backend/backend.php",
        type: "PUT",
        data: {
            TaskID: task_id,
            Task: task,
            TaskNote: task_note,
            IsCompleted: is_completed_string
        }
    })
}

//takes a taskID and posts it to deleteTask.php
//taskID: String
function delete_task_in_backend(task_id){
    $.ajax({
        url: "backend/backend.php",
        type: "DELETE",
        data: {
            TaskID: task_id
        }
    })
}

function generate_task(task_id, task, task_note = "", is_completed, due_time){

    //create a details + summary element
    let new_task_container = document.createElement("details");
    new_task_container.id = task_id

    //create the detail text and append it to new_task_container
    let new_task_container_summary = document.createElement("summary");
    new_task_container_summary.innerText = task;
    new_task_container.appendChild(new_task_container_summary);

    //create the task note div and append it to the new_task_container
    let task_note_div = document.createElement("div");
    task_note_div.innerText = task_note;

    //define styling for task_note_div
    task_note_div.style.display = "inline"
    new_task_container.appendChild(task_note_div);

    //create a breakpoint
    new_task_container.appendChild(document.createElement("br"))

    if(due_time){
        //create the div that contains the due time
        let new_task_due_time = document.createElement("div")
        new_task_due_time.innerText = "Zu erledigen bis: " + due_time
        new_task_container.appendChild(new_task_due_time)
    }

    //create the cross-out checkbox
    let new_task_checkbox = document.createElement("input")
    new_task_checkbox.type = "checkbox"
    new_task_checkbox.id = "ckbx" + task_id
    //create the associated label
    let new_task_checkbox_label = document.createElement("divLabel")
    new_task_checkbox_label.innerText = "Aufgabe erledigt"
    new_task_checkbox_label.appendChild(new_task_checkbox)


    //apply styling in accordance to is_completed
    if(is_completed === "1"){
        new_task_container_summary.style.textDecoration = "line-through"
        new_task_container.style.textDecoration = "line-through"
        new_task_checkbox.checked = true
    } else {
        new_task_container_summary.style.textDecoration = "none"
        new_task_container.style.textDecoration = "none"
        new_task_checkbox.checked = false
    }

    //create the cross_out_task function
    function cross_out_task(){
        //when this function is called, it checks whether the task is completed or not,
        // when it is no completed, it updates the task in the backend,
        // applies the line-through decoration and vice versa
        if(is_completed === "0"){
            put_task_to_backend(task_id, task, task_note, true)
            new_task_container_summary.style.textDecoration = "line-through"
            new_task_container.style.textDecoration = "line-through"
            is_completed = "1"
        } else {
            put_task_to_backend(task_id, task, task_note, false)
            new_task_container_summary.style.textDecoration = "none"
            new_task_container.style.textDecoration = "none"
            is_completed = "0"
        }
    }
    //add event listener for a click on the checkbox
    new_task_checkbox.addEventListener("click", cross_out_task)
    //append to new_task_container
    new_task_container.appendChild(new_task_checkbox_label)

    //define the delete button
    let new_task_delete_btn = document.createElement("button")
    new_task_delete_btn.classList.add("button")
    new_task_delete_btn.innerText = "Aufgabe entfernen"

    new_task_delete_btn.addEventListener("click", function () {
        delete_task_in_backend(task_id)
        setTimeout(get_tasks_and_generate_task_list, 100)
    })

    new_task_container.appendChild(new_task_delete_btn)

    //append the completed new_task_container to html_task_container
    html_task_container.appendChild(new_task_container)
}

get_tasks_and_generate_task_list();

function html_task_input_key_event(event){
    //is called each time a key is pressed, if the pressed key is "Enter",
    //the add_task() method is called
    if( event.key === "Enter" ) {
        add_task()
    }
}

//adds a "keydown" event listener to the html_task_input element
html_task_input.addEventListener("keydown", html_task_input_key_event)

function add_task(){

    // if input value length greater 0, execute. If 0, string is empty, interpreted as false
    if(html_task_input.value){
        post_task_to_backend(html_task_input.value, html_task_note_input.value, html_task_due_time_input.value);
        setTimeout(get_tasks_and_generate_task_list, 1000)
    }
}