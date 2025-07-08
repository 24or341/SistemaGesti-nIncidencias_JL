<?php ?>

<div class="row text-center mb-4">
  <div class="col-md-4 mb-3">
    <div class="card shadow-sm border-0" style="background-color: #fff3f4;">
      <div class="card-body">
        <h5 class="card-title text-danger fw-bold">Pendientes</h5>
        <h2 class="text-dark"><?= $pendientes ?></h2>
      </div>
    </div>
  </div>

  <div class="col-md-4 mb-3">
    <div class="card shadow-sm border-0" style="background-color: #fff8e1;">
      <div class="card-body">
        <h5 class="card-title text-warning fw-bold">En Desarrollo</h5>
        <h2 class="text-dark"><?= $en_desarrollo ?></h2>
      </div>
    </div>
  </div>

  <div class="col-md-4 mb-3">
    <div class="card shadow-sm border-0" style="background-color: #e8f5e9;">
      <div class="card-body">
        <h5 class="card-title text-success fw-bold">Terminadas</h5>
        <h2 class="text-dark"><?= $terminadas ?></h2>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm border-0 p-4 mb-4" style="background-color: #ffffff; max-width: 800px; margin: auto;">
  <h5 class="mb-3 text-center text-primary fw-semibold">Resumen gráfico de incidencias</h5>
  <div style="position: relative; height: 350px;">
    <canvas id="grafico"></canvas>
  </div>
</div>

<script>
  const datos = [<?= $pendientes ?>, <?= $en_desarrollo ?>, <?= $terminadas ?>];

  const ctx = document.getElementById('grafico').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Pendientes', 'En Desarrollo', 'Terminadas'],
      datasets: [{
        label: 'Cantidad de Incidencias',
        data: datos,
        backgroundColor: ['#dc3545', '#ffc107', '#28a745'],
        borderColor: ['#c82333', '#e0a800', '#218838'],
        borderWidth: 1,
        borderRadius: 8,
        hoverOffset: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: 'top',
          labels: {
            font: {
              size: 14,
              weight: 'bold'
            },
            color: '#333'
          }
        },
        tooltip: {
          callbacks: {
            label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y}`
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
            color: '#555'
          },
          grid: {
            color: '#ddd'
          }
        },
        x: {
          ticks: {
            color: '#555'
          },
          grid: {
            display: false
          }
        }
      }
    }
  });
</script>