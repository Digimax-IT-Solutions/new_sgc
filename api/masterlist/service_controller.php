<?php
require_once __DIR__ . '/../../_init.php';

// if (get('action') === 'add') {
//     $name = post('name');
//     $category_id = post('category_id');
//     $quantity = post('quantity');
//     $price = post('price');

//     try {
//         Product::add($name, $category_id, $quantity, $price);
//         flashMessage('add_product', 'Product added successfully.', FLASH_SUCCESS);
//     } catch (Exception $ex) {
//         flashMessage('add_product', 'An error occured', FLASH_ERROR);
//     }
//     redirect('../../admin_add_item.php');
// }

if (get('action') === 'add') {
    $procedure_name = strtoupper($_POST['procedure_name']);
    $price = post('price');
    $description = post('description');
    $machine = post('machine');
    $process = post('process');
    $recommended_for = post('recommended_for');
    $results = post('results');
    $code = post('code');
    $category_id = post('category_id');
    try {
        Service::add(
            $procedure_name,
            $price,
            $description,
            $machine,
            $process,
            $recommended_for,
            $results,
            $code,
            $category_id
        );
        flashMessage('add_service', 'Service added successfully.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add_service', 'An error occurred: ' . $ex->getMessage(), FLASH_ERROR);
    }
    redirect('../../admin_service_list.php');
}

if (get('action') === 'delete') {
    $id = $_GET['id'];

    Service::find($id)?->delete();

    flashMessage('delete_service', 'Service deleted successfully.', FLASH_SUCCESS);
    redirect('../../admin_service_list.php');
}

// if (get('action') === 'update') {
//     $product = Guard::hasModel(Product::class);
//     $product->name = post('name');
//     $product->category_id = post('category_id');
//     $product->price = post('price');
//     $product->update();

//     flashMessage('update_product', 'Product updated successfully', FLASH_SUCCESS);
//     redirect('../admin_update_item.php?id=' . $product->id);
// }

// if (get('action') === 'add_stock') {
//     $product = Guard::hasModel(Product::class);
//     $product->quantity += get('quantity');
//     $product->update();

//     flashMessage('add_stock', "Stocks quantity updated successfully", FLASH_SUCCESS);
//     redirect('../admin_home.php');
// }