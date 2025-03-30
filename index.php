<?php
session_start();
require_once "src/StudyWithMeController.php";

$controller = new StudyWithMeController();

$command = $_GET['command'] ?? 'welcome';

switch ($command) {
    case 'welcome':
        $controller->showWelcome();
        break;
    case 'create_profile':
        $controller->createProfile();
        break;
    case 'dashboard':
        $controller->showDashboard();  // Loads index.html
        break;
    case 'logout':
        $controller->logout();
        break;
    default:
        $controller->showWelcome();
        break;
}
