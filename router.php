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
    'purchase_request' => 'views/admin/purchase_request/purchase_request_list.php',
    'create_purchase_request' => 'views/admin/purchase_request/create_purchase_request.php',
    'view_purchase_request' => 'views/admin/purchase_request/view_purchase_request.php',
    'print_purchase_request' => 'views/admin/purchase_request/print_purchase_request.php',

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

    // OTHER NAME
    'other_name' => 'views/admin/other_name/other_name_list.php',
    'view_other_name_list' => 'views/admin/other_name/view_other_name_list.php',
    'create_other_name' => 'views/admin/other_name/create_other_name.php',

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

    // DEPOSIT
    'create_make_deposit' => 'views/admin/make_deposit/create_make_deposit.php',
    'reports' => 'views/admin/reports/reports.php',
    'user_list' => 'views/admin/user/user_list.php',

    // END ADMIN ROUTE ==========================================================


    // START ACCOUNTING ROUTE ==========================================================
    // SALES INVOICE
    'accounting_invoice_list' => 'views/accounting/invoice/invoice_list.php',
    'accounting_create_invoice' => 'views/accounting/invoice/create_invoice.php',
    'accounting_view_invoice' => 'views/accounting/invoice/view_invoice.php',
    'accounting_print_invoice' => 'views/accounting/invoice/print_invoice.php',
    'accounting_void_invoice_list' => 'views/accounting/invoice/void_invoice_list.php',
    'accounting_draft_invoice_list' => 'views/accounting/invoice/draft_invoice_list.php',
    // RECEIVE PAYMENT
    'accounting_receive_payment_list' => 'views/accounting/receive_payment/receive_payment_list.php',
    'accounting_customer_payment' => 'views/accounting/receive_payment/customer_payment.php',
    'accounting_official_receipt' => 'views/accounting/receive_payment/official_receipt.php',
    'accounting_view_payment' => 'views/accounting/receive_payment/view_payment.php',
    'accounting_draft_payment' => 'views/accounting/receive_payment/draft_payment.php',
    'accounting_void_payment' => 'views/accounting/receive_payment/void_payment.php',
    'accounting_print_payment' => 'views/accounting/receive_payment/print_payment.php',
    // CREDIT MEMO
    'accounting_credit_memo_list' => 'views/accounting/credit_memo/credit_memo_list.php',
    'accounting_create_credit_memo' => 'views/accounting/credit_memo/create_credit_memo.php',
    'accounting_view_credit_memo' => 'views/accounting/credit_memo/view_credit_memo.php',
    'accounting_print_credit_memo' => 'views/accounting/credit_memo/print_credit_memo.php',
    'accounting_draft_credit_memo' => 'views/accounting/credit_memo/draft_credit_memo.php',
    'accounting_void_credit_memo' => 'views/accounting/credit_memo/void_credit_memo.php',
    // SALES RETURN
    'accounting_sales_return_list' => 'views/accounting/sales_return/sales_return_list.php',
    'accounting_create_sales_return' => 'views/accounting/sales_return/create_sales_return.php',
    'accounting_print_sales_return' => 'views/accounting/sales_return/print_sales_return.php',
    'accounting_view_sales_return' => 'views/accounting/sales_return/view_sales_return.php',
    'accounting_draft_sales_return' => 'views/accounting/sales_return/draft_sales_return.php',
    'accounting_void_sales_return' => 'views/accounting/sales_return/void_sales_return.php',
    // PURCHASE ORDER
    'accounting_purchase_order_list' => 'views/accounting/purchase_order/purchase_order_list.php',
    'accounting_view_purchase_order' => 'views/accounting/purchase_order/view_purchase_order.php',
    'accounting_create_purchase_order' => 'views/accounting/purchase_order/create_purchase_order.php',
    'accounting_print_purchase_order' => 'views/accounting/purchase_order/print_purchase_order.php',
    'accounting_draft_purchase_order' => 'views/accounting/purchase_order/draft_purchase_order.php',
    'accounting_void_purchase_order' => 'views/accounting/purchase_order/void_purchase_order.php',
    // RECEIVE ITEMS
    'accounting_receive_item_list' => 'views/accounting/receive_items/receive_item_list.php',
    'accounting_create_receive_item' => 'views/accounting/receive_items/create_receive_item.php',
    'accounting_view_receive_item' => 'views/accounting/receive_items/view_receive_item.php',
    'accounting_print_receive_item' => 'views/accounting/receive_items/print_receive_item.php',
    // RECEIVE ITEMS WAREHOUSE
    'accounting_warehouse_receive_item_list' => 'views/warehouse/receive_items/receive_item_list.php',
    'accounting_create_receive_item_warehouse' => 'views/warehouse/receive_items/create_receive_item.php',
    'accounting_warehouse_print' => 'views/warehouse/receive_items/warehouse_print.php',
    'accounting_warehouse_view_receive_item' => 'views/warehouse/receive_items/view_receive_item.php',
    // PURCHASE REQUEST WAREHOUSE
    'accounting_warehouse_purchase_request_list' => 'views/warehouse/purchase_request/purchase_request_list.php',
    'accounting_create_purchase_request_warehouse' => 'views/warehouse/purchase_request/create_purchase_request.php',
    'accounting_view_purchase_request_warehouse' => 'views/warehouse/purchase_request/view_purchase_request.php',
    'accounting_print_purchase_request_warehouse' => 'views/warehouse/purchase_request/print_purchase_request.php',
    // MATERIAL ISSUANCE
    'accounting_material_issuance_list' => 'views/warehouse/material_issuance/material_issuance_list.php',
    'accounting_create_material_issuance' => 'views/warehouse/material_issuance/create_material_issuance.php',
    'accounting_view_material_issuance' => 'views/warehouse/material_issuance/view_material_issuance.php',
    'accounting_print_material_issuance' => 'views/warehouse/material_issuance/print_material_issuance.php',
    // APV
    'accounting_apv_list' => 'views/accounting/apv/apv_list.php',
    'accounting_create_apv_expense' => 'views/accounting/apv/create_apv_expense.php',
    'accounting_create_apv_item' => 'views/accounting/apv/create_apv_item.php',
    'accounting_view_apv' => 'views/accounting/apv/view_apv.php',
    'accounting_print_apv' => 'views/accounting/apv/print_apv.php',
    'accounting_draft_apv' => 'views/accounting/apv/draft_apv.php',
    'accounting_void_apv' => 'views/accounting/apv/void_apv.php',
    // ENTER BILLS 
    'accounting_enter_bills_list' => 'views/accounting/enter_bills/enter_bills_list.php',
    'accounting_create_enter_bills' => 'views/accounting/enter_bills/create_enter_bills.php',
    // PURCHASE RETURN
    'accounting_purchase_return_list' => 'views/accounting/purchase_return/purchase_return_list.php',
    // PAY BILLS
    'accounting_pay_bills_list' => 'views/accounting/pay_bills/pay_bills_list.php',
    'accounting_create_pay_bills' => 'views/accounting/pay_bills/create_pay_bills.php',
    // PURCHASE REQUEST
    'accounting_purchase_request_list' => 'views/accounting/purchase_request/purchase_request_list.php',
    'accounting_create_purchase_request' => 'views/accounting/purchase_request/create_purchase_request.php',
    'accounting_view_purchase_request' => 'views/accounting/purchase_request/view_purchase_request.php',
    'accounting_print_purchase_request' => 'views/accounting/purchase_request/print_purchase_request.php',
    // MAKE DEPOSIT
    'accounting_make_deposit_list' => 'views/accounting/make_deposit/make_deposit_list.php',
    // BANK TRANSFER
    'accounting_bank_transfer_list' => 'views/accounting/bank_transfer/bank_transfer_list.php',
    // RECONCILE
    'accounting_reconcile_list' => 'views/accounting/reconcile/reconcile_list.php',
    // DASHBOARDS
    'accounting_customer_center' => 'views/accounting/dashboards/customer_center.php',
    'accounting_vendor_center' => 'views/accounting/dashboards/vendor_center.php',
    'accounting_banking' => 'views/accounting/dashboards/banking.php',
    'accounting_purchasing' => 'views/accounting/dashboards/purchasing.php',
    'accounting_warehouse_dashboard' => 'views/accounting/dashboards/warehouse_dashboard.php',
    'accounting_accounting_dashboard' => 'views/accounting/dashboards/accounting.php',
    'accounting_masterlist' => 'views/accounting/dashboards/masterlist.php',
    // GENERAL JOURNAL
    'accounting_general_journal_list' => 'views/accounting/general_journal/general_journal_list.php',
    'accounting_create_general_journal' => 'views/accounting/general_journal/create_general_journal.php',
    'accounting_view_general_journal' => 'views/accounting/general_journal/view_general_journal.php',
    'accounting_print_general_journal' => 'views/accounting/general_journal/print_general_journal.php',
    'accounting_draft_general_journal' => 'views/accounting/general_journal/draft_general_journal.php',
    'accounting_void_general_journal' => 'views/accounting/general_journal/void_general_journal.php',
    // TRANSACTION ENTRIES
    'accounting_transaction_entries_list' => 'views/accounting/transaction_entries/transaction_entries_list.php',
    // TRIAL BALANCE
    'accounting_trial_balance_list' => 'views/accounting/trial_balance/trial_balance_list.php',
    // AUDIT TRAIL
    'accounting_audit_trail_list' => 'views/accounting/audit_trail/audit_trail_list.php',
    // PROFIT LOSS
    'accounting_profit_loss_list' => 'views/accounting/profit_loss/profit_loss_list.php',
    // BALANCE SHEET
    'accounting_balance_sheet_list' => 'views/accounting/balance_sheet/balance_sheet_list.php',
    'accounting_chart_of_accounts_list' => 'views/accounting/chart_of_accounts/chart_of_accounts_list.php',
    'accounting_create_chart_of_accounts' => 'views/accounting/chart_of_accounts/create_chart_of_accounts.php',
    'accounting_edit_chart_of_accounts' => 'views/accounting/chart_of_accounts/edit_chart_of_accounts.php',
    // VENDOR PAYMENTS
    'accounting_vendor_payment_list' => 'views/accounting/vendor_payment/vendor_payment_list.php',
    // INVENTORY ITEMS
    'accounting_inventory_item_list' => 'views/accounting/inventory_item/inventory_item_list.php',
    'accounting_create_inventory_item' => 'views/accounting/inventory_item/create_inventory_item.php',
    'accounting_edit_inventory_item' => 'views/accounting/inventory_item/edit_inventory_item.php',
    // VENDORS
    'accounting_vendor_list' => 'views/accounting/vendors/vendor_list.php',
    'accounting_create_vendor' => 'views/accounting/vendors/create_vendor.php',
    'accounting_edit_vendor' => 'views/accounting/vendors/edit_vendor.php',
    'accounting_view_vendor' => 'views/accounting/vendors/view_vendor.php',
    // CUSTOMERS
    'accounting_customer_list' => 'views/accounting/customers/customer_list.php',
    'accounting_create_customer' => 'views/accounting/customers/create_customer.php',
    'accounting_edit_customer' => 'views/accounting/customers/edit_customer.php',
    'accounting_view_customer' => 'views/accounting/customers/view_customer.php',
    // TAXES
    'accounting_tax_list' => 'views/accounting/taxes/tax_list.php',
    'accounting_create_tax' => 'views/accounting/taxes/create_tax.php',
    'accounting_edit_tax' => 'views/accounting/taxes/edit_tax.php',
    // SETTINGS
    'accounting_settings' => 'views/accounting/settings/settings.php',
    // LOGOUT
    'accounting_logout' => 'views/accounting/logout.php',

    // END ACCOUNTING ROUTE ==========================================================

    // START PURCHASING ROUTE ====================================================================

    // END PURCHASING ROUTE ====================================================================


    // START WAAREHOUSE ROUTE ====================================================================
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
    'main/clean_database' => 'main/Developer/clean_database.view.php',
    'main/backup_database' => 'main/Developer/backup_database.view.php',
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
    include 'views/404.php';
}