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
  <title>Explorer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #121212;
      color: #eee;
    }

    ul {
      list-style: none;
      padding-left: 20px;
    }

    li {
      cursor: pointer;
      margin: 2px 0;
    }
  </style>
</head>

<body class="p-3">
  <h5>File Explorer</h5>
  <div id="explorer"></div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    function loadDir(path, container) {
      fetch(`../../fs.php?action=list&path=${encodeURIComponent(path)}`)
        .then(r => r.json())
        .then(data => {
          const ul = $("<ul>");
          for (const key in data) {
            if (typeof data[key] === "string") {
              const li = $("<li>📄 " + key + "</li>");
              li.on("dblclick", () => {
                window.parent.postMessage({
                  type: "openFile",
                  path: `${path}/${key}`
                }, "*");
              });
              ul.append(li);
            } else {
              const li = $("<li>📁 " + key + "</li>");
              li.on("click", () => loadDir(`${path}/${key}`, li));
              ul.append(li);
            }
          }
          $(container).append(ul);
        });
    }
    loadDir("home", "#explorer");
  </script>
</body>

</html>