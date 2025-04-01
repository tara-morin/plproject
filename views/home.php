 <!DOCTYPE html>
 <html lang="en" >
 <head>
  <!--link to the deployed site: https://cs4640.cs.virginia.edu/tmn7vs/sprint2-->
   <meta charset="UTF-8">  
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   
   <meta name="author" content="Tara Morin">
   <meta name="description" content="StudyBuddy lets you focused and keep up with your tasks.">  
   <meta name="keywords" content="study focus studying focusing plan planner to-do">   
   <title>StudyBuddy Landing Page</title>
   
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"  integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"  crossorigin="anonymous"> 
   
   <link rel="stylesheet/less" type="text/css" href="styles/custom.less" > 
   <script src="https://cdn.jsdelivr.net/npm/less"></script>  
 </head>
 <body>
 <nav class="navbar navbar-expand-sm bg-body-tertiary" data-bs-theme="light">
   <div class="container-fluid">
     <a class="navbar-brand" href="#">StudyBuddy</a>
     <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
       <span class="navbar-toggler-icon"></span>
     </button>
     <div class="navbar-collapse collapse" id="navbarNavAltMarkup">
     <div class="navbar-nav">
              <a class="nav-link" aria-current="page" href="index.php?command=dashboard">Home</a>
              <a class="nav-link" href="index.php?command=focus">Focus</a>
              <a class="nav-link" href="index.php?command=showTasks">Plan</a>
              <a class="nav-link" href="index.php?command=chat">Chat</a>
              <a class="nav-link active" href="index.php?command=profile">Profile</a>
          </div>
     </div>
   </div>
 </nav>
 
   <div class="container-fluid text-center">
     <h1 class="display-1 p-2 g-col-6">
       Welcome,<?php echo $_SESSION["name"]?>
     </h1>
     <div class="row">
        <div class="col-md-6 p-3">
            <div class="container">
                <h2 class="text-center mb-3">Weekly Leaderboard</h2>
                <div class="card">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <span>
                                🥇 <strong>1st Place</strong>
                            </span>
                            <span class="username">
                                @student1
                            </span>
                            <span class="badge bg-warning text-dark">10 hr 38 mins</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <span>
                                🥈 <strong>2nd Place</strong>
                            </span>
                            <span class="username">
                                @student2
                            </span>
                            <span class="badge bg-secondary">8 hr 9 mins</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <span>
                                🥉 <strong>3rd Place</strong>
                            </span>
                            <span class="username">
                                @student3
                            </span>
                            <span class="badge third">7 hr 21 mins</span>
                        </li>
                    </ul>
                </div>
            </div> 
        </div> 
     <div class="col-md-6 p-3">
        <div class="p-2 g-col-6 text-body-secondary">
            You have studied 
          </div>
          <div class="lead"><?php echo $study_today?></div>
          <div class="p-2 g-col-6 text-body-secondary">
            today through StudyWithMe.
          </div>
    </div>
       
    </div>
    <div class="row mt-4">
        <h1 class="display-5 g-col-6">
            Next Task to do:
        </h1>
        <div class="g-col-6"><?php echo $task_title?> </div>
        <a class="btn btn-primary g-col-6" style='float:right' href="index.php?command=focus&task_data=<?= urlencode($next_task) ?>" role="button">Focus</a>
    </div>
     </div>
    
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
 
 <!-- </div> -->
 <footer class="footer p-2 g-col-6">
   <p>&copy; 2025</p>
 </footer>
 </body>
 
 </html>
 