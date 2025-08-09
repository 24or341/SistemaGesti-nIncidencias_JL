<?php
  // Configuración del archivo layout.php
  // Realizado por: Jorge Enrique Castañeda Centurión
  // Fecha: 2025-09-08
  /** @var string $content */
  /** @var string|null $title */
?>
<!DOCTYPE html> // Declaración del tipo de documento HTML5
<html lang="es"> // Establece el idioma del documento como español
<head>
  <meta charset="UTF-8"> // Define la codificación de caracteres como UTF-8
  <title><?= htmlspecialchars($title ?? 'Autenticación') ?></title> // Título de la página, con un valor por defecto si no se proporciona
  <meta name="viewport" content="width=device-width, initial-scale=1"> // Configuración para que la página sea responsiva en dispositivos móviles
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet"
  > // Enlace a la hoja de estilos de Bootstrap 5.3.2
  <style>
    .auth-form {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      background-color: #f8f9fa;
    }
  </style> // Estilos personalizados para centrar el formulario de autenticación
</head> // Cierre de la sección head 
<body> // Inicio del cuerpo del documento
  <?= $content ?> // Inserción del contenido dinámico de la página
</body> // Cierre del cuerpo del documento
</html> // Cierre del documento HTML