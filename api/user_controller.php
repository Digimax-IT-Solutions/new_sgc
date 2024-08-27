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


    try {
        User::add($name, $username, $role, $password);
        flashMessage('add_user', 'New user added.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add_user', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../user_list');
}


// //Update category

// if (post('action') === 'update') {
//     $name = post('name');
//     $id = post('id');

//     try {
//         $category = Uom::find($id);
//         $category->name = $name;
//         $category->update();
//         flashMessage('update_uom', 'Uom updated successfully.', FLASH_SUCCESS);
//         redirect('../admin_category.php');
//     } catch (Exception $ex) {
//         flashMessage('update_uom', $ex->getMessage(), FLASH_ERROR);
//         redirect("../admin_category.php?action=update&id={$id}");
//     }
// }