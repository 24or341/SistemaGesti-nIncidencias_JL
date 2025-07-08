<?php
$title = 'Reportes Estadísticos';
?>

<h2 class="mb-4 text-center text-primary fw-bold" style="letter-spacing: 1px;">
  <?= htmlspecialchars($title) ?>
</h2>

<form class="row g-3 mb-5 justify-content-center bg-white border rounded shadow-sm py-3 px-2" method="get" action="<?= url('reporte') ?>">
  <input type="hidden" name="path" value="reporte">
  <div class="col-md-3">
    <label for="inicio" class="form-label">Desde:</label>
    <input type="date" id="inicio" name="inicio" class="form-control border-primary shadow-sm" value="<?= htmlspecialchars($inicio) ?>">
  </div>
  <div class="col-md-3">
    <label for="fin" class="form-label">Hasta:</label>
    <input type="date" id="fin" name="fin" class="form-control border-primary shadow-sm" value="<?= htmlspecialchars($fin) ?>">
  </div>
  <div class="col-md-2 align-self-end d-grid">
    <button type="submit" class="btn btn-outline-primary shadow-sm fw-semibold">
      ✔ Aplicar rango
    </button>
  </div>
</form>

<div class="row g-4 mb-5">
  <div class="col-md-6">
    <div class="bg-white rounded shadow-sm p-3">
      <h5 class="text-center mb-3 fw-semibold">📊 Incidencias por Estado</h5>
      <canvas id="graficoEstado" height="280"></canvas>
    </div>
  </div>
  <div class="col-md-6">
    <div class="bg-white rounded shadow-sm p-3">
      <h5 class="text-center mb-3 fw-semibold">📌 Incidencias por Tipo</h5>
      <canvas id="graficoTipo" height="280"></canvas>
    </div>
  </div>
</div>

<div class="text-center mb-4 d-flex justify-content-center gap-3">
  <a href="<?= url('reporte/exportPdf') . "&inicio={$inicio}&fin={$fin}" ?>"
     class="btn btn-outline-success shadow-sm fw-semibold">
    📄 Exportar PDF por Empleado
  </a>
</div>

<script>
  const porEstado = <?= json_encode($por_estado, JSON_THROW_ON_ERROR) ?>;
  const porTipo   = <?= json_encode($por_tipo, JSON_THROW_ON_ERROR) ?>;

  const estados = porEstado.map(e => e.estado);
  const totalesEstado = porEstado.map(e => e.total);
  new Chart(document.getElementById('graficoEstado'), {
    type: 'doughnut',
    data: {
      labels: estados,
      datasets: [{
        data: totalesEstado,
        backgroundColor: ['#f8bbd0','#ffd54f','#aed581','#4fc3f7'],
        borderColor: '#fff',
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });

  const tipos = porTipo.map(t => t.tipo);
  const totalesTipo = porTipo.map(t => t.total);
  new Chart(document.getElementById('graficoTipo'), {
    type: 'bar',
    data: {
      labels: tipos,
      datasets: [{
        label: 'Total',
        data: totalesTipo,
        backgroundColor: '#42a5f5',
        borderRadius: 5
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0
          }
        }
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });
</script>
