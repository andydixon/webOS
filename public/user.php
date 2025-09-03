<?php
// user.php — Update user preferences (theme, wallpaper, password)

session_start();
if (!isset($_SESSION['user_file'])) {
    http_response_code(403);
    exit("Not logged in");
}

$userFile = $_SESSION['user_file'];
$userConfig = json_decode(file_get_contents($userFile), true);

$action = $_GET['action'] ?? '';

if ($action === 'theme') {
    $userConfig['theme'] = $_GET['theme'];
} elseif ($action === 'wallpaper') {
    $userConfig['wallpaper'] = file_get_contents("php://input");
} elseif ($action === 'password') {
    $newPass = file_get_contents("php://input");
    $userConfig['password'] = password_hash($newPass, PASSWORD_BCRYPT);
}

file_put_contents($userFile, json_encode($userConfig, JSON_PRETTY_PRINT));
$_SESSION['user'] = $userConfig;

echo "ok";
