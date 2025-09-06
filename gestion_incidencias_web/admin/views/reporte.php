<?php
  // Configuración del archivo reporte.php
  // Realizado por: Jorge Enrique Castañeda Centurión
  // Fecha: 2025-09-08
  /** @var array<int,array{estado:string,total:int}> $por_estado */
  /** @var array<int,array{tipo:string,total:int}> $por_tipo */
  /** @var string $inicio */
  /** @var string $fin */
  $title = 'Reportes Estadísticos'; // Título de la página
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

<script> // Configuración de los gráficos utilizando Chart.js
  const porEstado = <?= json_encode($por_estado, JSON_THROW_ON_ERROR) ?>; // Datos de incidencias por estado
  const porTipo   = <?= json_encode($por_tipo, JSON_THROW_ON_ERROR) ?>; // Datos de incidencias por tipo

  const estados = porEstado.map(e => e.estado); // Extraer los estados de las incidencias
  const totalesEstado = porEstado.map(e => e.total); // Extraer los totales de incidencias por estado
  new Chart(document.getElementById('graficoEstado'), { // Crear el gráfico de incidencias por estado
    type: 'doughnut', // Tipo de gráfico
    data: { // Datos del gráfico de incidencias por estado
      labels: estados, // Etiquetas de los estados
      datasets: [{ // Datos del conjunto de datos
        data: totalesEstado, // Totales de incidencias por estado
        backgroundColor: ['#f8bbd0','#ffd54f','#aed581','#4fc3f7'], // Colores de fondo para cada estado
        borderColor: '#fff', // Color del borde
        borderWidth: 2 // Ancho del borde
      }]
    },
    options: { // Opciones del gráfico de incidencias por estado
      responsive: true, // Hacer el gráfico responsivo
      plugins: { // Plugins del gráfico de incidencias por estado
        legend: { // Configuración de la leyenda
          position: 'bottom' // Posición de la leyenda
        }
      }
    }
  });

  const tipos = porTipo.map(t => t.tipo); // Extraer los tipos de incidencias
  const totalesTipo = porTipo.map(t => t.total); // Extraer los totales de incidencias por tipo
  new Chart(document.getElementById('graficoTipo'), { // Crear el gráfico de incidencias por tipo
    type: 'bar', // Tipo de gráfico
    data: { // Datos del gráfico de incidencias por tipo
      labels: tipos, // Etiquetas de los tipos
      datasets: [{ // Datos del conjunto de datos
        label: 'Total', // Etiqueta del conjunto de datos
        data: totalesTipo, // Totales de incidencias por tipo
        backgroundColor: '#42a5f5', // Color de fondo
        borderRadius: 5 // Radio de los bordes
      }]
    },
    options: { // Opciones del gráfico de incidencias por tipo
      responsive: true, // Hacer el gráfico responsivo
      scales: { // Escalas del gráfico de incidencias por tipo
        y: { // Escala Y
          beginAtZero: true, // Comenzar desde cero
          ticks: { // Configuración de las etiquetas en el eje Y
            precision: 0 // Precisión de las etiquetas
          }
        }
      },
      plugins: { // Plugins del gráfico de incidencias por tipo
        legend: { // Configuración de la leyenda
          display: false // No mostrar la leyenda
        }
      }
    }
  });
</script>