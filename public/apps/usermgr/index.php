<?php
session_start();
if ($_SESSION['user']['username'] !== 'andy') {
  echo "Access denied";
  exit;
}
$usersIndexFile = __DIR__ . '/../../config/users.json';
$usersIndex = json_decode(file_get_contents($usersIndexFile), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'];
  if ($action === 'add') {
    $newUser = $_POST['username'];
    $userFile = __DIR__ . "/../../config/users/$newUser.json";
    $data = [
      "username" => $newUser,
      "password" => password_hash($_POST['password'], PASSWORD_BCRYPT),
      "theme" => "win95",
      "wallpaper" => "wallpapers/ocean.jpg",
      "apps" => ["notepad", "browser", "explorer", "settings", "store"],
      "fsRoot" => $newUser,
      "desktop" => []
    ];
    file_put_contents($userFile, json_encode($data, JSON_PRETTY_PRINT));
    $usersIndex['users'][] = ["username" => $newUser, "config" => "users/$newUser.json"];
    file_put_contents($usersIndexFile, json_encode($usersIndex, JSON_PRETTY_PRINT));
  } elseif ($action === 'delete') {
    $delUser = $_POST['username'];
    $usersIndex['users'] = array_values(array_filter($usersIndex['users'], fn($u) => $u['username'] !== $delUser));
    file_put_contents($usersIndexFile, json_encode($usersIndex, JSON_PRETTY_PRINT));
    @unlink(__DIR__ . "/../../config/users/$delUser.json");
  } elseif ($action === 'reset' && $username) {
    foreach ($usersIndex['users'] as $entry) {
      if ($entry['username'] === $username) {
        $file = __DIR__ . '/../../config/' . $entry['config'];
        if (file_exists($file)) {
          $data = json_decode(file_get_contents($file), true);
          $newPass = bin2hex(random_bytes(4)); // 8-char reset password
          $data['password'] = password_hash($newPass, PASSWORD_BCRYPT);
          file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
          $msg = "Password for $username reset to: $newPass";
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>User Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #121212;
      color: #eee;
    }
  </style>
</head>

<body class="p-3">
  <h5>User Manager</h5>
  <?php if (!empty($msg)): ?>
    <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <h6 class="mt-4">Change Password</h6>
  <div class="input-group w-75 mb-2">
    <input id="newpass" type="password" class="form-control" placeholder="New password">
    <button class="btn btn-warning btn-sm" onclick="changePassword()">Change</button>
  </div>

  <script>
    function changePassword() {
      const pw = document.getElementById("newpass").value;
      if (!pw) return alert("Enter a new password");
      fetch("../../user.php?action=password", {
        method: "POST",
        body: pw
      }).then(() => alert("Password updated!"));
    }
  </script>

  <div class="mb-3">
    <h6>Add User</h6>
    <form method="POST" class="row g-2">
      <input type="hidden" name="action" value="add">
      <div class="col"><input class="form-control" name="username" placeholder="Username"></div>
      <div class="col"><input class="form-control" name="password" type="password" placeholder="Password"></div>
      <div class="col-auto"><button class="btn btn-success btn-sm">Add</button></div>
    </form>
  </div>

  <div class="mb-3">
    <h6>Delete User</h6>
    <form method="POST" class="row g-2">
      <input type="hidden" name="action" value="delete">
      <div class="col"><input class="form-control" name="username" placeholder="Username"></div>
      <div class="col-auto"><button class="btn btn-danger btn-sm">Delete</button></div>
    </form>
  </div>

  <h6>Existing Users</h6>
  <ul class="list-group mb-3">
    <?php foreach ($usersIndex['users'] as $u): ?>
      <li class="list-group-item bg-dark text-light d-flex justify-content-between align-items-center">
        <?= htmlspecialchars($u['username']) ?>
        <form method="POST" class="mb-0 d-flex gap-1">
          <input type="hidden" name="username" value="<?= htmlspecialchars($u['username']) ?>">
          <button class="btn btn-sm btn-warning" name="action" value="reset">Reset PW</button>
          <button class="btn btn-sm btn-danger" name="action" value="delete">Delete</button>
        </form>
      </li>
    <?php endforeach; ?>
  </ul>

</body>

</html>