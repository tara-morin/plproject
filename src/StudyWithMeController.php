<?php
require_once "src/Database.php";

class StudyWithMeController {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function showWelcome() {
        include __DIR__ . '/../views/welcome.php';

    public function createProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?command=welcome");
            exit();
        }

        $name     = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $status   = $_POST['status'] ?? '';

        $errors = [];
        if (empty($name)) $errors[] = "Name is required.";
        if (empty($username)) $errors[] = "Username is required.";
        if (empty($password)) $errors[] = "Password is required.";
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) $errors[] = "Username can only contain letters, numbers, and underscores.";
        if (!in_array($status, ['school', 'work'])) $errors[] = "Invalid status selected.";

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: index.php?command=welcome");
            exit();
        }

        // Check for duplicate
        $existing = $this->db->query(
            "SELECT * FROM swm_users WHERE username = $1",
            $username
        );

        if ($existing && count($existing) > 0) {
            $_SESSION['errors'] = ["Username already taken."];
            header("Location: index.php?command=welcome");
            exit();
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $result = $this->db->query(
            "INSERT INTO swm_users (name, username, password, status)
             VALUES ($1, $2, $3, $4)
             RETURNING id",
            $name, $username, $hashed, $status
        );

        if ($result === false || count($result) === 0) {
            $_SESSION['errors'] = ["Error saving profile."];
            header("Location: index.php?command=welcome");
            exit();
        }

        $_SESSION['user_id']  = $result[0]['id'];
        $_SESSION['username'] = $username;
        $_SESSION['name']     = $name;
        $_SESSION['status']   = $status;

        header("Location: index.php?command=dashboard");
    }

    public function showDashboard() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?command=welcome");
            exit();
        }

        include __DIR__ . '/../views/index.html';
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?command=welcome");
    }
}
