<?php
/** @var string|null $qr */
/** @var string|null $secret */
/** @var string|null $otpauth */
/** @var string|null $error */
$qr      = $qr      ?? null;
$secret  = $secret  ?? '';
$otpauth = $otpauth ?? '';
$error   = $error   ?? null;
?>

<h2 class="text-center mb-3">Activar MFA (TOTP)</h2>

<?php if ($error): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($qr): ?>
  <div class="text-center mb-3">
    <img src="<?= htmlspecialchars($qr) ?>" alt="QR MFA" width="220" height="220">
    <p class="text-muted small mb-0">Si no puedes escanear, usa este código:</p>
    <code><?= htmlspecialchars($secret) ?></code>
  </div>
<?php else: ?>
  <div class="alert alert-warning">No se pudo generar el QR. Recarga la página.</div>
<?php endif; ?>

<form method="post" action="<?= url('auth/mfa-verify') ?>" class="mt-3">
  <div class="mb-3">
    <label class="form-label">Código de 6 dígitos</label>
    <input type="text" name="code" maxlength="6" class="form-control" required>
  </div>
  <button class="btn btn-primary w-100">Verificar</button>
</form>

<p class="text-center mt-3">← <a href="<?= url('auth/login') ?>">Volver</a></p>
