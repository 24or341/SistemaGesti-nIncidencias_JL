<?php $role = $_SESSION['user_role'] ?? ''; ?>

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
</style>

<nav id="sidebar" class="sidebar bg-dark p-3 d-flex flex-column">
  <button class="toggle-btn align-self-end" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
  </button>
  <div class="sidebar-title">SISTEMA</div>
  <ul class="nav flex-column">
    <?php if ($role === 'administrador'): ?>
      <li class="nav-item"><a href="<?= url('dashboard') ?>" class="nav-link"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a></li>
      <li class="nav-item"><a href="<?= url('incidencias') ?>" class="nav-link"><i class="fas fa-exclamation-triangle"></i> <span>Incidencias</span></a></li>
      <li class="nav-item"><a href="<?= url('empleados') ?>" class="nav-link"><i class="fas fa-users"></i> <span>Empleados</span></a></li>
      <li class="nav-item"><a href="<?= url('reporte') ?>" class="nav-link"><i class="fas fa-file-alt"></i> <span>Reportes</span></a></li>
    <?php else: ?>
      <li class="nav-item"><a href="<?= url('incidencias') ?>" class="nav-link"><i class="fas fa-list"></i> <span>Mis Incidencias</span></a></li>
    <?php endif; ?>
    <li class="nav-item mt-auto"><a href="<?= url('auth/logout') ?>" class="nav-link"><i class="fas fa-sign-out-alt"></i> <span>Cerrar Sesión</span></a></li>
  </ul>
</nav>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('collapsed');
  }
</script>