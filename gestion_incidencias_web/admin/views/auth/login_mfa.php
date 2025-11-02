<?php
  // Vista MFA
  /** @var string|null $info */
?>
<section class="auth-form">
  <div class="card p-4" style="min-width:320px;max-width:400px;width:100%;">
    <h2 class="text-center mb-3">Verificación en dos pasos</h2>

    <?php if (!empty($info)): ?>
      <div class="alert alert-info"><?= htmlspecialchars($info) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= url('auth/login') ?>">
      <div class="mb-3">
        <label for="otp" class="form-label">Código (6 dígitos)</label>
        <input
          type="text"
          id="otp"
          name="otp"
          inputmode="numeric"
          pattern="\d{6}"
          maxlength="6"
          class="form-control"
          placeholder="123456"
          required
        >
        <div class="form-text">Abre tu app Authenticator y escribe el código actual.</div>
      </div>

      <button type="submit" class="btn btn-primary w-100">Verificar</button>


      <p class="text-center mt-3">
        ← <a href="<?= url('auth/login') ?>">Volver</a>
      </p>
    </form>
  </div>
</section>
