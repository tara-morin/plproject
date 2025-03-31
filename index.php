<?php
/**
 * index.php
 * Study With Me (No Chat)
 * 
 * Team Members: [Your Names Here]
 * Deployed Link: https://cs4640.cs.virginia.edu/<yourID>/sprint3
 */

session_start();
require_once "src/StudyWithMeController.php";

$controller = new StudyWithMeController();
$command = $_GET['command'] ?? 'welcome';

switch ($command) {
    case 'login':
        include __DIR__ . '/views/login.php';
        break;
    case 'welcome':
        $controller->showWelcome();
        break;
    case 'create_profile':
        $controller->createProfile();
        break;
    case 'dashboard':
        $controller->showDashboard();
        break;
    case 'showTasks':
        $controller->showTasks();
        break;
    case 'createTask':
        $controller->createTask();
        break;
    case 'updateTask':
        $controller->updateTask();
        break;
    case 'deleteTask':
        $controller->deleteTask();
        break;
    // JSON endpoint
    case 'getTasksJSON':
        $controller->getTasksJSON();
        break;
    case 'logout':
        $controller->logout();
        break;
    default:
        $controller->showWelcome();
        break;
}
