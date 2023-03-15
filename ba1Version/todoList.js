let id_increment = 0

let html_task_container = document.getElementById("tasks_container")
let html_task_input = document.getElementById("task_input")
let html_debug_out = document.getElementById("debug_out")

function add_task(){

    // if input value length other than 0, execute. If 0, string is empty, interpreted as false
    if(html_task_input.value){
        //element definition
        let new_task = document.createElement("div")
        let new_task_checkbox = document.createElement("input")
        let new_task_content = document.createElement("p")

        //Set the type of new_task_checkbox to "checkbox"
        new_task_checkbox.type = "checkbox"

        //give new_task an ID
        new_task.id = id_increment.toString()

        //Write, what is written in the input box, into the new task
        new_task_content.innerText = html_task_input.value

        //Set the Style of new_task_checkbox and new_task_content to "inline"
        new_task_checkbox.style.display = "inline"
        new_task_content.style.display = "inline"

        function cross_out_task(){
            if(new_task_content.style.textDecoration !== "line-through"){
                new_task_content.style.textDecoration = "line-through"
            } else {
                new_task_content.style.textDecoration = "none"
            }
        }

        new_task_checkbox.addEventListener("click", cross_out_task)

        //append new_task_checkbox and new_task_content to new_task
        new_task.appendChild(new_task_checkbox)
        new_task.appendChild(new_task_content)

        //append new_task to html_task_container,
        // this will display the new task in the browser window
        html_task_container.appendChild(new_task)

        //increment id_increment by one
        id_increment++
    }
}

function html_task_input_key_event(event){
    //is called each time a key is pressed, if the pressed key is "Enter"
    //the add_task() method is called
    if( event.key === "Enter" ) {
        add_task()
    }
}

html_task_input.addEventListener("keydown", html_task_input_key_event)
