// desktop.js — Window manager and desktop behaviour

$(function() {
  // Load apps from desktop.php
  $.getJSON("desktop.php", function(apps) {
    window.appsConfig = apps;
    renderStartMenu(apps);
    renderDesktopIcons(userConfig.desktop);
  });

  // Clock in the tray
  setInterval(() => {
    $("#clock").text(new Date().toLocaleTimeString());
  }, 1000);

  // Start button toggles menu
  $("#start-button").on("click", function() {
    $("#start-menu").toggle();
  });

  // Hide start menu if you click elsewhere
  $(document).on("click", function(e) {
    if (!$(e.target).closest("#start-button, #start-menu").length) {
      $("#start-menu").hide();
    }
  });

  // Keyboard shortcuts
  $(document).on("keydown", function(e) {
    if (e.altKey && e.key === "Tab") {
      cycleWindows();
      e.preventDefault();
    }
    if (e.ctrlKey && e.key === "Escape") {
      $("#start-menu").toggle();
      e.preventDefault();
    }
  });
});

// Render the start menu with app list
function renderStartMenu(apps) {
  const menu = $("#start-menu").empty();
  apps.forEach(app => {
    const item = $(`<div class="menu-item"><img src="${app.icon}" width="16"> ${app.title}</div>`);
    item.on("click", () => openApp(app));
    menu.append(item);
  });
}

// Render desktop icons from user config
function renderDesktopIcons(icons) {
  $("#desktop").empty();
  icons.forEach(icon => {
    const el = $(`<div class="desktop-icon"></div>`);
    if (icon.type === "app") {
      const app = appsConfig.find(a => a.id === icon.id);
      el.html(`<img src="${app.icon}" width="32"><br>${app.title}`);
      el.on("dblclick", () => openApp(app));
    } else if (icon.type === "file") {
      const name = icon.path.split("/").pop();
      el.html(`<img src="icons/file.png" width="32"><br>${name}`);
      el.on("dblclick", () => {
        openApp({id:"notepad", entry:"apps/notepad/index.php", title:"Notepad"}, {path:icon.path});
      });
    }
    el.css({left:icon.x+"px", top:icon.y+"px"});
    $("#desktop").append(el);
    el.draggable({
      stop: function(ev, ui) {
        icon.x = ui.position.left;
        icon.y = ui.position.top;
        saveDesktop();
      }
    });
  });
}

// Save desktop icon positions back to server
function saveDesktop() {
  $.ajax({
    url: "state.php?desktop=1",
    method: "POST",
    data: JSON.stringify({desktop: userConfig.desktop})
  });
}

function openApp(app, args = {}) {
  const winWidth = app.width || 400;
  const winHeight = app.height || 300;

  const win = $(`
    <div class="window">
      <div class="window-titlebar">
        <span>${app.title}</span>
        <div class="window-controls">
          <button class="minimise">🗕</button>
          <button class="maximise">🗖</button>
          <button class="close">✕</button>
        </div>
      </div>
      <iframe src="${app.entry}" style="width:100%;height:calc(100% - 28px);border:0"></iframe>
    </div>
  `);

  win.css({
    left: 100 + Math.random() * 50,
    top: 100 + Math.random() * 50,
    width: winWidth,
    height: winHeight,
    position: "absolute",
    zIndex: ++window.topZ,
  });

  $("#desktop").append(win);

  // Enable drag & resize
  win.draggable({ handle: ".window-titlebar" }).resizable({
    minWidth: 300,
    minHeight: 200,
  });

  // --- Close button
  win.find(".close").on("click", () => {
    win.remove();
    btn.remove();
  });

  // --- Maximise / Restore button
  let isMaximised = false;
  let prevPos = {};
  win.find(".maximise").on("click", () => {
    if (!isMaximised) {
      prevPos = {
        left: win.css("left"),
        top: win.css("top"),
        width: win.css("width"),
        height: win.css("height"),
      };
      win.css({
        left: 0,
        top: 0,
        width: $("#desktop").width(),
        height: $("#desktop").height(),
      });
      isMaximised = true;
      win.find(".maximise").text("🗗"); // Restore symbol
    } else {
      win.css(prevPos);
      isMaximised = false;
      win.find(".maximise").text("🗖"); // Maximise symbol
    }
  });

  // --- Minimise button
  win.find(".minimise").on("click", () => {
    win.hide();
  });

  // --- Taskbar button
  const btn = $(`<button class="task-btn">${app.title}</button>`);
  $("#taskbar-apps").append(btn);

  btn.on("click", () => {
    if (win.is(":visible")) {
      win.hide();
    } else {
      win.show().css("z-index", ++window.topZ);
    }
  });

  // --- Bring to front on click
  win.on("mousedown", () => win.css("z-index", ++window.topZ));
}




// Cycle through open windows (Alt+Tab)
function cycleWindows() {
  const wins = $(".window");
  if (!wins.length) return;
  const topWin = wins.last();
  wins.first().before(topWin);
}
