<?php
$title = 'Iniciar Sesión';
?>

<section class="auth-form">
  <div class="card p-4" style="min-width: 320px; max-width: 400px; width: 100%;">
    <h2 class="text-center mb-3">Administrador</h2>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= url('auth/login') ?>">
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
      <button type="submit" class="btn btn-primary w-100">
        Ingresar
      </button>
    </form>

    <p class="text-center mt-3">
      ¿No tienes cuenta?
      <a href="<?= url('auth/register') ?>">Regístrate aquí</a>
    </p>
  </div>
</section>
