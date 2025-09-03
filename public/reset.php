<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usersIndex = json_decode(file_get_contents(__DIR__ . '/../config/users.json'), true)['users'];
    foreach ($usersIndex as $entry) {
        if ($entry['username'] === $_POST['username']) {
            $userFile = __DIR__ . '/../config/' . $entry['config'];
            $user = json_decode(file_get_contents($userFile), true);

            $newPass = bin2hex(random_bytes(4)); // 8-char new password
            $user['password'] = password_hash($newPass, PASSWORD_BCRYPT);
            file_put_contents($userFile, json_encode($user, JSON_PRETTY_PRINT));

            $msg = "Password reset for {$_POST['username']}. New password: $newPass";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Reset Password</title></head>
<body>
<form method="POST">
  <input name="username" placeholder="Username"><br>
  <button type="submit">Reset</button>
</form>
<?php if (!empty($msg)) echo "<p>$msg</p>"; ?>
</body>
</html>
