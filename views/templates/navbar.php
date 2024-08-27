<nav class="navbar navbar-expand navbar-light navbar-bg sticky-top">
  <a class="sidebar-toggle js-sidebar-toggle">
    <i class="hamburger align-self-center"></i>
  </a>

  <div class="navbar-collapse collapse">
    <ul class="navbar-nav ms-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-inline-block d-sm-none" href="#" id="userDropdown" role="button"
          data-bs-toggle="dropdown" aria-expanded="false">
          <i class="align-middle" data-feather="settings"></i>
        </a>

        <a class="nav-link dropdown-toggle d-none d-sm-inline-flex align-items-center" href="#" id="userDropdownDesktop"
          role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="photos/logo.png" class="avatar img-fluid rounded me-1" alt="User Avatar" />
          <span class="text-dark">
            <?php if (isset($_SESSION['user_name'])): ?>
              <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            <?php endif; ?>
          </span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
          <li>
            <a class="dropdown-item" href="pages-profile.html">
              <i class="align-middle me-1" data-feather="user"></i>
              Profile
            </a>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li>
            <a class="dropdown-item" href="api/logout_controller.php">Log out</a>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>