<?php
session_start();
if (!isset($_SESSION['user'])) {
  echo "Not logged in";
  exit;
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Browser</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #121212;
      color: #eee;
    }

    iframe {
      width: 100%;
      height: 80vh;
      border: 0;
      background: #fff;
    }
  </style>
</head>

<body class="p-3">
  <div class="mb-2">
    <input id="url" class="form-control d-inline-block w-75" value="https://example.com">
    <button class="btn btn-primary btn-sm" onclick="loadPage()">Go</button>
  </div>
  <iframe id="view"></iframe>

  <script>
    function loadPage() {
      document.getElementById("view").src = document.getElementById("url").value;
    }
    loadPage();
  </script>
</body>

</html>