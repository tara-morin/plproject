<?php
require_once "Database.php";

class StudyWithMeController {
    private $db;

    // Array example (for requirement: array usage)
    private $allowedStatuses = ['school', 'work'];

    public function __construct() {
        $this->db = new Database();
    }

    // 1. Show welcome (sign-up / login form)
    public function showWelcome() {
        include __DIR__ . '/../views/welcome.php';
    }

    // 2. Create profile (sign-up / login)
    public function createProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?command=welcome");
            exit();
        }

        $name     = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $status   = $_POST['status'] ?? '';

        // Example expression usage
        $minLength = 5 + 1; // 6

        // Validate
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

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: index.php?command=welcome");
            exit();
        }

        // Check if user already exists
        $existing = $this->db->query(
            "SELECT * FROM swm_users WHERE username = $1",
            $username
        );
        if ($existing !== false && count($existing) > 0) {
            // If user exists, verify password
            $row = $existing[0];
            if (!password_verify($password, $row['password'])) {
                $_SESSION['errors'] = ["Incorrect password for existing user."];
                header("Location: index.php?command=welcome");
                exit();
            }
            // Log them in
            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['name']     = $row['name'];
            $_SESSION['status']   = $row['status'];
        } else {
            // Create new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertResult = $this->db->query(
                "INSERT INTO swm_users (name, username, password, status)
                 VALUES ($1, $2, $3, $4)
                 RETURNING id",
                $name, $username, $hashedPassword, $status
            );

            if ($insertResult === false || count($insertResult) === 0) {
                $_SESSION['errors'] = ["Error creating user in database."];
                header("Location: index.php?command=welcome");
                exit();
            }

            $newId = $insertResult[0]['id'];
            $_SESSION['user_id']  = $newId;
            $_SESSION['username'] = $username;
            $_SESSION['name']     = $name;
            $_SESSION['status']   = $status;
        }

        // Example second state mechanism (cookie)
        setcookie('viewMode', 'light', time() + 3600);

        header("Location: index.php?command=dashboard");
    }

    // 3. Show dashboard
    public function showDashboard() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=welcome");
            exit();
        }
        // Show your existing index.html as the "dashboard"
        include __DIR__ . '/../views/index.html';
    }

    // 4. Log out
    public function logout() {
        session_destroy();
        // Clear cookie
        setcookie('viewMode', '', time() - 3600);
        header("Location: index.php?command=welcome");
    }

    // 5. Show tasks
    public function showTasks() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=welcome");
            exit();
        }
        include __DIR__ . '/../views/todo.html';
    }

    // 6. Create Task
    public function createTask() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?command=welcome");
            exit();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=welcome");
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

        if (!empty($errors)) {
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

        if ($res === false) {
            $_SESSION['task_errors'] = ["Database error inserting task."];
        }

        header("Location: index.php?command=showTasks");
    }

    // 7. Update Task
    public function updateTask() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?command=welcome");
            exit();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=welcome");
            exit();
        }

        $taskId    = $_POST['task_id'] ?? null;
        $completed = $_POST['completed'] ?? null;

        if (!$taskId) {
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

    // 8. Delete Task
    public function deleteTask() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?command=welcome");
            exit();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=welcome");
            exit();
        }

        $taskId = $_POST['task_id'] ?? null;
        if (!$taskId) {
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

    // 9. Return tasks in JSON
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
}
