import asyncio
from utils import request  # import our request function.

id_increment = 0

# Selecting the HTML Elements in todoListPy.php
html_task_template = \
    Element("task_template")
html_task_container = \
    Element("tasks_container")
html_task_title_input = \
    Element("task_title_input")
html_task_note_input = \
    Element("task_note_input")
html_task_due_time_input = \
    Element("task_due_time_input")
html_br = html_task_template.select(
    ".br_template", from_content=True
).clone(id_increment)

# variables for
backend_url = "backend/backend.php"
headers = {"Content-type": "application/x-www-form-urlencoded; charset=UTF-8"}


# sends a get request to the backend and then calls the g
# generate_task method for every row in the response
async def get_tasks_and_generate_task_list():
    # Send a GET request to backend/retrieveTasks.php
    response = await request(f"{backend_url}", method="GET")

    html_task_container.element.innerHTML = ""

    for task in await response.json():
        # TaskID, task, taskNote, IsCompleted, DueTime
        generate_task(task[0], task[1], task[2], task[3], task[4])


# sends a post request to the backend
async def post_task_to_backend(task, task_note, task_due_time=""):
    if task_due_time.__len__() > 0:
        print("here?")
        task_due_time_prepared_for_backend = task_due_time.split("T")[0] + " " + task_due_time.split("T")[1] + ":00"

        body = "Task=" + task \
               + "&TaskNote=" + task_note \
               + "&DueTime=" + task_due_time_prepared_for_backend
    else:
        body = "Task=" + task \
               + "&TaskNote=" + task_note

    await request(f"{backend_url}", body=body, method="POST", headers=headers)


async def update_task_to_backend(task_id, task, task_note, is_completed, task_due_time):
    if task_due_time:
        body = "TaskID=" + task_id \
               + "&Task=" + task \
               + "&TaskNote=" + task_note \
               + "&DueTime=" + task_due_time \
               + "&IsCompleted=" + is_completed
    else:

        body = "TaskID=" + task_id \
               + "&Task=" + task \
               + "&TaskNote=" + task_note \
               + "&IsCompleted=" + is_completed
    await request(f"{backend_url}", body=body, method="PUT", headers=headers)


async def delete_task_in_backend(task_id):
    body = "TaskID=" + task_id
    await request(f"{backend_url}", body=body, method="DELETE", headers=headers)


def generate_task(task_id, task, task_note, is_completed, task_due_time):
    # clone templates
    new_task_html = html_task_template.select(".task", from_content=True).clone(id_increment)

    # select the template's contents
    new_task_html_details = new_task_html.select("details")
    new_task_html_summary = new_task_html.select("summary")
    new_task_html_note = new_task_html.select("divNote")
    new_task_html_due = new_task_html.select("divDue")
    new_task_html_checkbox = new_task_html.select("input")
    new_task_html_checkbox_label = new_task_html.select("divLabel")
    new_task_html_del_btn = new_task_html.select("button")

    # set contents
    new_task_html_summary.element.innerText = task
    new_task_html_note.element.innerText = task_note
    new_task_html_del_btn.element.innerText = "Aufgabe entfernen"

    if task_due_time:
        new_task_html_due.element.innerText = "Fällig bis: " + task_due_time
    else:
        new_task_html_due.element.innerText = "Fällig bis: Keine Fälligkeit."

    new_task_html_checkbox_label.element.innerHTML = " Aufgabe erledigt "
    new_task_html_checkbox_label.element.appendChild(new_task_html_checkbox.element)

    # set ids
    new_task_html_details.element.id = task_id
    new_task_html_checkbox.element.id = "ckbx=" + task_id + "&isCompleted=" + is_completed

    # append the new elements to the details container
    new_task_html_details.element.appendChild(new_task_html_summary.element)
    new_task_html_details.element.appendChild(new_task_html_note.element)
    new_task_html_details.element.appendChild(html_br.clone().element)
    new_task_html_details.element.appendChild(new_task_html_due.element)
    new_task_html_details.element.appendChild(html_br.clone().element)
    new_task_html_details.element.appendChild(new_task_html_checkbox_label.element)
    new_task_html_details.element.appendChild(new_task_html_del_btn.element)

    # append the details container to the task container
    html_task_container.element.appendChild(new_task_html_details.element)

    # apply styling in accordance to is_completed
    def cross_out_task(is_completed_local):
        if is_completed_local == "1":
            new_task_html_details.element.classList.add("line-through")
            new_task_html_due.element.classList.add("line-through")
            new_task_html_summary.element.classList.add("line-through")
            new_task_html_note.element.classList.add("line-through")
            new_task_html_checkbox.element.checked = "true"
        else:
            new_task_html_details.element.classList.remove("line-through")
            new_task_html_due.element.classList.remove("line-through")
            new_task_html_summary.element.classList.remove("line-through")
            new_task_html_note.element.classList.remove("line-through")
            new_task_html_checkbox.element.checked = False

    cross_out_task(is_completed)

    def mark_as_completed(evt=None):
        new_task_html_checkbox_id = new_task_html_checkbox.element.id.split("&")[0]
        new_task_html_checkbox_is_completed = new_task_html_checkbox.element.id.split("&")[1].split("=")[1]
        if new_task_html_checkbox_is_completed == "0":
            asyncio.ensure_future(
                update_task_to_backend(task_id, task, task_note, "1", task_due_time)
            )
            new_task_html_checkbox_is_completed = "1"
        else:
            asyncio.ensure_future(
                update_task_to_backend(task_id, task, task_note, "0", task_due_time)
            )
            new_task_html_checkbox_is_completed = "0"

        cross_out_task(new_task_html_checkbox_is_completed)

        new_task_html_checkbox.element.id = new_task_html_checkbox_id + \
                                            "&isCompleted=" + \
                                            new_task_html_checkbox_is_completed

    new_task_html_checkbox.element.onclick = mark_as_completed

    def delete_task(evt=None):
        asyncio.ensure_future(
            delete_task_in_backend(task_id)
        )
        asyncio.ensure_future(
            get_tasks_and_generate_task_list()
        )

    new_task_html_del_btn.element.onclick = delete_task


asyncio.ensure_future(
    get_tasks_and_generate_task_list()
)


def html_task_input_key_event(event):
    # is called each time a key is pressed, if the pressed key is "Enter"
    # the add_task() method is called
    if event.key == "Enter":
        add_task()


html_task_title_input.element.onkeypress = html_task_input_key_event


def add_task():
    # if input value greater 0, execute. If 0 (false), string/element is empty, don't execute
    if html_task_title_input.element.value:
        asyncio.ensure_future(
            post_task_to_backend(html_task_title_input.element.value,
                                 html_task_note_input.element.value,
                                 html_task_due_time_input.element.value)
        )
        asyncio.ensure_future(
            get_tasks_and_generate_task_list()
        )
