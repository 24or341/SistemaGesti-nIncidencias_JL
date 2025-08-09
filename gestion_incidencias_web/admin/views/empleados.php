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

<h2 class="mb-4 text-center"><?= htmlspecialchars($title ?? 'Empleados Registrados') ?></h2> // Verifica si hay un mensaje de error y lo muestra

<?php if (!empty($errorEmp)): ?> // Verifica si hay un mensaje de error y lo muestra
  <div class="alert alert-danger"><?= htmlspecialchars($errorEmp) ?></div> // Muestra el mensaje de error si existe
<?php endif; ?> // Verifica si hay empleados registrados y muestra la tabla

<div class="table-responsive"> // Crea una tabla para mostrar los empleados
  <table class="table table-bordered table-hover bg-white"> // Crea una tabla para mostrar los empleados
    <thead class="table-light"> // Define el encabezado de la tabla
      <tr> // Define las columnas de la tabla
        <th>ID</th> // Columna para el ID del empleado
        <th>DNI</th> // Columna para el DNI del empleado
        <th>Nombre</th> // Columna para el nombre del empleado
        <th>Apellido</th> // Columna para el apellido del empleado
        <th>Correo</th> // Columna para el correo electrónico del empleado
      </tr> // Cierra las columnas de la tabla
    </thead> // Cierra el encabezado de la tabla
    <tbody> // Cuerpo de la tabla donde se mostrarán los datos de los empleados
      <?php if (empty($empleados)): ?> // Verifica si no hay empleados registrados
        <tr> // Crea una fila para mostrar un mensaje si no hay empleados
          <td colspan="5" class="text-center text-muted">No hay empleados registrados.</td> // Mensaje indicando que no hay empleados registrados
        </tr> // Cierra la fila de mensaje
      <?php else: ?> // Si hay empleados registrados, itera sobre ellos para mostrarlos
        <?php foreach ($empleados as $emp): ?> // Itera sobre cada empleado en el array $empleados
          <tr> // Crea una fila para cada empleado
            <td><?= htmlspecialchars((string)$emp['dni'], ENT_QUOTES, 'UTF-8') ?></td> // Muestra el ID del empleado
            <td><?= htmlspecialchars((string)$emp['nombre'], ENT_QUOTES, 'UTF-8') ?></td> // Muestra el DNI del empleado
            <td><?= htmlspecialchars((string)$emp['apellido'], ENT_QUOTES, 'UTF-8') ?></td> // Muestra el nombre del empleado
            <td><?= htmlspecialchars((string)$emp['email'], ENT_QUOTES, 'UTF-8') ?></td> // Muestra el apellido del empleado
          </tr> // Cierra la fila del empleado
        <?php endforeach; ?> // Cierra el bucle de iteración sobre los empleados
      <?php endif; ?> // Cierra la verificación de empleados registrados
    </tbody> // Cierra el cuerpo de la tabla
  </table> // Cierra la tabla
</div> // Cierra el contenedor de la tabla
