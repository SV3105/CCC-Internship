<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Frontend Intern Operations System</title>
  <link rel="stylesheet" href="css/reset.css" />
  <link rel="stylesheet" href="css/layout.css" />
  <link rel="stylesheet" href="css/components.css" />
</head>
<body>

  <header>
    <h1>Intern Operations System</h1>
    <nav>
      <button data-view="interns">Interns</button>
      <button data-view="tasks">Tasks</button>
      <button data-view="logs">Logs</button>
    </nav>
  </header>

  <main id="app"></main>

  <div id="loading" class="hidden">Loading...</div>
  <div id="error" class="hidden"></div>

  <script src="js/state.js"></script>
  <script src="js/fake-server.js"></script>
  <script src="js/validators.js"></script>
  <script src="js/rules-engine.js"></script>
  <script src="js/renderer.js"></script>
  <script src="js/app.js"></script>
</body>
</html>
