<?php
// state.php — Saves and loads desktop/window state

session_start();
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit("Not logged in");
}

$user = $_SESSION['user'];
$stateFile = __DIR__ . '/../storage/states/' . $user['username'] . '.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = file_get_contents("php://input");
    file_put_contents($stateFile, $data);
    echo "ok";
    exit;
}

if (file_exists($stateFile)) {
    header('Content-Type: application/json');
    echo file_get_contents($stateFile);
} else {
    echo "{}";
}
