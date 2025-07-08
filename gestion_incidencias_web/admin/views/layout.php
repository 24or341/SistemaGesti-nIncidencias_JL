<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Panel') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    >

    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
      rel="stylesheet"
    >

    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>

  <style>
    body {
      font-family: 'Bahnschrift Condensed', 'Barlow Condensed', sans-serif;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      height: 100vh;
      overflow-y: auto;
      background-color: #2c3e50;
      color: white;
      z-index: 1000;
      transition: width 0.3s ease;
    }

    .sidebar.collapsed {
      width: 70px;
    }

    .main-content {
      margin-left: 250px;
      background-color: #e3f2fd;
      min-height: 100vh;
      transition: margin-left 0.3s ease;
    }

    .main-content.collapsed {
      margin-left: 70px;
    }

    .topbar {
      position: sticky;
      top: 0;
      background-color: #bbdefb;
      border-bottom: 1px solid #90caf9;
      padding: 12px 20px;
      z-index: 900;
    }

    footer {
      background-color: #bbdefb;
      text-align: center;
      padding: 12px;
      color: #0d47a1;
    }
  </style>

  </head>
  <body>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="main-content">
      <?php include __DIR__ . '/partials/header.php'; ?>

      <main class="container py-4">
        <?= $content ?>
      </main>

      <?php include __DIR__ . '/partials/footer.php'; ?>
    </div>
  </body>
</html>