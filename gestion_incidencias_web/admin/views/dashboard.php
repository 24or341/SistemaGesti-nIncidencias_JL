<?php
// Configuración del archivo dashboard.php
// Realizado por: Jorge Enrique Castañeda Centurión
// Fecha: 2025-09-08
  /** @var int $pendientes */
  /** @var int $en_desarrollo */
  /** @var int $terminadas */
?>

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

<script> // Script para generar el gráfico de incidencias
  const datos = [<?= $pendientes ?>, <?= $en_desarrollo ?>, <?= $terminadas ?>]; // Datos del gráfico basados en las incidencias

  const ctx = document.getElementById('grafico').getContext('2d'); // Obtener el contexto del canvas para el gráfico
  new Chart(ctx, { // Crear una nueva instancia de Chart.js
    type: 'bar', // Tipo de gráfico: barras
    data: { // Datos del gráfico
      labels: ['Pendientes', 'En Desarrollo', 'Terminadas'], // Etiquetas para las barras del gráfico
      datasets: [{ // Dataset del gráfico
        label: 'Cantidad de Incidencias', // Etiqueta del dataset
        data: datos, // Datos del dataset
        backgroundColor: ['#dc3545', '#ffc107', '#28a745'], // Colores de fondo para las barras
        borderColor: ['#c82333', '#e0a800', '#218838'], // Colores de borde para las barras
        borderWidth: 1, // Ancho del borde de las barras
        borderRadius: 8, // Radio de las esquinas de las barras
        hoverOffset: 8 // Desplazamiento al pasar el mouse sobre las barras
      }] // Configuración del dataset del gráfico
    },
    options: { // Opciones del gráfico
      responsive: true, // Hacer el gráfico responsivo
      maintainAspectRatio: false, // Mantener la relación de aspecto del gráfico
      plugins: { // Plugins del gráfico
        legend: { // Configuración de la leyenda del gráfico
          display: true, // Mostrar la leyenda
          position: 'top', // Posición de la leyenda
          labels: { // Etiquetas de la leyenda
            font: { // Fuente de las etiquetas de la leyenda
              size: 14, // Tamaño de la fuente
              weight: 'bold' // Peso de la fuente
            },
            color: '#333' // Color de las etiquetas de la leyenda
          }
        },
        tooltip: { // Configuración de las herramientas emergentes del gráfico
          callbacks: { // Funciones de retorno de llamada para personalizar las herramientas emergentes
            label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y}` // Personalizar el texto de la herramienta emergente
          }
        }
      },
      scales: { // Escalas del gráfico
        y: { // Escala Y del gráfico
          beginAtZero: true, // Comenzar desde cero en el eje Y
          ticks: { // Configuración de las marcas del eje Y
            stepSize: 1, // Tamaño del paso de las marcas
            color: '#555' // Color de las marcas del eje Y
          },
          grid: { // Configuración de la cuadrícula del eje Y
            color: '#ddd' // Color de la cuadrícula
          }
        },
        x: { // Escala X del gráfico
          ticks: { // Configuración de las marcas del eje X
            color: '#555' // Color de las marcas del eje X
          },
          grid: { // Configuración de la cuadrícula del eje X
            display: false // No mostrar la cuadrícula del eje X
          }
        }
      }
    } // Configuración adicional del gráfico
  });
</script>