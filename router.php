<?php
// router.php 
// Define the mapping of pages to include files
$pages = [
    'index' => 'index.php',
    'main/dashboard' => 'main/dashboard.php',
    // CUSTOMER CENTER
    'main/sales_invoice' => 'main/sales_invoice.view.php',
    'main/create_invoice' => 'main/create_invoice.view.php',
    'main/view_invoice' => 'main/view_invoice.view.php',
    'main/sales_return' => 'main/sales_return.php',
    'main/receive_payments' => 'main/Customer_center/receive_payments.view.php',
    'main/create_receive_payments' => 'main/Customer_center/create_receive_payments.view.php',
    'main/view_receive_payment' => 'main/Customer_center/view_receive_payment.view.php',
    'main/view_credit' => 'main/Customer_center/view_credit.view.php',
    // VENDOR CENTER
    'main/purchase_order' => 'main/purchase_order.view.php',
    'main/create_purchase_order' => 'main/create_purchase_order.view.php',
    'main/view_purchase_order' => 'main/view_purchase_order.view.php',
    'main/received_items' => 'main/received_items.view.php',
    'main/received_items_list' => 'main/received_items_list.view.php',
    'main/purchase_return' => 'main/purchase_return.php',
    'main/enter_bills' => 'main/Vendor/enter_bills.view.php',
    'main/bill_details_view' => 'main/bill_details_view.php',
    'main/bill' => 'main/Vendor/bill.view.php',
    'main/credit' => 'main/Customer_center/credit.view.php',
    'main/create_credit' => 'main/Customer_center/create_credit.view.php',
    'main/print_invoice' => 'main/modules/invoice/print_invoice.php',
    'main/sales_invoice_pastdue' => 'main/sales_invoice_pastdue.view.php',
    // BANKING
    'main/write_check' => 'main/Banking/write_check.view.php',
    'main/expenses' => 'main/Banking/expenses.view.php',
    'main/expenses_details_view' => 'main/expenses_details_view.php',
    // ACCOUNTING
    'main/audit_trail' => 'main/Accounting/audit_trail.view.php',
    'main/create_general_journal' => 'main/Accounting/create_general_journal.view.php',
    'main/general_journal' => 'main/Accounting/general_journal.view.php',
    'main/view_general_journal' => 'main/Accounting/view_general_journal.view.php',
    // MASTERLIST
    'main/chart_of_accounts' => 'main/chart_of_accounts.view.php',
    'main/item_list' => 'main/item_list.view.php',
    'main/customer_list' => 'main/customer_list.view.php',
    'main/vendor_list' => 'main/vendor_list.view.php',
    'main/other_names_list' => 'main/other_names_list.view.php',
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
$page = array_key_exists($page, $pages) ? $page : 'notfound';

$file = $pages[$page];

if (file_exists($file)) {
    include $file;
} else {
    include '404.php';
}