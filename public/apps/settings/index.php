<?php
session_start();
if (!isset($_SESSION['user'])) {
  echo "Not logged in";
  exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Settings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #121212;
      color: #eee;
    }
  </style>
</head>

<body class="p-3">
  <h5>Appearance Settings</h5>
  <form id="themeForm" class="mb-3">
    <?php foreach (["win95", "xp", "modern", "material"] as $t): ?>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="theme" value="<?= $t ?>" <?= $user['theme'] === $t ? "checked" : "" ?>>
        <label class="form-check-label"><?= strtoupper($t) ?></label>
      </div>
    <?php endforeach; ?>
    <button class="btn btn-primary btn-sm mt-2">Save Theme</button>
  </form>

  <h5>Wallpaper</h5>
  <div class="input-group w-75">
    <input id="wallpaper" class="form-control" value="<?= htmlspecialchars($user['wallpaper']) ?>">
    <button class="btn btn-success btn-sm" onclick="saveWallpaper()">Save</button>
  </div>

  <script>
    document.getElementById("themeForm").onsubmit = function(e) {
      e.preventDefault();
      const theme = document.querySelector("input[name=theme]:checked").value;
      fetch("../../user.php?action=theme&theme=" + theme).then(() => window.parent.location.reload());
    };

    function saveWallpaper() {
      const wp = document.getElementById("wallpaper").value;
      fetch("../../user.php?action=wallpaper", {
        method: "POST",
        body: wp
      }).then(() => window.parent.location.reload());
    }
  </script>
</body>

</html>