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
            <td><?= htmlspecialchars($emp['id']) ?></td>
            <td><?= htmlspecialchars($emp['dni']) ?></td>
            <td><?= htmlspecialchars($emp['nombre']) ?></td>
            <td><?= htmlspecialchars($emp['apellido']) ?></td>
            <td><?= htmlspecialchars($emp['email']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
