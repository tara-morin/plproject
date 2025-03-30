<?php
require_once 'src/Config.php';
require_once 'src/Database.php';

$db = new Database();

$queries = [
    "DROP TABLE IF EXISTS swm_focus_blocklist",
    "DROP TABLE IF EXISTS swm_sessions",
    "DROP TABLE IF EXISTS swm_tasks",
    "DROP TABLE IF EXISTS swm_users",

    // swm_users
    "CREATE TABLE swm_users (
        id SERIAL PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        status VARCHAR(10) CHECK (status IN ('school', 'work')) NOT NULL,
        days_member INTEGER DEFAULT 0,
        hours_studied FLOAT DEFAULT 0,
        tasks_completed INTEGER DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    // swm_tasks
    "CREATE TABLE swm_tasks (
        id SERIAL PRIMARY KEY,
        user_id INTEGER NOT NULL REFERENCES swm_users(id),
        title VARCHAR(255) NOT NULL,
        due_date DATE,
        time_spent FLOAT DEFAULT 0,
        completed BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    // swm_sessions
    "CREATE TABLE swm_sessions (
        id SERIAL PRIMARY KEY,
        user_id INTEGER NOT NULL REFERENCES swm_users(id),
        session_type VARCHAR(10) CHECK (session_type IN ('focus', 'break')) NOT NULL,
        start_time TIMESTAMP,
        end_time TIMESTAMP,
        duration FLOAT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    // swm_focus_blocklist
    "CREATE TABLE swm_focus_blocklist (
        id SERIAL PRIMARY KEY,
        user_id INTEGER NOT NULL REFERENCES swm_users(id),
        url VARCHAR(255) NOT NULL
    )"
];

foreach ($queries as $query) {
    $result = $db->query($query);
    if ($result === false) {
        echo "Error executing query: " . htmlspecialchars($query) . "<br>";
        exit();
    }
}

echo "Study With Me database initialized successfully!";
?>
