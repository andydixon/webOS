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
  <title>Notepad</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #121212;
      color: #eee;
    }

    #editor {
      width: 100%;
      height: 70vh;
      background: #1e1e1e;
      color: #eee;
      font-family: monospace;
    }
  </style>
</head>

<body class="p-3">
  <div class="mb-2">
    <input id="filepath" class="form-control d-inline-block w-50" placeholder="home/new.txt">
    <button class="btn btn-primary btn-sm" onclick="openFile()">Open</button>
    <button class="btn btn-success btn-sm" onclick="saveFile()">Save</button>
  </div>
  <textarea id="editor" class="form-control"></textarea>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    function openFile() {
      const path = $("#filepath").val();
      fetch(`../../fs.php?action=read&path=${encodeURIComponent(path)}`)
        .then(r => r.text())
        .then(t => $("#editor").val(t));
    }

    function saveFile() {
      const path = $("#filepath").val();
      fetch(`../../fs.php?action=write&path=${encodeURIComponent(path)}`, {
        method: "POST",
        body: $("#editor").val()
      }).then(() => alert("Saved"));
    }
  </script>
</body>

</html>