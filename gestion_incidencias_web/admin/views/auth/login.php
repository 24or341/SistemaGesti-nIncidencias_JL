<?php
  // Configuración del archivo login.php
  // Realizado por: Jorge Enrique Castañeda Centurión
  // Fecha: 2025-09-08
  /** @var string|null $title */
  $title = $title ?? 'Iniciar Sesión'; // Título de la página, con un valor por defecto si no se proporciona
?>

<section class="auth-form"> // Sección para el formulario de autenticación
  <div class="card p-4" style="min-width: 320px; max-width: 400px; width: 100%;"> // Tarjeta para el formulario de inicio de sesión
    <h2 class="text-center mb-3">Administrador</h2> // Título del formulario

    <?php if (!empty($error)): ?> // Verifica si hay un mensaje de error
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div> // Muestra el mensaje de error si existe
    <?php endif; ?> // Fin de la verificación del error

    <form method="post" action="<?= url('auth/login') ?>"> // Formulario de inicio de sesión
      <div class="mb-3"> // Campo para el correo electrónico
        <label for="email" class="form-label">Correo electrónico</label> // Etiqueta para el campo de correo electrónico
        <input
          type="email"
          id="email"
          name="email"
          class="form-control"
          required
        > // Campo de entrada para el correo electrónico, requerido
      </div>
      <div class="mb-3"> // Campo para la contraseña
        <label for="password" class="form-label">Contraseña</label> // Etiqueta para el campo de contraseña
        <input
          type="password"
          id="password"
          name="password"
          class="form-control"
          required
        > // Campo de entrada para la contraseña, requerido
      </div> // Fin del campo de contraseña
      <button type="submit" class="btn btn-primary w-100">
        Ingresar
      </button> // Botón para enviar el formulario
    </form> // Fin del formulario de inicio de sesión

    <p class="text-center mt-3"> // Enlace para redirigir a la página de registro
      ¿No tienes cuenta?
      <a href="<?= url('auth/register') ?>">Regístrate aquí</a>
    </p> // Fin del enlace de registro
  </div> // Fin de la tarjeta del formulario
</section> // Cierre de la sección del formulario de autenticación