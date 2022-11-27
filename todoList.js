let id_increment = 0

let html_task_container = document.getElementById("tasks-container")
let html_task_input = document.getElementById("task_input")
let html_debug_out = document.getElementById("debug_out")

function add_task(){
    // if input value length greater 0, execute. If 0, string is empty, interpreted as false
    if(html_task_input.value){
        //create html task element and its content
        let new_task = document.createElement("div")
        new_task.id = id_increment.toString()

        let new_task_checkbox = document.createElement("input")
        new_task_checkbox.type = "checkbox"
        new_task_checkbox.style.display = "inline-block"

        let new_task_content = document.createElement("p")
        new_task_content.innerText = html_task_input.value
        new_task_content.style.display = "inline-block"

        new_task.appendChild(new_task_checkbox)
        new_task.appendChild(new_task_content)

        html_task_container.appendChild(new_task)

        id_increment++

        function cross_out_task(){
            if(new_task_content.style.textDecoration !== "line-through"){
                new_task_content.style.textDecoration = "line-through"
            } else {
                new_task_content.style.textDecoration = "none"
            }
        }

        new_task_checkbox.addEventListener("click", cross_out_task)
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