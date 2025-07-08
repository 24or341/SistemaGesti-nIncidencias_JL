<?php  ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title ?? 'Autenticación') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <style>
    .auth-form {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      background-color: #f8f9fa;
    }
  </style>
</head>
<body>
  <?= $content ?>
</body>
</html>
