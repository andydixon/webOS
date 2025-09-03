<?php
session_start();
if (!isset($_SESSION['user_file'])) {
  echo "Not logged in";
  exit;
}
$userFile = $_SESSION['user_file'];
$user = json_decode(file_get_contents($userFile), true);
$apps = json_decode(file_get_contents(__DIR__ . '/../../config/apps.json'), true)['apps'];
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>App Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #121212;
      color: #eee;
    }
  </style>
</head>

<body class="p-3">
  <h5>App Store</h5>
  <ul class="list-group">
    <?php foreach ($apps as $app): ?>
      <li class="list-group-item bg-dark text-light d-flex justify-content-between align-items-center">
        <span><img src="../../<?= $app['icon'] ?>" width="16" class="me-2"><?= htmlspecialchars($app['title']) ?></span>
        <?php if (in_array($app['id'], $user['apps'])): ?>
          <span class="badge bg-success">Installed</span>
        <?php else: ?>
          <form method="POST" class="mb-0">
            <input type="hidden" name="id" value="<?= $app['id'] ?>">
            <button class="btn btn-sm btn-primary">Install</button>
          </form>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
</body>

</html>