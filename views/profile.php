<?php
// File: views/profile.php
// Ensure the user is logged in (this check is already done in the controller).
$fullname = $_SESSION['name'] ?? 'User'; // Use the full name stored during login/registration.
$memberDays = $_SESSION['member_days'] ?? 27; // For example, or query from the database.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">  
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <meta name="author" content="Tara Morin">
    <meta name="description" content="A log of your completed study time.">  
    <meta name="keywords" content="study focus studying focusing plan planner to-do">   
    <title>My Profile</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"  
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"  
          crossorigin="anonymous"> 
          
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
                    <a class="nav-link" aria-current="page" href="index.php?command=dashboard">Home</a>
                    <a class="nav-link" href="index.php?command=focus">Focus</a>
                    <a class="nav-link" href="index.php?command=showTasks">Plan</a>
                    <a class="nav-link" href="index.php?command=chat">Chat</a>
                    <a class="nav-link active" href="index.php?command=profile">Profile</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid align-items-center">
        <!--Profile Picture and Full Name -->
        <div class="row d-flex justify-content-start">
            <div class="col-auto">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" 
                     role="img" aria-label="Profile picture" class="img-fluid bi bi-person-circle">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                </svg>
            </div>
            <div class="col-auto">
                <div class="row" style="min-height: 30px;"></div>
                <div class="col">
                    <h1 class="lead" id="usernameDisplay">
                        <?php echo htmlspecialchars($fullname); ?>
                    </h1>
                </div>
                <div class="col">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editNameModal">
                        Edit Name
                    </button>
                    <!-- Modal for editing the user's full name -->
                    <div class="modal fade" id="editNameModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="index.php?command=setName" method="POST">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="editNameModalLabel">Edit Name</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" 
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="<?php echo htmlspecialchars($fullname); ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Membership Duration- to be implemented later -->
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col d-flex justify-content-start align-items-center">
                <h1 class="display-6 px-2">Member for </h1>
                <h1 class="display-6 px-2 day-count"><?php echo htmlspecialchars($memberDays); ?></h1>
                <h1 class="display-6 px-2">days</h1>
            </div>
        </div>

        <!--Usage Type and space for additional Settings -->
        <div class="row d-flex justify-content-center align-items-center text-center">
            <div class="col-6 d-flex flex-column align-items-center">
                <h1 class="display-6 mb-2">I'm using StudyBuddy for...</h1> 
                <div class="col-auto d-flex align-items-center">
                    <label class="form-check-label me-2">School</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" aria-label="Toggle" id="flexSwitchCheckDefault">
                    </div>
                    <label class="form-check-label ms-2">Work</label> 
                </div>
            </div>

            <div class="col-6">
                <p class="mt-3">More settings coming soon...</p>
                <?php
                    // (Place right after the settings row in profile.php)
                    // You could fetch real numbers from your controller; here are placeholders:
                    $studyThisWeek    = 12;    // hours
                    $studyStreak      = 5;     // days
                    $workThisWeek     = 20;    // hours
                    $tasksCompleted   = 8;     // count
                ?>
                <div id="schoolStats" class="mode-stats card my-4 p-3">
                    <h2 class="h5">📚 Weekly Study Summary</h2>
                    <p>Total study time this week: <strong><?= $studyThisWeek ?>hrs</strong></p>
                    <p>Current study streak: <strong><?= $studyStreak ?>days</strong></p>
                </div>

                <div id="workStats" class="mode-stats card my-4 p-3" style="display:none;">
                    <h2 class="h5">💼 Weekly Work Summary</h2>
                    <p>Total work time this week: <strong><?= $workThisWeek ?>hrs</strong></p>
                    <p>Tasks completed this week: <strong><?= $tasksCompleted ?></strong></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
  const switchEl = document.getElementById('flexSwitchCheckDefault');
  const schoolEl = document.getElementById('schoolStats');
  const workEl   = document.getElementById('workStats');

  if (!switchEl || !schoolEl || !workEl) {
    console.warn('profileStats.js: missing element(s)', { switchEl, schoolEl, workEl });
    return;
  }

  // arrow‑function
  const updateView = () => {
    if (switchEl.checked) {
      schoolEl.style.display = 'none';
      workEl.style.display   = 'block';
      console.log('Switched to WORK view');
    } else {
      workEl.style.display   = 'none';
      schoolEl.style.display = 'block';
      console.log('Switched to SCHOOL view');
    }
  };

  // initialize
  updateView();

  // arrow callback for the event listener
  switchEl.addEventListener('change', () => updateView());
});
    </script>

    <footer class="footer p-2 g-col-6">
        <p>&copy; 2025</p>
    </footer>
</body>
</html>
