<style>
    /* Compact Sidebar Styling - MS Word-like compactness */
    .sidebar-item {
        padding: 5px 5px;
        margin: 0;
        font-size: 0.85rem;
        transition: background-color 0.2s, color 0.2s;
        position: relative;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        /* Aligns icon and text vertically */
        padding: 5px 8px;
        color: #FFFFFF;
        font-weight: 500;
        transition: color 0.2s ease;
        text-decoration: none;
    }

    .sidebar-link:hover {
        color: #f0f0f0;
        background-color: rgba(255, 255, 255, 0.05);
    }

    .sidebar-link i {
        margin-right: 6px;
        font-size: 1rem;
        min-width: 20px;
        /* Ensures all icons take up the same horizontal space */
        text-align: center;
        /* Centers the icon in the allocated space */
    }

    .sidebar-item.active .sidebar-link {
        background-color: #7D102E;
        color: #FFFFFF;
    }

    .sidebar-item .arrow-icon {
        font-size: 0.75rem;
    }

    .sidebar-item.active .arrow-icon {
        transform: rotate(90deg);
    }

    /* Sidebar Dropdown with Tree View Lines */
    .sidebar-dropdown {
        padding-left: 20px;
        position: relative;
    }

    .sidebar-dropdown .sidebar-item {
        padding-left: 25px;
        position: relative;
    }

    /* Tree Lines (For submenu items only) */
    .sidebar-dropdown .sidebar-item::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 1px;
        background-color: rgba(255, 255, 255, 0.2);
        /* Vertical line for sub-items */
    }

    .sidebar-dropdown .sidebar-item:last-child::before {
        height: 55%;
        bottom: auto;
    }

    .sidebar-dropdown .sidebar-item::after {
        content: '';
        position: absolute;
        left: 10px;
        top: 20px;
        width: 10px;
        height: 1px;
        background-color: rgba(255, 255, 255, 0.2);
        /* Horizontal line */
    }

    /* No line under the main items */
    .sidebar-nav>.sidebar-item::before {
        content: none;
    }

    .sidebar-nav>.sidebar-item>.sidebar-link::after {
        content: none;
    }

    /* Scrollbar Styling */
    .js-simplebar {
        scrollbar-width: thin;
        scrollbar-color: #444 #1b1b1b;
    }

    .js-simplebar::-webkit-scrollbar {
        width: 6px;
    }

    .js-simplebar::-webkit-scrollbar-thumb {
        background-color: #444;
        border-radius: 4px;
    }

    /* Remove all unnecessary margin or padding from top-level nav */
    .sidebar-nav {
        padding: 0;
        margin: 0;
    }

    .sidebar-nav>.sidebar-item {
        padding-left: 7px;
        margin: 0;
    }

    .sidebar-nav .sidebar-item .sidebar-link {
        padding: 4px 8px;
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