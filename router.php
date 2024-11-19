<?php
// router.php 
// Define the mapping of pages to include files
$pages = [

    'index' => 'index.php',
    // DEVELOPER ONLY
    'dashboard' => 'views/admin_home.php',
    'purchasing_home' => 'views/purchasing_home.php',
    'warehouse' => 'views/warehouse_home.php',

    // START ADMIN ROUTE ==========================================================
    // SALES INVOICE
    'invoice' => 'views/admin/invoice/invoice_list.php',
    'create_invoice' => 'views/admin/invoice/create_invoice.php',
    'view_invoice' => 'views/admin/invoice/view_invoice.php',
    'print_invoice' => 'views/admin/invoice/print_invoice.php',
    'void_invoice' => 'views/admin/invoice/void_invoice_list.php',
    'draft_invoice' => 'views/admin/invoice/draft_invoice_list.php',

    // RECEIVE PAYMENT
    'receive_payment' => 'views/admin/receive_payment/receive_payment_list.php',
    'customer_payment' => 'views/admin/receive_payment/customer_payment.php',
    'official_receipt' => 'views/admin/receive_payment/official_receipt.php',
    'view_payment' => 'views/admin/receive_payment/view_payment.php',
    'draft_payment' => 'views/admin/receive_payment/draft_payment.php',
    'void_payment' => 'views/admin/receive_payment/void_payment.php',
    'print_payment' => 'views/admin/receive_payment/print_payment.php',
    'print_or' => 'views/admin/receive_payment/print_or.php',
    'or_list' => 'views/admin/receive_payment/or_list.php',
    'view_or' => 'views/admin/receive_payment/view_or.php',
    'draft_or' => 'views/admin/receive_payment/draft_or.php',
    'void_or' => 'views/admin/receive_payment/void_or.php',


    // CREDIT MEMO
    'credit_memo' => 'views/admin/credit_memo/credit_memo_list.php',
    'create_credit_memo' => 'views/admin/credit_memo/create_credit_memo.php',
    'view_credit' => 'views/admin/credit_memo/view_credit_memo.php',
    'print_credit' => 'views/admin/credit_memo/print_credit_memo.php',
    'draft_credit' => 'views/admin/credit_memo/draft_credit_memo.php',
    'void_credit' => 'views/admin/credit_memo/void_credit_memo.php',

    // SALES RETURN
    'sales_return' => 'views/admin/sales_return/sales_return_list.php',
    'create_sales_return' => 'views/admin/sales_return/create_sales_return.php',
    'print_sales_return' => 'views/admin/sales_return/print_sales_return.php',
    'view_sales_return' => 'views/admin/sales_return/view_sales_return.php',
    'draft_sales_return' => 'views/admin/sales_return/draft_sales_return.php',
    'void_sales_return' => 'views/admin/sales_return/void_sales_return.php',

    // PURCHASE ORDER
    'purchase_request' => 'views/admin/purchase_request/purchase_request_list.php',
    'purchase_order' => 'views/admin/purchase_order/purchase_order_list.php',
    'view_purchase_order' => 'views/admin/purchase_order/view_purchase_order.php',
    'create_purchase_order' => 'views/admin/purchase_order/create_purchase_order.php',
    'print_purchase_order' => 'views/admin/purchase_order/print_purchase_order.php',
    'draft_purchase_order' => 'views/admin/purchase_order/draft_purchase_order.php',
    'void_purchase_order' => 'views/admin/purchase_order/void_purchase_order.php',

    // RECEIVE ITEMS
    'receive_items' => 'views/admin/receive_items/receive_item_list.php',
    'create_receive_item' => 'views/admin/receive_items/create_receive_item.php',
    'view_receive_item' => 'views/admin/receive_items/view_receive_item.php',
    'print_receive_item' => 'views/admin/receive_items/print_receive_item.php',

    // APV
    'accounts_payable_voucher' => 'views/admin/apv/apv_list.php',
    'create_apv_expense' => 'views/admin/apv/create_apv_expense.php',
    'create_apv_item' => 'views/admin/apv/create_apv_item.php',
    'view_apv' => 'views/admin/apv/view_apv.php',
    'print_apv' => 'views/admin/apv/print_apv.php',
    'draft_apv' => 'views/admin/apv/draft_apv.php',
    'void_apv' => 'views/admin/apv/void_apv.php',

    // ENTER BILLS 
    'enter_bills' => 'views/admin/enter_bills/enter_bills_list.php',
    'create_enter_bills' => 'views/admin/admin/enter_bills/create_enter_bills.php',

    // PURCHASE RETURN
    'purchase_return' => 'views/admin/purchase_return/purchase_return_list.php',

    // PAY BILLS
    'pay_bills' => 'views/admin/pay_bills/pay_bills_list.php',
    'create_pay_bills' => 'views/admin/pay_bills/create_pay_bills.php',

    // PURCHASE REQUEST
    'create_purchase_request' => 'views/admin/purchase_request/create_purchase_request.php',
    'view_purchase_request' => 'views/admin/purchase_request/view_purchase_request.php',
    'print_purchase_request' => 'views/admin/purchase_request/print_purchase_request.php',
    'void_purchase_request' => 'views/admin/purchase_request/void_purchase_request.php',
    'draft_purchase_request' => 'views/admin/purchase_request/draft_purchase_request.php',
    // MAKE DEPOSIT
    'make_deposit' => 'views/admin/make_deposit/make_deposit_list.php',

    // BANK TRANSFER
    'bank_transfer' => 'views/admin/bank_transfer/bank_transfer_list.php',

    // RECONCILE
    'reconcile' => 'views/admin/reconcile/reconcile_list.php',

    // DASHBOARDS
    'customer_center' => 'views/admin/dashboards/customer_center.php',
    'vendor_center' => 'views/admin/dashboards/vendor_center.php',
    'banking' => 'views/admin/dashboards/banking.php',
    'purchasing' => 'views/admin/dashboards/purchasing.php',
    'warehouse_dashboard' => 'views/admin/dashboards/warehouse_dashboard.php',
    'accounting' => 'views/admin/dashboards/accounting.php',
    'masterlist' => 'views/admin/dashboards/masterlist.php',

    // GENERAL JOURBAL
    'general_journal' => 'views/admin/general_journal/general_journal_list.php',
    'create_general_journal' => 'views/admin/general_journal/create_general_journal.php',
    'view_journal' => 'views/admin/general_journal/view_general_journal.php',
    'print_general_journal' => 'views/admin/general_journal/print_general_journal.php',
    'draft_general_journal' => 'views/admin/general_journal/draft_general_journal.php',
    'void_general_journal' => 'views/admin/general_journal/void_general_journal.php',

    'general_ledger' => 'views/admin/general_ledger/general_ledger.php',

    // TRANSACTION ENTRIES
    'transaction_entries' => 'views/admin/transaction_entries/transaction_entries_list.php',

    // TRIAL BALANCE
    'trial_balance' => 'views/admin/trial_balance/trial_balance_list.php',

    // AUDIT TRAIL
    'audit_trail' => 'views/admin/audit_trail/audit_trail_list.php',

    // PROFIT LOSS
    'profit_loss' => 'views/admin/profit_loss/profit_loss_list.php',

    // BALANCE SHEET
    'balance_sheet' => 'views/admin/balance_sheet/balance_sheet_list.php',

    // CHART OF ACCOUNTS
    'chart_of_accounts' => 'views/admin/chart_of_accounts/chart_of_accounts_list.php',
    'chart_of_accounts_list' => 'views/admin/chart_of_accounts/chart_of_accounts_list.php',
    'add_chart_of_account' => 'views/admin/chart_of_accounts/add_chart_of_account.php',
    'edit_chart_of_account' => 'views/admin/chart_of_accounts/edit_chart_of_account.php',

    // ITEM LIST
    'item_list' => 'views/admin/items/item_list.php',
    'add_item' => 'views/admin/items/add_item.php',
    'view_item' => 'views/admin/items/view_item.php',

    // CUSTOMER LIST
    'customer' => 'views/admin/customer/customer_list.php',
    'create_customer' => 'views/admin/customer/create_customer.php',
    'view_customer' => 'views/admin/customer/view_customer.php',

    // VENDOR LIST
    'vendor_list' => 'views/admin/vendor/vendor_list.php',
    'view_vendor' => 'views/admin/vendor/view_vendor.php',
    'create_vendor' => 'views/admin/vendor/create_vendor.php',

    // EMPLOYEE LIST
    'employee_list' => 'views/admin/employee/employee_list.php',
    'create_employee' => 'views/admin/employee/create_employee.php',
    'edit_employee' => 'views/admin/employee/edit_employee.php',

    // OTHER NAME
    'other_name' => 'views/admin/other_name/other_name_list.php',
    'view_other_name_list' => 'views/admin/other_name/view_other_name_list.php',
    'create_other_name' => 'views/admin/other_name/create_other_name.php',

    // FS CLASSIFICATION 
    'fs_classification' => 'views/admin/fs_classification/fs_classification_list.php',
    'create_fs_classification' => 'views/admin/fs_classification/create_fs_classification.php',
    'view_fs_classification' => 'views/admin/fs_classification/view_fs_classification.php',
    'fs_notes_classification' => 'views/admin/fs_notes_classification/fs_notes_classification_list.php',
    'create_fs_notes_classification' => 'views/admin/fs_notes_classification/create_fs_notes_classification.php',
    'view_fs_notes_classification' => 'views/admin/fs_notes_classification/view_fs_notes_classification.php',

    // LOCATION LIST
    'location' => 'views/admin/location/location_list.php',
    'create_location' => 'views/admin/location/create_location.php',
    'view_location' => 'views/admin/location/view_location.php',

    // OUM LIST
    'uom' => 'views/admin/uom/oum_list.php',
    'create_uom' => 'views/admin/uom/create_uom.php',
    'view_uom' => 'views/admin/uom/view_uom.php',

    // CATEGORY LIST
    'category' => 'views/admin/category/category_list.php',
    'create_category' => 'views/admin/category/create_category.php',
    'view_category' => 'views/admin/category/view_category.php',

    // TERMS LIST
    'terms' => 'views/admin/terms/terms_list.php',
    'create_terms' => 'views/admin/terms/create_terms.php',

    // COST CENTER
    'cost_center' => 'views/admin/cost_center/cost_center_list.php',
    'create_cost_center' => 'views/admin/cost_center/create_cost_center_list.php',
    'view_cost_center' => 'views/admin/cost_center/view_cost_center_list.php',

    // PAYMENT METHOD
    'payment_method' => 'views/admin/payment_method/payment_method_list.php',
    'create_payment_method' => 'views/admin/payment_method/create_payment_method.php',

    // DISCOUNT LIST
    'discount' => 'views/admin/discount/discount_list.php',
    'create_discount' => 'views/admin/discount/create_discount.php',
    'view_discount' => 'views/admin/discount/view_discount.php',

    // INPUT VAT
    'input_vat' => 'views/admin/input_vat/input_vat_list.php',
    'create_input_vat' => 'views/admin/input_vat/create_input_vat.php',
    'view_input_vat' => 'views/admin/input_vat/view_input_vat.php',

    // SALES TAX
    'sales_tax' => 'views/admin/sales_tax/sales_tax_list.php',
    'create_sales_tax' => 'views/admin/sales_tax/create_sales_tax.php',
    'view_sales_tax' => 'views/admin/sales_tax/view_sales_tax.php',

    // WTAX RATE
    'wtax' => 'views/admin/wtax/wtax_rate_list.php',
    'create_wtax' => 'views/admin/wtax/create_wtax.php',
    'view_wtax' => 'views/admin/wtax/view_wtax.php',

    // WRITE CHECK
    'write_check' => 'views/admin/write_check/write_check_list.php',
    'create_check' => 'views/admin/write_check/create_check.php',
    'view_check' => 'views/admin/write_check/view_check.php',
    'print_check' => 'views/admin/write_check/print_check.php',
    'print_cheque' => 'views/admin/write_check/print_cheque.php',
    'print_cross_cheque' => 'views/admin/write_check/print_cross_cheque.php',
    'draft_check' => 'views/admin/write_check/draft_check.php',
    'void_check' => 'views/admin/write_check/void_check.php',

    // REPORTS
    'create_make_deposit' => 'views/admin/make_deposit/create_make_deposit.php',
    'reports' => 'views/admin/reports/reports.php',
    'purchases_by_item_details' => 'views/admin/reports/purchases_by_item_details.php',
    'inventory_valuation_detail' => 'views/admin/reports/inventory_valuation_detail.php',


    // END ADMIN ROUTE ==========================================================


    // START PURCHASING ROUTE ====================================================================
    'purchasing_purchase_request' => 'views/purchasing/purchase_request/purchasing_purchase_request_list.php',
    'purchasing_create_purchase_request' => 'views/purchasing/purchase_request/purchasing_create_purchase_request.php',
    'purchasing_view_purchase_request' => 'views/purchasing/purchase_request/purchasing_view_purchase_request.php',
    'purchasing_print_purchase_request' => 'views/purchasing/purchase_request/purchasing_print_purchase_request.php',
    'purchasing_draft_purchase_request' => 'views/purchasing/purchase_request/purchasing_draft_purchase_request.php',
    'purchasing_void_purchase_request' => 'views/purchasing/purchase_request/purchasing_void_purchase_request.php',


    'purchasing_purchase_order' => 'views/purchasing/purchase_order/purchasing_purchase_order_list.php',
    'purchasing_create_purchase_order' => 'views/purchasing/purchase_order/purchasing_create_purchase_order.php',
    'purchasing_view_purchase_order' => 'views/purchasing/purchase_order/purchasing_view_purchase_order.php',
    'purchasing_draft_purchase_order' => 'views/purchasing/purchase_order/purchasing_draft_purchase_order.php',
    'purchasing_void_purchase_order' => 'views/purchasing/purchase_order/purchasing_void_purchase_order.php',

    // END PURCHASING ROUTE ===================================================================

    // START WAAREHOUSE ROUTE ====================================================================

    // INVENTORY

    'inventory_list' => 'views/admin/inventory/inventory_list.php',

    // RECEIVE ITEMS WAREHOUSE
    'warehouse_receive_items' => 'views/warehouse/receive_items/receive_item_list.php',
    'create_receive_item_warehouse' => 'views/warehouse/receive_items/create_receive_item.php',
    'warehouse_print' => 'views/warehouse/receive_items/warehouse_print.php',
    'warehouse_view_receive_item' => 'views/warehouse/receive_items/view_receive_item.php',

    // PURCHASE REQUEST WAREHOUSE
    'warehouse_purchase_request' => 'views/warehouse/purchase_request/purchase_request_list.php',
    'create_purchase_request_warehouse' => 'views/warehouse/purchase_request/create_purchase_request.php',
    'view_purchase_request_warehouse' => 'views/warehouse/purchase_request/view_purchase_request.php',
    'print_purchase_request_warehouse' => 'views/warehouse/purchase_request/print_purchase_request.php',

    // Material Issuance
    'material_issuance' => 'views/warehouse/material_issuance/material_issuance_list.php',
    'create_material_issuance' => 'views/warehouse/material_issuance/create_material_issuance.php',
    'view_material_issuance' => 'views/warehouse/material_issuance/view_material_issuance.php',
    'print_material_issuance' => 'views/warehouse/material_issuance/print_material_issuance.php',

    // END WAREHOUSE ROUTE ====================================================================


    // MAINTENANCE
    'user_list' => 'views/admin/user/user_list.php',
    'account_types' => 'views/admin/account_types/account_types_list.php',
    'role_list' => 'views/admin/user/role_list.php',
    'create_user' => 'views/admin/user/create_user.php',
    'company_settings' => 'views/admin/company/company_settings.php',
    'main/clean_database' => 'main/Developer/clean_database.view.php',
    'main/backup_database' => 'main/Developer/backup_database.view.php',
    'logout' => 'logout.php',
    'notfound' => '404.php',

    'database' => 'qwe.php'
];

// Get the requested page from the rewritten URL
$page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'index';
$page = array_key_exists($page, $pages) ? $page : 'notfound';

$file = $pages[$page];

if (file_exists($file)) {
    include $file;
} else {
    include 'views/404.php';
}
