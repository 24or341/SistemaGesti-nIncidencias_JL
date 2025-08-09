<?php
  // Configuración del archivo header.php
  // Realizado por: Jorge Enrique Castañeda Centurión
  // Fecha: 2025-09-08 
  $role = $_SESSION['user_role'] ?? ''; // Asignar el rol del usuario desde la sesión, si está disponible
?>

<style>
  .sidebar {
    min-height: 100vh;
    width: 220px;
    transition: width 0.3s ease;
    overflow-x: hidden;
  }

  .sidebar.collapsed {
    width: 70px;
  }

  .sidebar .nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    padding: 10px 15px;
    border-radius: 6px;
    color: white;
    transition: background-color 0.3s ease, transform 0.2s ease;
    white-space: nowrap;
  }

  .sidebar.collapsed .nav-link span {
    display: none;
  }

  .sidebar .nav-link:hover {
    background-color: #495057;
    transform: translateX(5px);
  }

  .toggle-btn {
    background: none;
    border: none;
    color: white;
    font-size: 1.2rem;
    margin-bottom: 1rem;
  }

  .sidebar .sidebar-title {
    color: white;
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 1.5rem;
    text-align: center;
  }

  .sidebar.collapsed .sidebar-title {
    display: none;
  }
</style> // Estructura del sidebar con estilos CSS

<nav id="sidebar" class="sidebar bg-dark p-3 d-flex flex-column"> // Inicio del sidebar
  <button class="toggle-btn align-self-end" onclick="toggleSidebar()"> // Botón para alternar el sidebar
    <i class="fas fa-bars"></i> // Icono del botón
  </button> // Botón para alternar el sidebar
  <div class="sidebar-title">SISTEMA</div> // Título del sidebar
  <ul class="nav flex-column"> // Lista de navegación del sidebar
    <?php if ($role === 'administrador'): ?> // Verifica si el usuario es administrador
      <li class="nav-item"><a href="<?= url('dashboard') ?>" class="nav-link"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a></li> // Enlace al dashboard
      <li class="nav-item"><a href="<?= url('incidencias') ?>" class="nav-link"><i class="fas fa-exclamation-triangle"></i> <span>Incidencias</span></a></li> // Enlace a las incidencias
      <li class="nav-item"><a href="<?= url('empleados') ?>" class="nav-link"><i class="fas fa-users"></i> <span>Empleados</span></a></li> // Enlace a los empleados
      <li class="nav-item"><a href="<?= url('reporte') ?>" class="nav-link"><i class="fas fa-file-alt"></i> <span>Reportes</span></a></li> // Enlace a los reportes
    <?php else: ?> // Si el usuario no es administrador
      <li class="nav-item"><a href="<?= url('incidencias') ?>" class="nav-link"><i class="fas fa-list"></i> <span>Mis Incidencias</span></a></li> // Enlace a las incidencias del usuario
    <?php endif; ?> // Fin de la verificación del rol del usuario
    <li class="nav-item mt-auto"><a href="<?= url('auth/logout') ?>" class="nav-link"><i class="fas fa-sign-out-alt"></i> <span>Cerrar Sesión</span></a></li> // Enlace para cerrar sesión
  </ul> // Fin de la lista de navegación
</nav> // Fin del sidebar

<script> // Función para alternar el estado del sidebar
  function toggleSidebar() { // Alterna la clase 'collapsed' en el sidebar y el contenido principal
    const sidebar = document.getElementById('sidebar'); // Obtiene el elemento del sidebar
    const mainContent = document.querySelector('.main-content'); // Obtiene el contenido principal
    sidebar.classList.toggle('collapsed'); // Alterna la clase 'collapsed' en el sidebar
    mainContent.classList.toggle('collapsed'); // Alterna la clase 'collapsed' en el contenido principal
  } // Fin de la función para alternar el sidebar
</script> // Fin del script para alternar el sidebar