id_increment = 0

html_task_template = Element("task_template")
html_task_container = Element("task_container")
html_task_input = Element("task_input")


def add_task():

    # if input value length other than 0, execute. If 0, string is empty, interpreted as false
    if html_task_input.element.value:

        # import the global id_increment variable into this method
        global id_increment

        # clone template
        new_task_html = html_task_template \
            .select(".task", from_content=True) \
            .clone(id_increment)

        # select the template's contents
        new_task_html_checkbox = new_task_html.select("input")
        new_task_html_content = new_task_html.select("p")

        # set the ID of new_task_html
        new_task_html.element.id = str(id_increment)

        # Write, what is written in the input box, into the new task
        new_task_html_content.element.innerText = html_task_input.element.value

        def cross_out_task(evt=None):
            if not new_task_html_content.element.classList.contains("line-through"):
                new_task_html_content.element.classList.add("line-through")
            else:
                new_task_html_content.element.classList.remove("line-through")

        new_task_html_checkbox.element.onclick = cross_out_task

        # Append new_task_html to html_task_container,
        # this will display the new task in the browser window
        html_task_container.element.appendChild(new_task_html.element)

        # increment id_increment by one
        id_increment = id_increment + 1


def html_task_input_key_event(event):
    # is called each time a key is pressed, if the pressed key is "Enter"
    # the add_task() method is called
    if event.key == "Enter":
        add_task()


html_task_input.element.onkeypress = html_task_input_key_event
