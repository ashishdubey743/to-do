<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <style>
        .center-form {
            display: flex;
            justify-content: center;
        }


        .btn-blue-300 {
            background-color: #1870B7;
            /* Equivalent to Bootstrap $blue-300 */
            color: white;
        }

        .input-container {
            display: flex;
            flex-direction: column;
            /* Stack input and error message vertically */
        }

        .btn {
            margin-left: 0.5rem;
            /* Add some space between input container and button */
        }

        .pagination {
            justify-content: center;
            margin-top: 20px;
        }

        .pagination>.page-item>.page-link {
            color: #6c757d;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
        }

        .pagination>.page-item>.page-link:hover {
            color: #007bff;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }

        .pagination>.page-item.active>.page-link {
            z-index: 3;
            color: #ffffff;
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3 class="mt-4 text-primary">PHP - Simple To Do List App</h3>
        <hr>
        <form id="task-form" class="center-form">
            @csrf
            <div class="mb-3 d-flex align-items-start">
                <div class="input-container">
                    <input type="text" class="form-control" id="task_name" name="task_name" placeholder="Add Task"
                        required>
                    <div class="error text-danger" style="display:none;">Error message here</div>
                </div>
                <button type="submit" class="btn btn-blue-300">Add Task</button>
            </div>
        </form>




        <table class="table table-hover mt-4">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Task</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody id="task-list">
                <!-- Example Task -->
                @php $count = 0; @endphp
                @foreach ($tasks as $task)
                    @php    $count++; @endphp
                    <tr>
                        <th scope="row">{{ $count }}</th>
                        <td>{{ $task->task_name }}</td>
                        <td>{{ $task->task_status }}</td>
                        <td>
                            @if ($task->task_status !== 'Done')
                                <button class="btn btn-success btn-sm" onclick="completeTask(this, {{ $task->id }})"><i
                                        class="fa-solid fa-check"></i></button>
                            @endif

                            <button class="btn btn-danger btn-sm" onclick="openModal(this, {{ $task->id }})"
                                data-bs-toggle="modal" data-bs-target="#modal"><i class="fa-solid fa-xmark"></i></button>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirm:</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Do you want to delete the task?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="delete_task"
                        data-bs-dismiss="modal">Delete</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"
        integrity="sha384-eMNipPo8rFyyhHR4mIbXmUMjvRQy0zhG+TB4rmD1AYAOwD8GqOJmMB8ymEaaIh5y"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script>
        let currentButton;
        document.getElementById('task-form').addEventListener('submit', function (e) {
            e.preventDefault();

            const taskInput = document.getElementById('task_name');
            const taskValue = taskInput.value.trim();

            if (taskValue) {
                addTask(taskValue);
                taskInput.value = '';
            }
        });

        async function addTask(task) {

            let data = {
                task_name: task,
                _token: '{{ csrf_token()}}'
            }
            let response = await runAjax("{{ route('add_task') }}", 'POST', data);
            if (response.code === 200) {
                const taskList = document.getElementById('task-list');
                const rowCount = taskList.rows.length + 1;
                const newRow = taskList.insertRow();

                newRow.innerHTML = `
                <th scope="row">${rowCount}</th>
                <td>${task}</td>
                <td>Pending</td>
                <td>
                    <button class="btn btn-success btn-sm" onclick="completeTask(this, ${response.data.id})"><i class="fa-solid fa-check"></i></button>
                    <button class="btn btn-danger btn-sm" onclick="openModal(this, ${response.data.id})"
                                data-bs-toggle="modal" data-bs-target="#modal"><i class="fa-solid fa-xmark"></i></button>
                </td>
            `;
            } else {
                document.querySelector('.error').textContent = response.message;
                document.querySelector('.error').style.display = "block";
                setTimeout(() => {
                    document.querySelector('.error').style.display = "none";
                }, 2000);
            }
        }

        async function completeTask(button, task_id) {
            let data = {
                task_id: task_id,
                _token: '{{ csrf_token()}}'
            }
            let response = await runAjax('{{ route('update_task') }}', 'PUT', data)
            if (response.code === 200) {
                const row = button.closest('tr');
                row.cells[2].innerText = 'Done';
                button.remove();
            }
        }

        function openModal(button, taskId) {
            currentButton = button;  // Store the reference of the button that opened the modal
            document.getElementById('delete_task').setAttribute('data-task-id', taskId);
        }

        document.getElementById('delete_task').addEventListener('click', function () {
            const taskId = this.getAttribute('data-task-id');
            deleteTask(currentButton, taskId);
        });

        async function deleteTask(button, task_id) {
            let data = {
                task_id: task_id,
                _token: '{{ csrf_token()}}'
            }
            let response = await runAjax('{{ route('delete_task') }}', 'DELETE', data)
            if (response.code === 200) {
                const row = button.closest('tr');
                row.remove();
            }
        }

        async function runAjax(url, method, data) {
            return $.ajax({
                url: url,
                type: method,
                data: data,
                dataType: 'json',
                success: function (response) {
                    return response
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', error);
                }
            });
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"
        integrity="sha512-u3fPA7V8qQmhBPNT5quvaXVa1mnnLSXUep5PS1qo5NRzHwG19aHmNJnj1Q8hpA/nBWZtZD4r4AX6YOt5ynLN2g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>