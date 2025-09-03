<?php
// fs.php — JSON-based filesystem API
// Allows apps to read/write files within a user's virtual FS

session_start();
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit("Not logged in");
}

$user = $_SESSION['user'];
$fsRoot = __DIR__ . '/../storage/fs/' . $user['fsRoot'] . '.json';

// Initialise FS file if it doesn't exist
if (!file_exists($fsRoot)) {
    file_put_contents($fsRoot, json_encode(["home" => []], JSON_PRETTY_PRINT));
}
$fs = json_decode(file_get_contents($fsRoot), true);

$action = $_GET['action'] ?? '';
$path   = $_GET['path'] ?? '';

function &getNode(&$fs, $pathParts) {
    $node = &$fs;
    foreach ($pathParts as $p) {
        if (!isset($node[$p])) $node[$p] = [];
        $node = &$node[$p];
    }
    return $node;
}

switch ($action) {
    case 'list':
        $parts = explode('/', $path);
        $node = getNode($fs, $parts);
        header('Content-Type: application/json');
        echo json_encode($node);
        break;

    case 'read':
        $parts = explode('/', $path);
        $fileName = array_pop($parts);
        $dir = getNode($fs, $parts);
        echo $dir[$fileName] ?? '';
        break;

    case 'write':
        $parts = explode('/', $path);
        $fileName = array_pop($parts);
        $dir = &getNode($fs, $parts);
        $data = file_get_contents("php://input");
        $dir[$fileName] = $data;
        file_put_contents($fsRoot, json_encode($fs, JSON_PRETTY_PRINT));
        echo "ok";
        break;

    case 'mkdir':
        $parts = explode('/', $path);
        getNode($fs, $parts);
        file_put_contents($fsRoot, json_encode($fs, JSON_PRETTY_PRINT));
        echo "ok";
        break;

    case 'delete':
        $parts = explode('/', $path);
        $fileName = array_pop($parts);
        $dir = &getNode($fs, $parts);
        unset($dir[$fileName]);
        file_put_contents($fsRoot, json_encode($fs, JSON_PRETTY_PRINT));
        echo "ok";
        break;

    default:
        echo "Unknown action";
}
