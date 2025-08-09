<?php
  // Configuración del archivo register.php
  // Realizado por: Jorge Enrique Castañeda Centurión
  // Fecha: 2025-09-08
  $title = 'Crear Cuenta'; // Título de la página
?>

<section class="auth-form"> // Sección para el formulario de autenticación
  <div class="card p-4" style="min-width: 320px; max-width: 450px; width: 100%;"> // Contenedor del formulario con estilos responsivos
    <h2 class="text-center mb-3">Registro de Administrador</h2> // Título del formulario

    <?php if (!empty($success)): ?> // Mensaje de éxito si se ha registrado correctamente
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div> // Muestra el mensaje de éxito si existe
    <?php elseif (!empty($error)): ?> // Mensaje de error si hay un problema al registrar
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div> // Muestra el mensaje de error si existe
    <?php endif; ?>

    <form method="post" action="<?= url('auth/register') ?>"> // Formulario para registrar un nuevo administrador
      <input type="hidden" name="role" value="administrador"> // Campo oculto para definir el rol del usuario como administrador
      <div class="mb-3"> // Campo para el nombre del administrador
        <label for="nombre" class="form-label">Nombre</label> // Etiqueta para el campo de nombre
        <input
          type="text"
          id="nombre"
          name="nombre"
          class="form-control"
          required
        > // Campo de entrada para el nombre, requerido
      </div>
      <div class="mb-3"> // Campo para el apellido del administrador
        <label for="apellido" class="form-label">Apellido</label> // Etiqueta para el campo de apellido
        <input
          type="text"
          id="apellido"
          name="apellido"
          class="form-control"
          required
        > // Campo de entrada para el apellido, requerido
      </div>
      <div class="mb-3"> // Campo para el correo electrónico del administrador
        <label for="email" class="form-label">Correo electrónico</label> // Etiqueta para el campo de correo electrónico
        <input
          type="email"
          id="email"
          name="email"
          class="form-control"
          required
        > // Campo de entrada para el correo electrónico, requerido
      </div>
      <div class="mb-3"> // Campo para la contraseña del administrador
        <label for="password" class="form-label">Contraseña</label> // Etiqueta para el campo de contraseña
        <input
          type="password"
          id="password"
          name="password"
          class="form-control"
          required
        > // Campo de entrada para la contraseña, requerido
      </div> // Campo para confirmar la contraseña del administrador
      <button type="submit" class="btn btn-success w-100">
        Registrar
      </button> // Botón para enviar el formulario de registro
    </form> // Fin del formulario de registro

    <p class="text-center mt-3"> // Enlace para volver al inicio de sesión
      ← <a href="<?= url('auth/login') ?>">Volver al inicio de sesión</a> // Texto que permite al usuario regresar a la página de inicio de sesión
    </p> // Fin del párrafo de enlace
  </div> // Fin del contenedor del formulario
</section> // Fin de la sección del formulario de autenticación