<?php
// Configuración del archivo layout.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
/** @var string $content */
/** @var string|null $title */
?>

<!DOCTYPE html> // Layout principal del panel de administración
<html lang="es"> // Idioma del documento
  <head> // Encabezado del documento HTML
    <meta charset="UTF-8"> // Codificación de caracteres
    <title><?= htmlspecialchars($title ?? 'Panel') ?></title> // Título de la página, con protección contra XSS
    <meta name="viewport" content="width=device-width, initial-scale=1"> // Configuración de la vista para dispositivos móviles

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    > // Enlace a Bootstrap CSS

    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
      rel="stylesheet"
    > // Enlace a Font Awesome

    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap" rel="stylesheet"> // Enlace a Google Fonts

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script> // Enlace a Chart.js

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
  </style> // Estilos personalizados para el layout

  </head> // Cierre del encabezado
  <body> // Cuerpo del documento HTML
    <?php include __DIR__ . '/partials/sidebar.php'; ?> // Inclusión de la barra lateral

    <div class="main-content"> // Contenido principal del documento
      <?php include __DIR__ . '/partials/header.php'; ?> // Inclusión del encabezado

      <main class="container py-4"> // Contenedor principal para el contenido
        <?= $content ?> // Contenido dinámico de la página
      </main> // Cierre del contenedor principal

      <?php include __DIR__ . '/partials/footer.php'; ?> // Inclusión del pie de página
    </div> // Cierre del contenido principal
  </body> // Cierre del cuerpo del documento
</html> // Cierre del documento HTML