<?php
// router.php

// Define the mapping of pages to include files
$pages = [
    'index' => 'index.php',
    'main/dashboard' => 'main/dashboard.php',
    'main/sales_invoice' => 'main/sales_invoice.view.php',
    'main/create_invoice' => 'main/create_invoice.view.php',
    'main/view_invoice' => 'main/view_invoice.view.php',
    'main/sales_return' => 'main/sales_return.php',
    'main/received_payments' => 'main/received_payments.php',
    'main/purchase_order' => 'main/purchase_order.view.php',
    'main/create_purchase_order' => 'main/create_purchase_order.view.php',
    'main/received_items' => 'main/received_items.php',
    'main/purchase_return' => 'main/purchase_return.php',
    'main/chart_of_accounts' => 'main/chart_of_accounts.view.php',
    'main/item_list' => 'main/item_list.view.php',
    'main/customer_list' => 'main/customer_list.view.php',
    'main/vendor_list' => 'main/vendor_list.view.php',
    'main/location_list' => 'main/location_list.view.php',
    'main/category_list' => 'main/category_list.view.php',
    'main/terms_list' => 'main/terms_list.view.php',
    'main/payment_method_list' => 'main/payment_method_list.view.php',
    'main/sales_tax_list' => 'main/sales_tax_list.view.php',
    'main/wtax_list' => 'main/wtax_list.view.php',
    'main/uom_list' => 'main/uom_list.view.php',
    'main/user_list' => 'main/user_list.php',
    'main/settings' => 'main/settings.php',
    'logout' => 'logout.php',
    'notfound' => '404.php',
];

// Get the requested page from the rewritten URL
$page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'index';

if (array_key_exists($page, $pages)) {
    $file = $pages[$page];
    if (file_exists($file)) {
        include $file;
    } else {
        include '404.php';
    }
} else {
    include '404.php';
}