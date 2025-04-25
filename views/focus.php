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
    // Decode the task data URL-encoded string and parse it into an object
    let task_data = <?= json_encode(json_decode(urldecode($_GET['task_data']))) ?>;

    // Ensure task_data exists and has a title
    if (task_data && task_data.title) {
      document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('task-title').textContent = task_data.title;
      });
    } else {
      document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('task-title').textContent = 'No task data available';
      });
    }
  </script>
  <?php endif; ?>
  <script>
    let modal;
    let my_session;
    let set_time=null;
    function StudySession(){
      this.time= null;
      this.interval= null;
      this.starting_time= null;
      this.grand_total=0;
      this.session_started=false;
      this.start= async function(){
        if (typeof task_data === 'undefined') {
          alert('Task data is missing! Please go back and select a task before starting the timer.');
          return;
        }
        console.log("starting new session");
        session_started = true;

          try {
            const start_response= await fetch('index.php?command=startStudy', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({ userID: task_data.user_id })
            });
            const data = await start_response.json();
          } catch (err) {
            console.error('Failed to start study session:', err);
          }}
        this.end= async function(){
          if (typeof task_data === 'undefined') {
            alert('Task data is missing! Please go back and select a task before starting the timer.');
            return;
          }
          const time_spent = this.grand_total;
          console.log("grand total for the session is:")
          console.log(time_spent);
          if (this.grand_total < 60) {
            alert('You must spend more than 1 minute studying to log study time!');
            return;
          }
        try {
          //Wait for logging task time
          console.log("logging time now.");
          const result= await logTime();
          if (result==false){
            console.log("issue logging task time");
            alert("issue logging task time");
            return;
          }
          console.log("logged task time, now going to end session");
          console.log("user ID is:");
          console.log(task_data.user_id);
          // Wait for ending the session and updating that time
          const endResponse= await fetch('index.php?command=endStudy', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              userID: task_data.user_id
            })
          });
        console.log("Received endResponse:", endResponse);
        const endResult = await endResponse.json();
        console.log("Received endResult:", endResult);
        if (!endResponse.ok || !endResult.success) {
          console.error('Failed to end study session:', endResult.error || endResponse.statusText);
          alert('Error ending session: ' + (endResult.error || 'Unknown error'));
          return;
        }
        if (endResult.success){
          console.log("redirect finally working!!")
          my_session.session_started = false;
          //Redirect to home
          window.location.href = 'index.php?command=dashboard';
        }
        } catch (error) {
          console.error('Failed to end study session:', error);
          alert('There was an error saving your session. Please try again.');
        }
        }
  }

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
      //make sure a task is present
      if (typeof task_data === 'undefined') {
        alert('Task data is missing! Please go back and select a task before starting the timer.');
        return;
      }
      //get the start button
      const button= document.getElementById('timerBtn');
      button.textContent="Pause";
      //redirect event listener
      button.onclick= pauseTimer;
      if (my_session==null){
        //if session is null, start a new one
        my_session= new StudySession();
        my_session.start();
        //check if a time was entered into the setTimer function
        if (my_session.time == null) {
          //use the set_time but if that is null, default to 30 minutes
          my_session.time = set_time ?? (30 * 60);
        }
      }
      else if (my_session.started==false){
        //check if there is technically a session present, but data has already been sent to the server
        //in this case we will start a new session
        my_session= new StudySession();
        my_session.start();
        //check if a time was entered into the setTimer function
        if (my_session.time == null) {
          //use the set_time but if that is null, default to 30 minutes
          my_session.time = set_time ?? (30 * 60);
        }
  }
    //keep track of what the timer is starting at
    my_session.starting_time = my_session.time;
    my_session.interval= setInterval(updateTimer, 1000);
}
  function updateTimer(){
    const timer = document.getElementById('timer');
    timer.textContent = getTimeasString(my_session.time);
    my_session.time--;

    if (my_session.time < 0) {
      clearInterval(my_session.interval);
      alert("TIME'S UP! Take a break or start again.");
      pauseTimer();
      //reset session time to 0
      my_session.time = 0;
      //reset timer display to 0 too
      const timer = document.getElementById('timer');
      timer.textContent = getTimeasString(my_session.time);
      const button = document.getElementById('timerBtn');
      button.textContent = "Start";
      button.onclick = startTimer;
    }
  }

  function pauseTimer(){
      //pause the time
      clearInterval(my_session.interval);
      //get the pause button
      const button= document.getElementById('timerBtn');
      button.textContent="Resume";
      //redirect event listener
      button.onclick= startTimer;
      const time_spent= my_session.starting_time - my_session.time;
      my_session.grand_total += time_spent;
      my_session.starting_time = my_session.time;
  }
  async function logTime(){
    if (typeof task_data === 'undefined') {
      alert('Task data is missing! Please go back and select a task before starting the timer.');
      return;
    }
    if (my_session.grand_total < 60) {
      alert('You must spend more than 1 minute studying to log study time!');
      return;
    }
    console.log("grand total is:");
    console.log(my_session.grand_total);
    console.log("to test nullifying, taskid is");
    console.log(task_data.id);
    console.log("and last, userid is");
    console.log(task_data.user_id);
    try {
      const logResponse = await fetch('index.php?command=logTaskTime', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          time: my_session.grand_total,
          taskID: task_data.id,
          userID: task_data.user_id
        })
      });
      const logText = await logResponse.json();
      console.log('Raw response from server:', logText);

      let logData;
      try {
        logData = JSON.parse(logText);
      } catch (e) {
        console.error('JSON parse error:', e);
        return false;
      }

      if (!logResponse.ok || !logText.success) {
        console.error('Failed to log time:', logText.error || logResponse.statusText);
        alert('Error logging your study time: ' + (logText.error || 'Unknown error.'));
        return false;
      }
      //clear the current grand_total since time was logged
      console.error("made it to end of logTime function");
      my_session.grand_total = 0;
      return true;
    } catch (err) {
      console.error('Fetch error:', err);
    }
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
      set_time = (hours * 3600) + (mins * 60);
      //check if a session has already begun and the user wants to set a new time
      if (my_session!=null){
        my_session.time= set_time;
      }
      //otherwise, the set_time variable is now saved and will be assigned to my_session.time once a new session is started.
    }
    else{
      alert("Submit time in hours and minutes format! Ex. 2:30");
      return;
    }
    timer.textContent= getTimeasString(set_time);
    modal.hide();
  }

  window.addEventListener('unload', function(event){
    logTime();
    localStorage.setItem('session', JSON.stringify(my_session));
  });

  window.addEventListener('load', function(event){
    const data= localStorage.getItem('session');
    localStorage.removeItem('session');
    if (!data || data === 'undefined' || data === 'null') {
      // Default to 30 minutes on the timer
      let time = 30 * 60;
      let mins = Math.floor(time / 60);
      let seconds = time % 60;
      seconds = seconds < 10 ? '0' + seconds : seconds;
      document.getElementById("timer").textContent = `${mins}:${seconds.toString().padStart(2, '0')}`;
    }else {
      const data_as_json = JSON.parse(data);
      my_session = new StudySession();
      Object.assign(my_session, data_as_json);
      document.getElementById("timer").textContent = getTimeasString(my_session.time);
    }
  });
  function resetSession() {
    //stop any timer
    clearInterval(my_session?.interval);
    //nullify session object
    my_session = null;
    set_time = null;

    // Reset timer display to 30 minutes
    const defaultTime = 30 * 60;
    const timer = document.getElementById("timer");
    timer.textContent = getTimeasString(defaultTime);

    // clear session from local storage
    localStorage.removeItem('session');
  }
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
      <h2 class="lead" id="task-title"></h2>
      
      <button class="btn btn-secondary btn-lg m-3" id="timerBtn" title="Start the timer" onclick="startTimer()">
        Start
      </button>
      
      <button class="btn btn-primary btn-lg m-3" id="saveProgressBtn" onclick="my_session.end();" 
         title="Save progress and return home">
        Log Session Time &amp; Return Home
    </button>
    <button class="btn btn-danger btn-sm" onclick="resetSession()">Reset Session</button>
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