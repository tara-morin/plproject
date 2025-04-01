<?php
// File: views/focus.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">  
  <meta name="author" content="Ninglan Lei, Tara Morin">
  <meta name="description" content="StudyBuddy Focus Page">  
  <meta name="keywords" content="study, focus, timer">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>StudyBuddy - Focus</title>
  
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" 
    crossorigin="anonymous">

  <link rel="stylesheet/less" type="text/css" href="styles/custom.less">
  <script src="https://cdn.jsdelivr.net/npm/less"></script>
</head>

<body>
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
          <a class="nav-link active" aria-current="page" href="index.php?command=focus">Focus</a>
          <a class="nav-link" href="index.php?command=showTasks">Plan</a>
          <a class="nav-link" href="index.php?command=chat">Chat</a>
          <a class="nav-link" href="index.php?command=profile">Profile</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Focus Container -->
  <main class="container text-center my-5">
    <?php if (isset($task_info) && !empty($task_info)): ?>
      <h1 class="display-1" id="timer" aria-live="polite">15:42</h1>
      
      <h2 class="lead" id="currentTask"><?= htmlspecialchars($task_info['title']) ?></h2>
      
      <button class="btn btn-secondary btn-lg m-3" id="pauseBtn" title="Pause the timer">
        Pause
      </button>
      
      <a href="index.php?command=dashboard" class="btn btn-primary btn-lg m-3" id="saveProgressBtn" 
         title="Save progress and return home">
        Save Progress &amp; Return Home
      </a>
    <?php else: ?>
      <h2 class="lead">No task selected. Please pick a task to focus on.</h2>
      
      <a href="index.php?command=showTasks" class="btn btn-danger btn-lg m-3" title="Go to to-do page">
        Go to Plan
      </a>
    <?php endif; ?>
  </main>

  <footer class="footer p-2 text-center">
    <p>&copy; 2025 CS4640</p>
  </footer>
</body>
</html>
