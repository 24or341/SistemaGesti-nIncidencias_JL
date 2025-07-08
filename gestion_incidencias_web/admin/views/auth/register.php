<?php
$title = 'Crear Cuenta';
?>

<section class="auth-form">
  <div class="card p-4" style="min-width: 320px; max-width: 450px; width: 100%;">
    <h2 class="text-center mb-3">Registro de Administrador</h2>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= url('auth/register') ?>">
      <input type="hidden" name="role" value="administrador">
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input
          type="text"
          id="nombre"
          name="nombre"
          class="form-control"
          required
        >
      </div>
      <div class="mb-3">
        <label for="apellido" class="form-label">Apellido</label>
        <input
          type="text"
          id="apellido"
          name="apellido"
          class="form-control"
          required
        >
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input
          type="email"
          id="email"
          name="email"
          class="form-control"
          required
        >
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input
          type="password"
          id="password"
          name="password"
          class="form-control"
          required
        >
      </div>
      <button type="submit" class="btn btn-success w-100">
        Registrar
      </button>
    </form>

    <p class="text-center mt-3">
      ← <a href="<?= url('auth/login') ?>">Volver al inicio de sesión</a>
    </p>
  </div>
</section>
