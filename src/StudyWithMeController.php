<?php
require_once 'Config.php';
require_once 'Database.php';

class StudyWithMeController {
    private $db;

    private $allowedStatuses = ['school', 'work'];

    public function __construct() {
        $this->db = new Database();
    }

    // Login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            include __DIR__ ."/views/login.php";
            exit();
        } else if (!isset($_POST["username"])) {
            include __DIR__ . '/views/login.php';
            exit();
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $minLength = 5 + 1; 

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

        $existing = $this->db->query(
            "SELECT * FROM swm_users WHERE username = $1",
            $username
        );
        if (count($existing) > 0) {
            $row = $existing[0];
            if (!password_verify($password, $row['password'])) {
                $_SESSION['errors'] = "Incorrect password for existing user.";
                header("Location: index.php?command=login");
                exit();
            }
            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['name']     = $row['name'];
            $_SESSION['status']   = $row['status'];
        } else {
            $_SESSION['errors'] = ["Account not found for this username."];
        }
        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            unset($_SESSION['username']);
            unset($_SESSION['password']);
            header("Location: index.php?command=login");
            exit();
        }
        setcookie('viewMode', 'light', time() + 3600);

        header("Location: index.php?command=dashboard");
    }

    public function createProfile(){
        if (!isset($_POST["username"]) && !isset($_POST["conf_password"])){
            include __DIR__ . '/views/newuser.php';
            exit();
        }
        $name          = trim($_POST['name'] ?? '');
        $username      = trim($_POST['username'] ?? '');
        $password      = $_POST['password'] ?? '';
        $conf_password = $_POST['conf_password'] ?? '';
        $status        = $_POST['status'] ?? '';

        $errors = [];
        if (strlen($name) === 0) {
            $errors[] = "Name is required.";
        }
        if (strlen($username) < 6) {
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
        if (strcmp($password, $conf_password) != 0){
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

        $_SESSION['success'] = "Profile successfully created!";
        header("Location: index.php?command=login");
        exit();
    }

    public function showDashboard() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=login");
            exit();
        }
        
        $study_today= $this->getStudyTime();
        $next_task= $this->getNextTask();
        $task_info= json_decode($next_task,true);
        $task_title = $task_info['title']?? 'No upcoming task';
        include __DIR__ . '/views/home.php';
    }

    public function showFocus() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=login");
            exit();
        }
        $task_data = $_GET['task_data'] ?? null;
    
    if ($task_data) {
        $task_info = json_decode(urldecode($task_data), true);
        $_SESSION["task_info"]= $task_info;
    }
    else if (isset($_SESSION["task_info"])){
        $task_info= $_SESSION["task_info"];
    }

        include __DIR__ . '/views/focus.php';
    }
    
    public function showProfile() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=login");
            exit();
        }
        
        include __DIR__ . '/views/profile.php';
    }
    
    public function logout() {
        session_destroy();
        setcookie('viewMode', '', time() - 3600);
        header("Location: index.php?command=login");
    }

    public function showTasks() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=login");
            exit();
        }
        $tasks = $this->db->query(
            "SELECT * FROM swm_tasks WHERE user_id = $1 ORDER BY created_at DESC",
            $_SESSION['user_id']
        );
        include __DIR__ . '/views/todo.php';
    }

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
        // if ($timeSpent !== '' && !preg_match("/^\d{1,2}:\d{2}$/", $timeSpent)) {
        //     $errors[] = "Time must be in hh:mm format (e.g. 1:30).";
        // }

        if (count($errors) > 0) {
            $_SESSION['task_errors'] = $errors;
            header("Location: index.php?command=showTasks");
            exit();
        }

        $timeDecimal = $this->convertTimeToHours("0:00");

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

    public function updateTask() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?command=login");
            exit();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=login");
            exit();
        }
    
        $taskId = $_POST['task_id'] ?? null;
        if (empty($taskId)) {
            header("Location: index.php?command=showTasks");
            exit();
        }
    
        $title   = trim($_POST['title'] ?? '');
        $dueDate = $_POST['due_date'] ?? '';
        $time_spent= $this->convertTimeToHours($_POST['time_spent']);
        $completed = (isset($_POST['completed']) && $_POST['completed'] === 'on') ? 'TRUE' : 'FALSE';
    
        $this->db->query(
            "UPDATE swm_tasks
             SET title = $1, due_date = $2, completed = $3, time_spent= $4
             WHERE id = $5 AND user_id = $6",
            $title, $dueDate, $completed, $time_spent, $taskId, $_SESSION['user_id']
        );
    
        header("Location: index.php?command=showTasks");
        exit();
    }

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
        if (empty($taskId)) {
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

    // convert "hh:mm" -> decimal hours
    private function convertTimeToHours($timeString) {
        if (!preg_match("/^\d{1,2}:\d{2}$/", $timeString)) {
            return 0.0;
        }
        list($hh, $mm) = explode(':', $timeString);
        return (float)$hh + ((float)$mm / 60.0);
    }

    private function getStudyTime(){
        $currentDate = date('Y-m-d');
    
        $userId = $_SESSION["user_id"];
    
        $query = "
            SELECT start_time, end_time
            FROM swm_sessions
            WHERE user_id = $1
            AND DATE(start_time) = $2
            AND session_type = 'focus';
        ";
    
        $result = $this->db->query($query, $_SESSION["user_id"], $currentDate);
    
        $studyTime= 0.0;
    
       foreach($result as $row) {
            $startTime = strtotime($row['start_time']);
            $endTime = strtotime($row['end_time']);
    
            if ($startTime && $endTime) {
                $sessionDurationInSeconds = $endTime - $startTime;
                $studyTimeInHours += $sessionDurationInSeconds / 3600;
                // Convert total study time from hours to hours and minutes
                $studyTime= $this->formatTime($studyTimeInHours);
                return $studyTime;
            }
        }
        
    }

    public function formatTime($timespent){
        $hours = floor($timespent); 
        $minutes = round(($timespent - $hours) * 60); 
        $Time= $hours . ' hours ' . $minutes . ' minutes';
        return $Time;
    }
    

    private function getNextTask(){
    
        $query = "
            SELECT id, title, due_date, time_spent
            FROM swm_tasks
            WHERE user_id = $1
            AND completed = FALSE
            ORDER BY due_date ASC
        ";
    
        $tasks= $this->db->query($query, $_SESSION["user_id"]);
    
        if ($tasks) {
            $soonestDueDate = $tasks[0]['due_date'];
    
            $sameDueDateTasks = array_filter($tasks, function($task) use ($soonestDueDate) {
                return $task['due_date'] == $soonestDueDate;
            });
    
            usort($sameDueDateTasks, function($a, $b) {
                return $a['time_spent'] <=> $b['time_spent'];
            });
    
            $taskData = json_encode($sameDueDateTasks[0]);
            return $taskData;
        } else {
            return json_encode([]);
        }
    }
    public function logTaskTime(){
        $input = json_decode(file_get_contents('php://input'), true);
        $taskTime = intval($input['time']);
        $userID= intval($input['userID']);
        $taskID= intval($input['taskID']);
        // echo "printing some stuff out";
        // echo "time spent: "+$taskTime;
        // echo "userID is"+$userID;
        // echo "taskID is"+$taskID;
        $timeinMinutes= $taskTime/ 3600;
        $timeInHours = round($timeInMinutes/60, 2);
        $this->db->query(
            "UPDATE swm_tasks
             SET time_spent= $1
             WHERE id = $2 AND user_id = $3",
            $timeInHours, $taskID, $userID
        );

    }

    public function startStudySession(){
        $input = json_decode(file_get_contents('php://input'), true);
        $userID= intval($input['userID']);
        //sql query logic goes here
        $this->db->query(
            "INSERT INTO swm_sessions (user_id, session_type, start_time) 
              VALUES ($1, $2, $3)",
            $userID, "session", CURRENT_TIMESTAMP
        );
    }

    public function endStudySession(){
        $input = json_decode(file_get_contents('php://input'), true);
        $userID= intval($input['userID']);
        //sql query logic goes here
        $this->db->query(
            "SELECT id FROM swm_sessions 
                WHERE user_id = $1 AND end_time IS NULL 
                ORDER BY start_time DESC LIMIT 1;",
            $userID,
        );
        if (count($result) > 0) {
            $sessionID = $result[0]['id'];
            $this->db->query(
                "UPDATE swm_sessions 
                 SET end_time = CURRENT_TIMESTAMP, 
                     duration = EXTRACT(EPOCH FROM (CURRENT_TIMESTAMP - start_time)) / 60.0 
                 WHERE id = $1",
                [$sessionID]
            );
    }
}
    public function setName() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header("Location: index.php?command=login");
            exit();
        }
        $newName = trim($_POST['name'] ?? '');
        if (strlen($newName) === 0) {
            $_SESSION['errors'] = "Name cannot be empty.";
            header("Location: index.php?command=profile");
            exit();
        }
        $this->db->query(
            "UPDATE swm_users SET name = $1 WHERE id = $2",
            $newName, $_SESSION['user_id']
        );
        $_SESSION['name'] = $newName;
        header("Location: index.php?command=profile");
        exit();
    } 

    public function getLeaderboardJson() {
        header('Content-Type: application/json');
        
        // calculate a 7‑day window (today and the previous 6 days)
        $startDate = date('Y-m-d', strtotime('-6 days'));
        $endDate   = date('Y-m-d');
        
        // aggregate total session minutes per user
        $sql = "
          SELECT
            u.username,
            FLOOR(SUM(EXTRACT(EPOCH FROM (s.end_time - s.start_time))) / 60)::INT
              AS total_minutes
          FROM swm_sessions s
          JOIN swm_users    u ON s.user_id = u.id
          WHERE s.session_type = 'focus'
            AND DATE(s.start_time) BETWEEN \$1 AND \$2
          GROUP BY u.username
          ORDER BY total_minutes DESC
        ";
        $rows = $this->db->query($sql, $startDate, $endDate);
        
        echo json_encode($rows);
        exit();
    }
}