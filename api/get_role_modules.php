<?php
require_once '../_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role_id = $_POST['role_id'];

    if ($role_id) {
        $modules = User::getRoleModuleAccess($role_id);

        if (!empty($modules)) {
            foreach ($modules as $module) {
                if ($module === 'dashboard') {
                    continue; // Skip the 'dashboard' module
                }

                // Convert to uppercase and replace underscores with spaces
                $formattedModule = strtoupper(str_replace('_', ' ', $module));
                echo '<div class="menu-item">';
                echo '<label>' . htmlspecialchars($formattedModule) . '</label>';
                echo '</div>';
            }
        } else {
            echo '<p>No modules available for this role.</p>';
        }

    }
}
