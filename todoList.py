id_increment = 0

html_task_template = Element("task_template")
html_task_container = Element("tasks_container")
html_task_input = Element("task_input")
html_debug_out = Element("debug_out")


def add_task():
    # if input value greater 0, execute. If 0 (false), string is empty, don't execute
    if html_task_input.element.value:
        global id_increment
        # boolean value to track whether a task is done
        task_done = False

        # clone template
        new_task_html = html_task_template \
            .select(".task", from_content=True) \
            .clone(id_increment)

        # select the template's contents
        new_task_html_checkbox = new_task_html.select("input")
        new_task_html_content = new_task_html.select("p")

        # write values
        new_task_html.element.id = id_increment
        new_task_html_content.element.innerText = html_task_input.element.value
        html_task_container.element.appendChild(new_task_html.element)

        def cross_out_task(evt=None):
            if task_done:
                new_task_html_content.element.classList.add("line-through")
                return True
            else:
                new_task_html_content.element.classList.remove("line-through")
                return False

        task_done = new_task_html_checkbox.element.onclick = cross_out_task

        id_increment = id_increment + 1


def html_task_input_key_event(event):
    # is called each time a key is pressed, if the pressed key is "Enter"
    # the add_task() method is called
    if event.key == "Enter":
        add_task()


html_task_input.element.onkeypress = html_task_input_key_event
