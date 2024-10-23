<?php

require_once __DIR__ . '/../_init.php';


// //Delete category
if (get('action') === 'delete') {
    $id = get('id');
    $user = User::find($id);

    if ($user) {
        $user->delete();
        flashMessage('delete_user', 'User has been deleted', FLASH_SUCCESS);
    } else {
        flashMessage('delete_user', 'Invalid user', FLASH_ERROR);
    }
    redirect('../user_list');
}


//Add category
if (post('action') === 'add') {

    $name = post('name');
    $username = post('username');
    $role = post('role');
    $password = post('password');

    $modules = $_POST['menus']; // Submenus


    try {
        // Add user to the users table
        $userId = User::add($name, $username, $role, $password);

        // // Add module access to the user_module_access table
        // if (!empty($modules)) {
        //     foreach ($modules as $module) {
        //         User::addModuleAccess($userId, $module);
        //     }
        // }

        flashMessage('add_user', 'New user added.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add_user', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../user_list');
}


if (post('action') === 'addrole') {

    $role_name = strtoupper(post('roleName')); // Convert to uppercase
    $modules = $_POST['menus']; // Submenus


    try {
        // Add user to the users table
        $roleId = Role::add($role_name);

        // Add module access to the user_module_access table
        if (!empty($modules)) {
            foreach ($modules as $module) {
                Role::addModuleAccess($roleId, $module);
            }
        }


        flashMessage('add_role', 'New role added.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add_role', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../role_list');
}

if (get('action') === 'deleteRole') {
    $id = get('id');
    $user = Role::find($id);

    if ($user) {
        $user->delete();
        flashMessage('delete_role', 'Role has been deleted', FLASH_SUCCESS);
    } else {
        flashMessage('delete_role', 'Invalid role', FLASH_ERROR);
    }
    redirect('../role_list');
}


