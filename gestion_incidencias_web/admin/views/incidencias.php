<?php ?>

<h2 class="mb-4 text-center fw-bold text-primary" style="letter-spacing: 1px;">Listado de Incidencias</h2>

<?php if (!empty($errorInc)): ?>
  <div class="alert alert-danger shadow-sm"><?= htmlspecialchars($errorInc) ?></div>
<?php endif; ?>
<?php if (!empty($errorEmp)): ?>
  <div class="alert alert-warning shadow-sm"><?= htmlspecialchars($errorEmp) ?></div>
<?php endif; ?>

<div class="row mb-4 g-2">
  <div class="col-md-3">
    <input type="text" id="busqueda" class="form-control form-control-sm border-primary shadow-sm" placeholder="Buscar por descripción, estado, tipo...">
  </div>
  <div class="col-md-2">
    <select id="filtro-prioridad" class="form-select form-select-sm border-primary shadow-sm">
      <option value="">Prioridad...</option>
      <?php foreach ($prioridades as $p): ?>
        <option value="<?= htmlspecialchars($p['prioridad']) ?>">
          <?= htmlspecialchars($p['prioridad']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-4 d-flex">
    <input type="date" id="fecha-desde" class="form-control form-control-sm me-2 border-primary shadow-sm" placeholder="Desde">
    <input type="date" id="fecha-hasta" class="form-control form-control-sm border-primary shadow-sm" placeholder="Hasta">
  </div>
</div>

<div class="table-responsive rounded shadow-sm">
  <table class="table table-bordered table-hover table-sm bg-white">
    <thead class="table-primary text-center align-middle">
      <tr style="font-size: 0.9rem;">
        <th>ID</th>
        <th>Tipo</th>
        <th>Estado</th>
        <th>Prioridad</th>
        <th>Descripción</th>
        <th>Ubicación</th>
        <th>Fecha</th>
        <th>Asignar</th>
      </tr>
    </thead>
    <tbody class="align-middle">
      <?php if (empty($incidencias)): ?>
        <tr>
          <td colspan="8" class="text-center text-muted">No hay incidencias reportadas.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($incidencias as $inc): ?>
          <tr>
            <td class="text-center">#<?= htmlspecialchars($inc['id']) ?></td>
            <td><?= htmlspecialchars($inc['tipo']) ?></td>

            <td>
              <?php
                $estado = strtolower($inc['estado']);
                $estadoClase = match ($estado) {
                  'pendiente' => 'bg-warning-subtle text-dark fw-semibold',
                  'en desarrollo' => 'bg-info-subtle text-info',
                  'terminada' => 'bg-success-subtle text-success',
                  default => 'bg-secondary-subtle text-secondary'
                };
              ?>
              <span class="badge <?= $estadoClase ?>"><?= htmlspecialchars($inc['estado']) ?></span>
            </td>

            <td>
              <?php if ($inc['estado'] === 'Pendiente'): ?>
                <select class="form-select form-select-sm" id="select-prio-<?= $inc['id'] ?>">
                  <option value="">Prioridad…</option>
                  <?php foreach ($prioridades as $p): ?>
                    <option value="<?= htmlspecialchars($p['id']) ?>">
                      <?= htmlspecialchars($p['prioridad']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              <?php else: ?>
                <?php
                  $prioridad = strtolower($inc['prioridad'] ?? '');
                  $colorClase = match ($prioridad) {
                    'alta' => 'bg-danger-subtle text-danger',
                    'media' => 'bg-warning-subtle text-dark',
                    'baja' => 'bg-success-subtle text-success',
                    default => 'bg-secondary-subtle text-secondary'
                  };
                ?>
                <?= $prioridad
                    ? "<span class='badge $colorClase'>" . htmlspecialchars($inc['prioridad']) . "</span>"
                    : '<span class="text-muted">—</span>' ?>
              <?php endif; ?>
            </td>

            <td><?= htmlspecialchars($inc['descripcion']) ?></td>
            <td><small><?= htmlspecialchars($inc['latitud']) ?>, <?= htmlspecialchars($inc['longitud']) ?></small></td>
            <td><?= htmlspecialchars($inc['fecha_reporte']) ?></td>

            <td>
              <?php if ($inc['estado'] === 'Pendiente'): ?>
                <div class="d-flex flex-column">
                  <div class="d-flex mb-2">
                    <select class="form-select form-select-sm" id="select-emp-<?= $inc['id'] ?>">
                      <option value="">Empleado…</option>
                      <?php foreach ($empleados as $emp): ?>
                        <?php if (isset($inc['empleado_id']) && $inc['empleado_id'] == $emp['id']) continue; ?>
                        <option value="<?= htmlspecialchars($emp['id']) ?>">
                          <?= htmlspecialchars($emp['nombre'] . ' ' . $emp['apellido']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="d-flex">
                    <input type="date"
                           id="fecha-<?= $inc['id'] ?>"
                           class="form-control form-control-sm me-2"
                           min="<?= date('Y-m-d') ?>"
                           placeholder="Fecha de resolución">
                    <button onclick="assignIncidencia(<?= $inc['id'] ?>)" class="btn btn-sm btn-outline-primary">✔</button>
                  </div>
                </div>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
  const API_BASE = '<?= API_BASE ?>';
  const API_TOKEN = '<?= $_SESSION['user_token'] ?? '' ?>';

  function assignIncidencia(id) {
    const selEmp  = document.getElementById(`select-emp-${id}`);
    const selPrio = document.getElementById(`select-prio-${id}`);
    const fechaEl = document.getElementById(`fecha-${id}`);

    const empleado_id     = parseInt(selEmp.value, 10);
    const prioridad_id    = parseInt(selPrio.value, 10);
    const fecha_programada = fechaEl.value;

    if (!empleado_id || !prioridad_id || !fecha_programada) {
      return alert('Seleccione empleado, prioridad y fecha.');
    }

    fetch(`${API_BASE}admin_dashboard/asignar_incidencia.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + API_TOKEN
      },
      body: JSON.stringify({ 
        incidencia_id: id,
        empleado_id,
        prioridad_id,
        fecha_programada
      })
    })
    .then(r => r.json())
    .then(json => {
      if (json.success) {
        alert('✅ Incidencia asignada con fecha.');
        window.location.reload();
      } else {
        alert('❌ ' + (json.message||'Error al asignar'));
      }
    })
    .catch(() => alert('❌ No se pudo conectar.'));
  }

  document.addEventListener('DOMContentLoaded', () => {
    const inputBusqueda = document.getElementById('busqueda');
    const filtroPrioridad = document.getElementById('filtro-prioridad');
    const fechaDesde = document.getElementById('fecha-desde');
    const fechaHasta = document.getElementById('fecha-hasta');

    [inputBusqueda, filtroPrioridad, fechaDesde, fechaHasta].forEach(el =>
      el.addEventListener('input', filtrarTabla)
    );

    function filtrarTabla() {
      const texto = inputBusqueda.value.toLowerCase();
      const prioridad = filtroPrioridad.value.toLowerCase();
      const desde = fechaDesde.value;
      const hasta = fechaHasta.value;

      const filas = document.querySelectorAll('table tbody tr');

      filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        const descripcion = celdas[4]?.textContent.toLowerCase();
        const estado = celdas[2]?.textContent.toLowerCase();
        const tipo = celdas[1]?.textContent.toLowerCase();
        const prio = celdas[3]?.textContent.toLowerCase();
        const fecha = celdas[6]?.textContent.trim();

        let visible = true;

        if (texto && !descripcion.includes(texto) && !estado.includes(texto) && !tipo.includes(texto)) {
          visible = false;
        }

        if (prioridad && !prio.includes(prioridad)) {
          visible = false;
        }

        if (desde && fecha < desde) visible = false;
        if (hasta && fecha > hasta) visible = false;

        fila.style.display = visible ? '' : 'none';
      });
    }
  });
</script>