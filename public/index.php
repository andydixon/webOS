<?php
// index.php — Main desktop UI
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>WebOS Desktop</title>
  <link rel="stylesheet" href="desktop.css">
  <link rel="stylesheet" href="themes/<?= htmlspecialchars($user['theme']) ?>.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
  <script>
    // Pass PHP user config into JS
    const userConfig = <?= json_encode($user) ?>;
  </script>
  <script src="desktop.js"></script>
</head>
<body>
  <!-- Desktop background and icons -->
  <div id="desktop"></div>

  <!-- Taskbar at the bottom -->
  <div id="taskbar">
    <div id="start-button">Start</div>
    <div id="taskbar-apps"></div>
    <div id="tray">
      <div id="tray-icons"></div>
      <span id="notifications"></span>
      <span id="clock"></span>
      <a href="logout.php" id="logout-link">Logout</a>
    </div>
  </div>

  <!-- Start menu (hidden by default) -->
  <div id="start-menu"></div>
</body>
</html>
