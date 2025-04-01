<?php
/**
 * index.php
 * Study With Me (No Chat)
 * 
 * Team Members: [Your Names Here]
 * Deployed Link: https://cs4640.cs.virginia.edu/<yourID>/sprint3
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "src/StudyWithMeController.php";

$controller = new StudyWithMeController();
$command = $_GET['command'] ?? 'welcome';

switch ($command) {
    case 'login':
        $controller->login();
        break;
    // case 'welcome':
    //     $controller->showWelcome();
    //     break;
    case 'create_profile':
        $controller->createProfile();
        break;
    case 'dashboard':
        $controller->showDashboard();
        break;
    case 'focus':
        $controller->showFocus();
        break;
    case 'profile':
        $controller->showProfile();
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
    case 'setUsername':
        $controller->setUsername();
        break;
    default:
        $controller->login();
        break;
}
