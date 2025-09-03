<?php
// desktop.php — returns the apps a user is allowed to use

session_start();
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$user = $_SESSION['user'];

// Load all available apps from config
$appsFile = __DIR__ . '/../config/apps.json';
if (!file_exists($appsFile)) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(["error" => "apps.json missing"]);
    exit;
}

$appsData = json_decode(file_get_contents($appsFile), true);
if (!is_array($appsData) || !isset($appsData['apps'])) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(["error" => "Invalid apps.json"]);
    exit;
}

// Filter apps by what this user has installed
$userApps = [];
foreach ($appsData['apps'] as $app) {
    if (in_array($app['id'], $user['apps'])) {
        $userApps[] = $app;
    }
}

// Send back JSON
header('Content-Type: application/json');
echo json_encode($userApps, JSON_PRETTY_PRINT);
