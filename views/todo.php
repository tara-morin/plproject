<?php
// File: views/todo.php
// This view assumes that the controller has set $tasks as an array of tasks for the logged-in user.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">  
  <meta name="author" content="Ninglan Lei">
  <meta name="description" content="StudyBuddy To-Do Page">  
  <meta name="keywords" content="study, planner, tasks">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>StudyBuddy - To-do</title>
  
  <!-- Bootstrap CSS -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" 
    crossorigin="anonymous">

  <!-- Custom LESS/CSS -->
  <link rel="stylesheet/less" type="text/css" href="styles/custom.less">
  <script src="https://cdn.jsdelivr.net/npm/less"></script>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-sm bg-body-tertiary" data-bs-theme="light">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php?command=dashboard">StudyBuddy</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
              data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" 
              aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="navbar-collapse collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
          <a class="nav-link" href="index.php?command=dashboard">Home</a>
          <a class="nav-link" href="index.php?command=focus">Focus</a>
          <a class="nav-link active" aria-current="page" href="index.php?command=showTasks">Plan</a>
          <a class="nav-link" href="index.php?command=chat">Chat</a>
          <a class="nav-link" href="index.php?command=profile">Profile</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Container -->
  <main class="container my-5">
    <h1 class="display-4 text-center">Plan / To-Do</h1>
    
    <!-- Filter and New Task Controls -->
    <div class="d-flex justify-content-between align-items-center my-4 flex-wrap">
      <!-- Simple filter dropdown -->
      <div class="dropdown my-2">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdown" 
                data-bs-toggle="dropdown" aria-expanded="false">
          Filter by
        </button>
        <ul class="dropdown-menu" aria-labelledby="filterDropdown">
          <li><a class="dropdown-item" href="#">Due Date</a></li>
          <li><a class="dropdown-item" href="#">Class</a></li>
          <li><a class="dropdown-item" href="#">Project</a></li>
          <li><a class="dropdown-item" href="#">Time Spent</a></li>
        </ul>
      </div>

      <!-- New Task Button triggers the creation modal -->
      <button type="button" class="btn btn-primary my-2" data-bs-toggle="modal" 
              data-bs-target="#newTaskModal">
        New Task
      </button>
    </div>

    <!-- Task List Table -->
    <table class="table table-striped table-responsive">
      <thead>
        <tr>
          <th scope="col">Due</th>
          <th scope="col">Task</th>
          <th scope="col">Time Spent</th>
          <th scope="col">Focus</th>
          <th scope="col">Update</th>
          <th scope="col">Delete</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($tasks)): ?>
          <?php foreach ($tasks as $task): ?>
            <tr>
              <td><?php echo htmlspecialchars($task['due_date']); ?></td>
              <td><?php echo htmlspecialchars($task['title']); ?></td>
              <!-- Updated to use the controller's static formatTime function -->
              <td><?php echo StudyWithMeController::formatTime($task['time_spent']); ?></td>
              <td>
                <a href="index.php?command=focus" class="btn btn-success">FOCUS</a>
              </td>
              <td>
                <!-- Update button that triggers the modal -->
                <button type="button" class="btn btn-warning btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#updateTaskModal_<?php echo $task['id']; ?>">
                  Update
                </button>
              </td>
              <td>
                <!-- Delete form -->
                <form action="index.php?command=deleteTask" method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this task?');">
                  <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                  <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
              </td>
            </tr>

            <!-- Update Task Modal for this task -->
            <div class="modal fade" id="updateTaskModal_<?php echo $task['id']; ?>" tabindex="-1" 
                 aria-labelledby="updateTaskModalLabel_<?php echo $task['id']; ?>" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form action="index.php?command=updateTask" method="POST">
                    <div class="modal-header">
                      <h1 class="modal-title fs-5" id="updateTaskModalLabel_<?php echo $task['id']; ?>">Update Task</h1>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                      <div class="mb-3">
                        <label for="title_<?php echo $task['id']; ?>" class="form-label">Task Name</label>
                        <input type="text" class="form-control" id="title_<?php echo $task['id']; ?>" 
                               name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                      </div>
                      <div class="mb-3">
                        <label for="due_date_<?php echo $task['id']; ?>" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date_<?php echo $task['id']; ?>" 
                               name="due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>" required>
                      </div>
                      <div class="mb-3">
                        <label for="time_spent_<?php echo $task['id']; ?>" class="form-label">Time Spent (hh:mm)</label>
                        <input type="text" class="form-control" id="time_spent_<?php echo $task['id']; ?>" 
                               name="time_spent" value="<?php echo StudyWithMeController::formatTime($task['time_spent']); ?>" 
                               placeholder="e.g. 0:30" pattern="^\d{1,2}:\d{2}$">
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="completed" 
                               id="completed_<?php echo $task['id']; ?>"
                               <?php echo ($task['completed'] === 't' || $task['completed'] === true) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="completed_<?php echo $task['id']; ?>">
                          Completed
                        </label>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">Update Task</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center">No tasks found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </main>

  <!-- Modal for creating a new task -->
  <div class="modal fade" id="newTaskModal" tabindex="-1" aria-labelledby="newTaskModalLabel" 
       aria-hidden="true" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="newTaskModalLabel">Add a New Task</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="index.php?command=createTask" method="POST">
            <fieldset>
              <legend class="visually-hidden">Create a new task</legend>
              <div class="mb-3">
                <label for="title" class="form-label">Task Name</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="e.g. Read Chapter 2" required>
              </div>
              <div class="mb-3">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" class="form-control" id="due_date" name="due_date" required>
              </div>
              <div class="mb-3">
                <label for="time_spent" class="form-label">Time Spent (hh:mm)</label>
                <input type="text" class="form-control" id="time_spent" name="time_spent" placeholder="e.g. 0:30" pattern="^\d{1,2}:\d{2}$">
              </div>
            </fieldset>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Add Task</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="foot footer p-2 text-center">
    <p>&copy; 2025</p>
  </footer>
  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
          integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" 
          crossorigin="anonymous"></script>
</body>
</html>
