<?php
// Configuración del archivo empleados.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
/**
 * @var array<int, array{
 *   id: int|string,
 *   dni: string,
 *   nombre: string,
 *   apellido: string,
 *   email: string
 * }> $empleados
 * @var string|null $title
 * @var string|null $errorEmp
 */
?>

<h2 class="mb-4 text-center"><?= htmlspecialchars($title ?? 'Empleados Registrados') ?></h2>

<?php if (!empty($errorEmp)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($errorEmp) ?></div>
<?php endif; ?>

<div class="table-responsive">
  <table class="table table-bordered table-hover bg-white">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>DNI</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Correo</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($empleados)): ?>
        <tr>
          <td colspan="5" class="text-center text-muted">No hay empleados registrados.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($empleados as $emp): ?>
          <tr>
            <td><?= htmlspecialchars((string)$emp['id'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string)$emp['dni'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string)$emp['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string)$emp['apellido'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string)$emp['email'], ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
