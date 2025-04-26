<?php
/**
 * index.php
 * Study With Me (No Chat)
 * 
 * Team Members: Tara Morin, Ninglan (Amy) Lei
 * Deployed Link: https://cs4640.cs.virginia.edu/tmn7vs/studybuddy
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "/students/tmn7vs/students/tmn7vs/private/studybuddy/src/StudyWithMeController.php";

$controller = new StudyWithMeController();
$command = $_GET['command'] ?? 'welcome';
 
switch ($command) {
    //login screen (this is also the default)
    case 'login':
        $controller->login();
        break;
    //profile creation
    case 'create_profile':
        $controller->createProfile();
        break;
    //redirect to home page
    case 'dashboard':
        $controller->showDashboard();
        break;
    //logic to reset a user's username
    case 'setName':
        $controller->setName();
        break;
    //redirect to focus page
    case 'focus':
        $controller->showFocus();
        break;
    //redirect to profile page
    case 'profile':
        $controller->showProfile();
        break;
    //switch statements related to user's tasks are all below:
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
    case 'logTaskTime':
        $controller->logTaskTime();
    // JSON endpoint
    case 'getTasksJSON':
        $controller->getTasksJSON();
        break;
    //logic for Focus screen study sessions
    case 'startStudy':
        $controller->startStudySession();
        break;
    case 'endStudy':
        $controller->endStudySession();
        break;
    case 'getLeaderboardJson':
        $controller->getLeaderboardJson();
        break;
    //log out
    case 'logout':
        $controller->logout();
        break;
    //default redirects to login page
    default:
        $controller->login();
        break;
}
