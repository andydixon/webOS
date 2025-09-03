<?php
session_start();
$usersIndex = json_decode(file_get_contents(__DIR__ . '/../config/users.json'), true)['users'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($usersIndex as $entry) {
        if ($entry['username'] === $_POST['username']) {
            $userFile = __DIR__ . '/../config/' . $entry['config'];
            $user = json_decode(file_get_contents($userFile), true);

            // Guest logs in without password
            if ($user['username'] === 'guest' || password_verify($_POST['password'], $user['password'])) {
                $_SESSION['user'] = $user;
                $_SESSION['user_file'] = $userFile;
                header("Location: index.php");
                exit;
            }
        }
    }
    $error = "Invalid credentials";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>WebOS Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #121212;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      color: #fff;
    }
    .login-box {
      background-color: #1e1e1e;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.7);
      width: 320px;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h3 class="text-center mb-3">WebOS Login</h3>
    <form method="POST">
      <div class="mb-3">
        <input name="username" class="form-control" placeholder="Username" required>
      </div>
      <div class="mb-3">
        <input name="password" type="password" class="form-control" placeholder="Password">
      </div>
      <button class="btn btn-primary w-100" type="submit">Login</button>
    </form>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger mt-3"><?= $error ?></div>
    <?php endif; ?>
    <div class="mt-3 text-center">
      <a href="reset.php" class="text-light">Forgot Password?</a>
    </div>
  </div>
</body>
</html>
ml>
