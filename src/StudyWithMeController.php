<?php
require_once 'Config.php';
require_once 'Database.php';

class StudyWithMeController {
    private $db;

    // Array example (for requirement: array usage)
    private $allowedStatuses = ['school', 'work'];

    public function __construct() {
        $this->db = new Database();
    }

    // 1. Show welcome (sign-up / login form)
    // public function showWelcome() {
    //     include __DIR__ . '/../views/welcome.php';
    // }

    // 2. Login
    public function login() {
        //display template if no POST information is avaliable
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            include __DIR__ . '/../views/login.php';
            exit();
        }
        else if (!isset($_POST["username"])){
            include __DIR__ . '/../views/login.php';
            exit();
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Example expression usage
        $minLength = 5 + 1; // 6

        // Validate
        $errors = [];
        if (strlen($username) < $minLength) {
            $errors[] = "Username must be at least 6 characters.";
        }
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
            $errors[] = "Username can only contain letters, numbers, and underscores.";
        }
        if (strlen($password) === 0) {
            $errors[] = "Password is required.";
        }


        // Check if user already exists
        $existing = $this->db->query(
            "SELECT * FROM swm_users WHERE username = $1",
            $username
        );
        if (count($existing) > 0) {
            // If user exists, verify password
            $row = $existing[0];
            if (!password_verify($password, $row['password'])) {
                $_SESSION['errors'] = "Incorrect password for existing user.";
                header("Location: index.php?command=login");
                exit();
            }
            // Log them in
            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['name']     = $row['name'];
            $_SESSION['status']   = $row['status'];
        }
        else{
            $_SESSION['errors'] = ["Account not found for this username."];
        }
        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            unsset($_SESSION['username']);
            unsset($_SESSION['password']);
            header("Location: index.php?command=login");
            exit();
        }
        // Example second state mechanism (cookie)
        setcookie('viewMode', 'light', time() + 3600);

        header("Location: index.php?command=dashboard");
    }

    //3. if the user doesn't exist, let them make a profile
    public function createProfile(){
        //if the post variables aren't set, it means the user is loading this page for the first time. 
        if (!isset($_POST["username"]) && !isset($_POST["conf_password"])){
            include __DIR__ . '/../views/newuser.php';
            exit();
        }
        $name     = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $conf_password = $_POST['conf_password'] ?? '';
        $status   = $_POST['status'] ?? '';

        $errors = [];
        if (strlen($name) === 0) {
            $errors[] = "Name is required.";
        }
        if (strlen($username) < $minLength) {
            $errors[] = "Username must be at least 6 characters.";
        }
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
            $errors[] = "Username can only contain letters, numbers, and underscores.";
        }
        if (strlen($password) === 0) {
            $errors[] = "Password is required.";
        }
        if (!in_array($status, $this->allowedStatuses)) {
            $errors[] = "Invalid status selected.";
        }
        if (strcmp($password, $conf_password)!=0){
            $errors[] = "Passwords must match.";
        }

        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            header("Location: index.php?command=create_profile");
            exit();
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertResult = $this->db->query(
            "INSERT INTO swm_users (name, username, password, status)
            VALUES ($1, $2, $3, $4)
            RETURNING id",
            $name, $username, $hashedPassword, $status
        );

        if (count($insertResult) === 0) {
            $_SESSION['errors'] = ["Error creating user in database."];
            header("Location: index.php?command=create_profile");
            exit();
        }

        // $newId = $insertResult[0]['id'];
        // $_SESSION['user_id']  = $newId;
        // $_SESSION['username'] = $username;
        // $_SESSION['name']     = $name;
        // $_SESSION['status']   = $status;

        $_SESSION['success']= "Profile successfully created!";
        header("Location: index.php?command=login");
        exit();
    }

    // 4. Show dashboard
    public function showDashboard() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=login");
            exit();
        }
        // Show your existing index.html as the "dashboard"
        include __DIR__ . '/../views/home.php';
        $study_today= $this->getStudyTime();
        $next_task= $this->getNextTask();
        $task_info= json_decode($next_task,true);
        $title = $task_info['title'];
    }

    // 5. Log out
    public function logout() {
        session_destroy();
        // Clear cookie
        setcookie('viewMode', '', time() - 3600);
        header("Location: index.php?command=login");
    }

    // 6. Show tasks
    public function showTasks() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=login");
            exit();
        }
        include __DIR__ . '/../views/todo.html';
    }

    // 7. Create Task
    public function createTask() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?command=login");
            exit();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=login");
            exit();
        }

        $title     = trim($_POST['title'] ?? '');
        $dueDate   = $_POST['due_date'] ?? '';
        $timeSpent = $_POST['time_spent'] ?? '0';

        $errors = [];
        if ($title === '') {
            $errors[] = "Task title is required.";
        }
        if ($timeSpent !== '' && !preg_match("/^\d{1,2}:\d{2}$/", $timeSpent)) {
            $errors[] = "Time must be in hh:mm format (e.g. 1:30).";
        }

        if (count($errors) > 0) {
            $_SESSION['task_errors'] = $errors;
            header("Location: index.php?command=showTasks");
            exit();
        }

        $timeDecimal = $this->convertTimeToHours($timeSpent);

        $res = $this->db->query(
            "INSERT INTO swm_tasks (user_id, title, due_date, time_spent)
             VALUES ($1, $2, $3, $4)",
            $_SESSION['user_id'], $title, $dueDate, $timeDecimal
        );

        if (count($res) == 0) {
            $_SESSION['task_errors'] = ["Database error inserting task."];
        }

        header("Location: index.php?command=showTasks");
    }

    // 8. Update Task
    public function updateTask() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?command=login");
            exit();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=login");
            exit();
        }

        $taskId    = $_POST['task_id'] ?? null;
        $completed = $_POST['completed'] ?? null;

        if (count($taskId) == 0) {
            header("Location: index.php?command=showTasks");
            exit();
        }

        $isCompleted = ($completed === 'on') ? 'TRUE' : 'FALSE';
        $this->db->query(
            "UPDATE swm_tasks
             SET completed = $1
             WHERE id = $2 AND user_id = $3",
            $isCompleted, $taskId, $_SESSION['user_id']
        );

        header("Location: index.php?command=showTasks");
    }

    // 9. Delete Task
    public function deleteTask() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?command=login");
            exit();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=login");
            exit();
        }

        $taskId = $_POST['task_id'] ?? null;
        if (count($taskId) == 0) {
            header("Location: index.php?command=showTasks");
            exit();
        }

        $this->db->query(
            "DELETE FROM swm_tasks WHERE id = $1 AND user_id = $2",
            $taskId,
            $_SESSION['user_id']
        );

        header("Location: index.php?command=showTasks");
    }

    // 10. Return tasks in JSON
    public function getTasksJSON() {
        header("Content-Type: application/json");

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(["error" => "Not logged in."]);
            return;
        }

        $rows = $this->db->query(
            "SELECT * FROM swm_tasks
             WHERE user_id = $1
             ORDER BY created_at DESC",
            $_SESSION['user_id']
        );

        echo json_encode($rows);
    }

    // Helper function: convert "hh:mm" -> decimal hours
    private function convertTimeToHours($timeString) {
        if (!preg_match("/^\d{1,2}:\d{2}$/", $timeString)) {
            return 0.0;
        }
        list($hh, $mm) = explode(':', $timeString);
        return (float)$hh + ((float)$mm / 60.0);
    }

    private function getStudyTime(){
        //get the date
        $currentDate = date('Y-m-d'); // Format: YYYY-MM-DD
    
        // Step 2: Get the user ID from the session
        $userId = $_SESSION["user_id"];
    
        //get all sessions for the current user, if it was today
        $query = "
            SELECT start_time, end_time
            FROM swm_sessions
            WHERE user_id = $1
            AND DATE(start_time) = $2
            AND session_type = 'focus';
        ";
    
        //query
        $result = $this->db->query($query, $_SESSION["user_id"], $currentDate);
    
        //instantiate study time
        $studyTime= 0.0;
    
       foreach($result as $row) {
            $startTime = strtotime($row['start_time']);
            $endTime = strtotime($row['end_time']);
    
            //add each to total
            if ($startTime && $endTime) {
                $sessionDurationInSeconds = $endTime - $startTime;  // Duration in seconds
                $studyTimeInHours += $sessionDurationInSeconds / 3600;  // Convert duration to hours and add to total
            }
        }
    
        // Convert total study time from hours to hours and minutes
        $hours = floor($studyTimeInHours); 
        $minutes = round(($studyTimeInHours - $hours) * 60); 
        $studyTime= $hours . ' hours ' . $minutes . ' minutes';
        return $studyTime;
    }
    

    private function getNextTask(){
    
        $query = "
            SELECT id, title, due_date, time_spent
            FROM swm_tasks
            WHERE user_id = $1
            AND completed = FALSE
            ORDER BY due_date ASC
        ";
    
        // Prepare the query using PDO
        $tasks= $this->db->query($query, $_SESSION["user_id"]);
    
        if ($tasks) {
            // Step 4: Find the soonest due date
            $soonestDueDate = $tasks[0]['due_date'];
    
            // Step 5: Filter all tasks that have the same soonest due date
            $sameDueDateTasks = array_filter($tasks, function($task) use ($soonestDueDate) {
                return $task['due_date'] == $soonestDueDate;
            });
    
            // Step 6: Sort the tasks with the same due date by time spent (ascending)
            usort($sameDueDateTasks, function($a, $b) {
                return $a['time_spent'] <=> $b['time_spent'];
            });
    
            // Step 7: Encode the task with the least time spent into JSON
            $taskData = json_encode($sameDueDateTasks[0]);
    
            // Step 8: Return the task data
            return $taskData;
        } else {
            // If no tasks are found, return an empty response
            return json_encode([]);
        }
    }
    
}
