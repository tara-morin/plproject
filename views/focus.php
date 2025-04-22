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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <?php if (isset($_GET['task_data'])): ?>
  <script>
    let task_data = <?= json_encode(json_decode(urldecode($_GET['task_data']))) ?>;
  </script>
<?php endif; ?>
  <script>
    let time;
    let interval;
    let modal;
    let starting_time;
    function getTimeasString(time){
      const hours = Math.floor(time / 3600);
      const mins = Math.floor((time % 3600) / 60);
      const seconds = time % 60;
      if (hours>=1){
        return `${hours}:${mins.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
      }
      else{
        return `${mins}:${seconds.toString().padStart(2, '0')}`;
      }
    }
    function startTimer(){
      //get the start button
      const button= document.getElementById('timerBtn');
      button.textContent="Pause";
      //redirect event listener
      button.onclick= pauseTimer;
      if (time==null){
        let input_time = 10;
        time = input_time * 60;
      }
      //keep track of what the timer is starting at
      starting_time= time;
      interval= setInterval(updateTimer, 1000);
  }
    function updateTimer(){
      const timer= document.getElementById('timer');
      timer.textContent = getTimeasString(time);
      time--;
    }
  function pauseTimer(){
      //pause the time
      clearInterval(interval);
      //get the pause button
      const button= document.getElementById('timerBtn');
      button.textContent="Resume";
      //redirect event listener
      button.onclick= startTimer;
      logTime();
  }
  function logTime(){
    //get task information from the header
    if (typeof task_data === 'undefined') {
      alert('Task data is missing! Please go back and select a task before starting the timer.');
      return;}

    const taskID = task_data.id;
    const userID = task_data.user_id;
    console.log("user ID is:"+userID);
    console.log("task ID is:"+taskID);
    //calculate the amount of time spent
    const time_spent= starting_time- time;
    console.log("time spent: "+time_spent);
    //making request to the php controller to log the task time
    fetch('index.php?command=logTaskTime', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
      time: time_spent,
      taskID: taskID,
      userID: userID
    })
    });
  }
  function triggerModal(){
    modal = new bootstrap.Modal(document.getElementById('timerModal'));
    modal.show();
  }
  function setTime(){
    const input= document.getElementById("input_time");
    const timer= document.getElementById('timer');
    const inputValue = input.value.trim();
    //pattern with hours and minutes
    const pattern = /^\d{1,2}:\d{2}$/;
    if (pattern.test(inputValue)) {
      let time_array= inputValue.split(':');
      let hours = time_array[0];
      let mins = time_array[1];
      time = (hours * 3600) + (mins * 60);
    }
    else{
      alert("Submit time in hours and minutes format! Ex. 2:30");
      return;
    }
    timer.textContent= getTimeasString(time);
    modal.hide();
  }

  window.addEventListener('unload', function(event){
    localStorage.setItem('time', time);
    logTime();
  });

  window.addEventListener('load', function(event){
    localStorage.getItem('time', time);
    if (time!=null){
      let mins= Math.floor(time/60);
      let seconds= time % 60;
      document.getElementById("timer").textContent= time;
    }
    else{
      //default of 30 minutes on the timer
      time= 30 * 60;
      let mins= Math.floor(time/60);
      let seconds= time % 60;
      seconds= seconds < 10 ? '0' + seconds : seconds;
      document.getElementById("timer").textContent = `${mins}:${seconds.toString().padStart(2, '0')}`;
    }
});
  </script>
  <!-- <script type="text/javascript" src="timer.js"></script> -->
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
      <h1 class="display-1" id="timer" aria-live="polite"></h1>
      <button class= "btn btn-secondary btn-sm"onclick="triggerModal();">Set a time</button>
    <!--Modal for user to enter their desired time  -->
      <div class='modal fade bd-example-modal-sm' id="timerModal" tabindex='-1' role='dialog' aria-labelledby='mySmallModalLabel' aria-hidden='true'> 
        <div class='modal-dialog modal-sm'> 
          <div class='modal-content'> 
          <label for="time_spent" class="form-label">Study for (hh:mm): </label>
          <input type="text" class="form-control" id="input_time" name="input_time" placeholder="e.g. 0:30" pattern="^\d{1,2}:\d{2}$">
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="modal.hide();">Close</button>
              <button type="button" class="btn btn-primary" onclick="setTime();">Save changes</button>
            </div>
          </div>
        </div> 
      </div>
      <h2 class="lead" id="currentTask"><?= htmlspecialchars($task_info['title']) ?></h2>
      
      <button class="btn btn-secondary btn-lg m-3" id="timerBtn" title="Start the timer" onclick="startTimer()">
        Start
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