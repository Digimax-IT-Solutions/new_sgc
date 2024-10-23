<style>
    /* Active Sidebar Item Background */
    .sidebar-item.active {
        background: #7D102E;

    }

    /* Sidebar CSS */
    .sidebar-link.collapsed .arrow-icon {
        transform: rotate(0deg);
        transition: transform 0.3s ease;
    }

    .sidebar-link:not(.collapsed) .arrow-icon {
        transform: rotate(90deg);
        transition: transform 0.3s ease;
    }

    /* Indent dropdown list */
    /* Indent only the submenu items */
    .sidebar-dropdown .sidebar-item {
        padding-left: 20px;
        /* Adjust this value to control the indentation */
    }

    /* Keep the top-level sidebar items unchanged */
    .sidebar-nav>.sidebar-item {
        padding-left: 0;
        /* Ensure no indentation for top-level items */
    }
</style>

<?php
require_once 'menu_config.php';

$currentUser = User::getAuthenticatedUser();
$userRoleName = $currentUser ? $currentUser->getRoleName() : '';
$userId = $currentUser ? $currentUser->id : '';

function hasRole($roles)
{
    global $userRoleName;
    return in_array($userRoleName, $roles);
}

function hasAccess($module)
{
    global $currentUser;
    return $currentUser ? $currentUser->hasModuleAccess($module) : false;
}

function hasSubmenuAccess($submenu)
{
    foreach ($submenu as $item) {
        if (isset($item['link']) && hasAccess($item['link'])) {
            return true;
        }
    }
    return false;
}



function renderMenuItem($item, $key)
{


    $link = $item['link'] ?? '';
    $hasSubmenu = isset($item['submenu']);

    // Check if the user has access to this menu item
    if (!$hasSubmenu && $link && !hasAccess($link)) {
        return;
    }

    if ($hasSubmenu && !hasSubmenuAccess($item['submenu'])) {
        return;
    }

    $isActive = getCurrentPage() == $link ? 'active' : '';
    $collapsed = $hasSubmenu ? 'collapsed' : '';
    $arrowIcon = $hasSubmenu ? "<i class='fas fa-chevron-right align-middle arrow-icon'></i>" : '';
    $toggleTarget = $hasSubmenu ? "data-bs-target='#" . ($item['toggle'] ?? $key) . "'" : '';

    echo "<li class='sidebar-item $isActive'>";
    echo "<a class='sidebar-link $collapsed' " . ($hasSubmenu ? "data-bs-toggle='collapse' $toggleTarget" : "href='{$link}'") . ">";
    echo "<i class='{$item['icon']} align-middle'></i>";
    echo "$arrowIcon";
    echo "<span class='align-middle'><b>" . htmlspecialchars($key) . "</b></span>";
    echo "</a>";

    if ($hasSubmenu) {
        $submenuId = $item['toggle'] ?? $key;
        $submenuActiveClass = in_array(getCurrentPage(), array_column($item['submenu'], 'link')) ? 'show' : 'collapse';
        echo "<ul id='$submenuId' class='sidebar-dropdown list-unstyled $submenuActiveClass' data-bs-parent='#sidebar-content'>";
        foreach ($item['submenu'] as $subKey => $subItem) {
            $subLink = $subItem['link'] ?? '';
            if ($subLink && !hasAccess($subLink)) {
                continue;
            }
            $subActiveClass = getCurrentPage() == $subLink ? 'active' : '';
            echo "<li class='sidebar-item $subActiveClass'>";
            echo "<a class='sidebar-link' href='$subLink'>";
            echo "<i class='{$subItem['icon']} align-middle'></i> " . htmlspecialchars($subKey);
            echo "</a>";
            echo "</li>";
        }
        echo "</ul>";
    }

    echo "</li>";
}

?>

<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar" id="sidebar-content">
        <a class="sidebar-brand" href="dashboard">
            <span class="align-middle"><?= htmlspecialchars($userRoleName); ?></span>
        </a>
        <!-- Sidebar user panel -->
        <div class="user-panel text-center">
            <div class="image">
                <img src="photos/logo.png" class="img-circle img-fluid rounded" style="width: 100px" alt="User Image">
            </div>
            <div class="info">
                <p></p>
                <a href="#"><i class="fa fa-circle text-success"></i>
                    <?= htmlspecialchars($currentUser ? $currentUser->name : 'Guest'); ?></a>
            </div>
        </div>
        <ul class="sidebar-nav mt-4">
            <?php
            foreach ($menuConfig as $key => $item) {
                renderMenuItem($item, $key);
            }
            ?>
        </ul>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>