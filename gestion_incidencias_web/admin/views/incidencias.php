<?php
// Configuración del archivo incidencias.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
  /**
   * @var array<int,array{
   *   id: int|string,
   *   tipo: string,
   *   estado: string,
   *   prioridad: string|null,
   *   descripcion: string,
   *   latitud: string,
   *   longitud: string,
   *   fecha_reporte: string,
   *   empleado_id?: int|string
   * }> $incidencias
   *
   * @var array<int,array{id:int|string,nombre:string,apellido:string}> $empleados
   * @var array<int,array{id:int|string,prioridad:string}> $prioridades
   * @var string|null $errorInc
   * @var string|null $errorEmp
   */
?>

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
                $estado = strtolower($inc['estado']); // Convierte el estado a minúsculas para comparación
                $estadoClase = match ($estado) { // Asigna una clase CSS según el estado de la incidencia
                  'pendiente' => 'bg-warning-subtle text-dark fw-semibold', // Estado pendiente
                  'en desarrollo' => 'bg-info-subtle text-info', // Estado en desarrollo
                  'terminada' => 'bg-success-subtle text-success', // Estado terminada
                  default => 'bg-secondary-subtle text-secondary' // Estado desconocido o no definido
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
                  $prioridad = strtolower($inc['prioridad'] ?? ''); // Convierte la prioridad a minúsculas para comparación
                  $colorClase = match ($prioridad) { // Asigna una clase CSS según la prioridad de la incidencia
                    'alta' => 'bg-danger-subtle text-danger', // Prioridad alta
                    'media' => 'bg-warning-subtle text-dark', // Prioridad media
                    'baja' => 'bg-success-subtle text-success', // Prioridad baja
                    default => 'bg-secondary-subtle text-secondary' // Prioridad desconocida o no definida
                  };
                ?>
                <?= $prioridad // Muestra la prioridad de la incidencia con la clase correspondiente
                    ? "<span class='badge $colorClase'>" . htmlspecialchars($inc['prioridad']) . "</span>" // Muestra la prioridad de la incidencia
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

<script> // Configuración de la API y funciones para asignar incidencias
  const API_BASE = '<?= API_BASE ?>'; // Base URL de la API
  const API_TOKEN = '<?= $_SESSION['user_token'] ?? '' ?>'; // Token de autenticación de la API

  function assignIncidencia(id) { // Función para asignar una incidencia a un empleado con fecha programada
    const selEmp  = document.getElementById(`select-emp-${id}`); // Obtiene el selector de empleados para la incidencia
    const selPrio = document.getElementById(`select-prio-${id}`); // Obtiene el selector de prioridades para la incidencia
    const fechaEl = document.getElementById(`fecha-${id}`); // Obtiene el campo de fecha para la incidencia

    const empleado_id     = parseInt(selEmp.value, 10); // Obtiene el ID del empleado seleccionado
    const prioridad_id    = parseInt(selPrio.value, 10); // Obtiene el ID de la prioridad seleccionada
    const fecha_programada = fechaEl.value; // Obtiene la fecha programada para la incidencia

    if (!empleado_id || !prioridad_id || !fecha_programada) { // Verifica si se ha seleccionado un empleado, prioridad y fecha
      return alert('Seleccione empleado, prioridad y fecha.'); // Muestra un mensaje de alerta si falta información
    }

    fetch(`${API_BASE}admin_dashboard/asignar_incidencia.php`, { // Realiza una solicitud POST a la API para asignar la incidencia
      method: 'POST', // Método de la solicitud
      headers: { // Define los encabezados de la solicitud
        'Content-Type': 'application/json', // Tipo de contenido de la solicitud
        'Authorization': 'Bearer ' + API_TOKEN // Token de autenticación para la API
      },
      body: JSON.stringify({  // Cuerpo de la solicitud con los datos de la incidencia
        incidencia_id: id, // ID de la incidencia a asignar
        empleado_id, // ID del empleado seleccionado
        prioridad_id, // ID de la prioridad seleccionada
        fecha_programada // Fecha programada para la resolución de la incidencia
      })
    })
    .then(r => r.json()) // Convierte la respuesta de la API a JSON
    .then(json => { // Maneja la respuesta de la API
      if (json.success) { // Si la asignación fue exitosa
        alert('✅ Incidencia asignada con fecha.'); // Muestra un mensaje de éxito
        window.location.reload(); // Recarga la página para actualizar la lista de incidencias
      } else {
        alert('❌ ' + (json.message||'Error al asignar')); // Muestra un mensaje de error si la asignación falla
      }
    })
    .catch(() => alert('❌ No se pudo conectar.')); // Maneja errores de conexión con la API
  }

  document.addEventListener('DOMContentLoaded', () => { // Configura los filtros de búsqueda y asigna eventos
    const inputBusqueda = document.getElementById('busqueda'); // Obtiene el campo de búsqueda
    const filtroPrioridad = document.getElementById('filtro-prioridad'); // Obtiene el filtro de prioridad
    const fechaDesde = document.getElementById('fecha-desde'); // Obtiene el campo de fecha de inicio
    const fechaHasta = document.getElementById('fecha-hasta'); // Obtiene el campo de fecha de fin

    [inputBusqueda, filtroPrioridad, fechaDesde, fechaHasta].forEach(el => // Asigna el evento de entrada a los elementos de filtro
      el.addEventListener('input', filtrarTabla) // Llama a la función filtrarTabla al cambiar el valor de los filtros
    );

    function filtrarTabla() { // Función para filtrar la tabla de incidencias según los criterios seleccionados
      const texto = inputBusqueda.value.toLowerCase(); // Obtiene el texto de búsqueda y lo convierte a minúsculas
      const prioridad = filtroPrioridad.value.toLowerCase(); // Obtiene la prioridad seleccionada y la convierte a minúsculas
      const desde = fechaDesde.value; // Obtiene la fecha de inicio del filtro
      const hasta = fechaHasta.value; // Obtiene la fecha de fin del filtro

      const filas = document.querySelectorAll('table tbody tr'); // Obtiene todas las filas de la tabla de incidencias

      filas.forEach(fila => { // Itera sobre cada fila de la tabla
        const celdas = fila.querySelectorAll('td'); // Obtiene todas las celdas de la fila actual
        const descripcion = celdas[4]?.textContent.toLowerCase(); // Obtiene el texto de la descripción de la incidencia y lo convierte a minúsculas
        const estado = celdas[2]?.textContent.toLowerCase(); // Obtiene el texto del estado de la incidencia y lo convierte a minúsculas
        const tipo = celdas[1]?.textContent.toLowerCase(); // Obtiene el texto del tipo de incidencia y lo convierte a minúsculas
        const prio = celdas[3]?.textContent.toLowerCase(); // Obtiene el texto de la prioridad de la incidencia y lo convierte a minúsculas
        const fecha = celdas[6]?.textContent.trim(); // Obtiene el texto de la fecha de la incidencia

        let visible = true; // Variable para determinar si la fila debe ser visible

        if (texto && !descripcion.includes(texto) && !estado.includes(texto) && !tipo.includes(texto)) { // Verifica si el texto de búsqueda no está en la descripción, estado o tipo de incidencia
          visible = false; // Si no coincide, la fila no será visible
        }

        if (prioridad && !prio.includes(prioridad)) { // Verifica si la prioridad seleccionada no coincide con la prioridad de la incidencia
          visible = false; // Si no coincide, la fila no será visible
        }

        if (desde && fecha < desde) visible = false; // Verifica si la fecha de la incidencia es anterior a la fecha de inicio del filtro
        if (hasta && fecha > hasta) visible = false; // Verifica si la fecha de la incidencia es posterior a la fecha de fin del filtro

        fila.style.display = visible ? '' : 'none'; // Muestra la fila si es visible, de lo contrario la oculta
      });
    }
  });
</script>