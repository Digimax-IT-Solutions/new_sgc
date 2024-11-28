<?php
// menu_config.php
require_once '_init.php';
require_once 'models/User.php';

function getAllRoles()
{
    return User::getAllRoles();
}


// Dynamically populate roles for each menu item if not defined
$menuConfig = [
    'Dashboard' => [
        'icon' => 'fas fa-tachometer-alt',
        'link' => 'dashboard',
        'roles' => getAllRoles() // Dynamically assign all roles
    ],
    'Customer Center' => [
        'icon' => 'fas fa-users',
        'toggle' => 'customer',
        'roles' => getAllRoles(),
        'submenu' => [
            'Sales Order' => [
                'icon' => 'fas fa-shopping-cart',
                'link' => '',
                'roles' => getAllRoles()
            ],
            'Cash Invoice' => [
                'icon' => 'fas fa-cash-register',
                'link' => 'or_list',
                'roles' => getAllRoles()
            ],
            'Sales Invoice' => [
                'icon' => 'fas fa-file-invoice-dollar',
                'link' => 'invoice',
                'roles' => getAllRoles()
            ],
            'Receive Payment' => [
                'icon' => 'fas fa-money-bill-wave',
                'link' => 'receive_payment',
                'roles' => getAllRoles()
            ],
            'Open Invoice' => [
                'icon' => 'fas fa-file-invoice',
                'link' => 'open_invoice',
                'roles' => getAllRoles()
            ],
            'Credit Memo' => [
                'icon' => 'fas fa-credit-card', // Updated to represent credit methods
                'link' => 'credit_memo'
            ],
            'Sales Return' => [
                'icon' => 'fas fa-undo-alt', // Updated to represent credit methods
                'link' => 'sales_return'
            ],
        ]
    ],
    'Vendor Center' => [
        'icon' => 'fas fa-truck',
        'toggle' => 'vendor',
        'roles' => getAllRoles(),
        'submenu' => [
            'Purchase Request' => [
                'icon' => 'fas fa-file-alt',
                'link' => 'purchase_request',
                'roles' => getAllRoles()
            ],
            'Purchase Order' => [
                'icon' => 'fas fa-shopping-cart',
                'link' => 'purchase_order',
                'roles' => getAllRoles()
            ],
            'Receive Items' => [
                'icon' => 'fas fa-box-open',
                'link' => 'receive_items',
                'roles' => getAllRoles()
            ],
            'Receive Item no PO' => [
                'icon' => 'fas fa-dolly-flatbed',
                'link' => 'receive_item_no_po_list',
                'roles' => getAllRoles()
            ],
            'AP Voucher' => [
                'icon' => 'fas fa-file-invoice',
                'link' => 'accounts_payable_voucher',
                'roles' => getAllRoles()
            ],
            'Purchase Return' => [
                'icon' => 'fas fa-undo',
                'link' => 'purchase_return',
                'roles' => getAllRoles()
            ],
            'Pay Bills' => [
                'icon' => 'fas fa-money-bill-wave',
                'link' => 'pay_bills',
                'roles' => getAllRoles()
            ],
        ],
    ],
    'Banking' => [
        'icon' => 'fas fa-piggy-bank',
        'toggle' => 'banking',
        'roles' => getAllRoles(),
        'submenu' => [
            'Write Check' => [
                'icon' => 'fas fa-check',
                'link' => 'write_check',
                'roles' => getAllRoles()
            ],
            'Make Deposit' => [
                'icon' => 'fas fa-cash-register',
                'link' => 'make_deposit',
                'roles' => getAllRoles()
            ],
            'Bank Transfer' => [
                'icon' => 'fas fa-exchange-alt',
                'link' => 'bank_transfer',
                'roles' => getAllRoles()
            ],
        ],
    ],
    'Accounting' => [
        'icon' => 'fas fa-calculator',
        'toggle' => 'accounting',
        'roles' => getAllRoles(),
        'submenu' => [
            'Chart of Accounts' => [
                'icon' => 'fas fa-list',
                'link' => 'chart_of_accounts_list',
                'roles' => getAllRoles()
            ],
            'General Journal' => [
                'icon' => 'fas fa-book',
                'link' => 'general_journal',
                'roles' => getAllRoles()
            ],
            'General Ledger' => [
                'icon' => 'fas fa-book-open',
                'link' => 'general_ledger',
                'roles' => getAllRoles()
            ],
            'Transaction Entries' => [
                'icon' => 'fas fa-pencil-alt',
                'link' => 'transaction_entries',
                'roles' => getAllRoles()
            ],
            'Trial Balance' => [
                'icon' => 'fas fa-balance-scale',
                'link' => 'trial_balance',
                'roles' => getAllRoles()
            ],
            'Audit Trail' => [
                'icon' => 'fas fa-search',
                'link' => 'audit_trail',
                'roles' => getAllRoles()
            ],
        ],
    ],
    'Master List' => [
        'icon' => 'fas fa-bookmark',
        'toggle' => 'masterlist',
        'roles' => getAllRoles(),
        'submenu' => [
            'Chart Of Account' => [
                'icon' => 'fas fa-list',
                'link' => 'chart_of_accounts',
                'roles' => getAllRoles()
            ],
            'Item List' => [
                'icon' => 'fas fa-box',
                'link' => 'item_list',
                'roles' => getAllRoles()
            ],
            'Customer List' => [
                'icon' => 'fas fa-users',
                'link' => 'customer',
                'roles' => getAllRoles()
            ],
            'Vendor List' => [
                'icon' => 'fas fa-truck',
                'link' => 'vendor_list',
                'roles' => getAllRoles()
            ],
            'Employee List' => [
                'icon' => 'fas fa-user-tie',
                'link' => 'employee_list',
                'roles' => getAllRoles()
            ],
            'Other Name List' => [
                'icon' => 'fas fa-user',
                'link' => 'other_name',
                'roles' => getAllRoles()
            ],
        ],
    ],
    'Other List' => [
        'icon' => 'fas fa-cogs',
        'toggle' => 'otherlist',
        'roles' => getAllRoles(),
        'submenu' => [
            // 'Fs Classification' => [
            //     'icon' => 'fas fa-sitemap', // More appropriate for classification
            //     'link' => 'fs_classification',
            //     'roles' => getAllRoles()
            // ],
            // 'Fs Notes Classification' => [
            //     'icon' => 'fas fa-tags', // Suitable for notes classification
            //     'link' => 'fs_notes_classification',
            //     'roles' => getAllRoles()
            // ],
            'Department' => [
                'icon' => 'fas fa-building', // Updated to building for departments
                'link' => 'department',
                'roles' => getAllRoles()
            ],
            'Position' => [
                'icon' => 'fas fa-user-tag', // Updated to user-tag for positions
                'link' => 'position',
                'roles' => getAllRoles()
            ],
            'Shift Schedule' => [
                'icon' => 'fas fa-clock', // Updated to clock for shift schedules
                'link' => 'shift_schedule',
                'roles' => getAllRoles()
            ],
            'Deduction' => [
                'icon' => 'fas fa-minus-circle', // Updated to minus-circle for deductions
                'link' => 'deduction',
                'roles' => getAllRoles()
            ],

            'Location' => [
                'icon' => 'fas fa-map-marker-alt', // Correct icon for location
                'link' => 'location',
                'roles' => getAllRoles()
            ],
            'Unit of Measure' => [
                'icon' => 'fas fa-balance-scale', // Represents measurement more accurately
                'link' => 'uom',
                'roles' => getAllRoles()
            ],

            'Cost Center' => [
                'icon' => 'fas fa-dollar-sign',
                'link' => 'cost_center',
                'roles' => getAllRoles()
            ],
            'Category' => [
                'icon' => 'fas fa-tags',
                'link' => 'category',
                'roles' => getAllRoles()
            ],
            'Terms' => [
                'icon' => 'fas fa-calendar-alt',
                'link' => 'terms',
                'roles' => getAllRoles()
            ],
            'Payment Method' => [
                'icon' => 'fas fa-credit-card',
                'link' => 'payment_method',
                'roles' => getAllRoles()
            ],
            'Discount' => [
                'icon' => 'fas fa-percent',
                'link' => 'discount',
                'roles' => getAllRoles()
            ],
            'Input Vat' => [
                'icon' => 'fas fa-percent',
                'link' => 'input_vat',
                'roles' => getAllRoles()
            ],
            'Sales Tax' => [
                'icon' => 'fas fa-percent',
                'link' => 'sales_tax',
                'roles' => getAllRoles()
            ],
            'WTax' => [
                'icon' => 'fas fa-percent',
                'link' => 'wtax',
                'roles' => getAllRoles()
            ],
        ],
    ],
    'Purchasing' => [
        'icon' => 'fas fa-shopping-basket',
        'toggle' => 'purchasing',
        'roles' => getAllRoles(),
        'submenu' => [
            'Purchase Request' => [
                'icon' => 'fas fa-file-alt',
                'link' => 'purchasing_purchase_request',
                'roles' => getAllRoles()
            ],
            'Purchase Order' => [
                'icon' => 'fas fa-shopping-cart',
                'link' => 'purchasing_purchase_order',
                'roles' => getAllRoles()
            ],
        ],
    ],
    'Inventory' => [
        'icon' => 'fas fa-tools',
        'toggle' => 'inventory',
        'roles' => getAllRoles(),
        'submenu' => [
            'Materials and Supplies' => [
                'icon' => 'fas fa-users-cog',
                'link' => 'inventory_list',
                'roles' => getAllRoles()
            ],
        ],
    ],
    'Warehouse' => [
        'icon' => 'fas fa-tools',
        'toggle' => 'warehouse',
        'roles' => getAllRoles(),
        'submenu' => [
            'Receive Items' => [
                'icon' => 'fas fa-users-cog',
                'link' => 'warehouse_receive_items',
                'roles' => getAllRoles()
            ],
            'Purchase Request' => [
                'icon' => 'fas fa-users-cog',
                'link' => 'warehouse_purchase_request',
                'roles' => getAllRoles()
            ],
            'Material Issuance' => [
                'icon' => 'fas fa-users-cog',
                'link' => 'material_issuance',
                'roles' => getAllRoles()
            ],
        ],
    ],
    'Inventory Report' => [
        'icon' => 'fas fa-box',
        'toggle' => 'inventory_report',
        'roles' => getAllRoles(),
        'submenu' => [
            'Inventory Valuation Summary' => [
                'icon' => 'fas fa-chart-line',
                'link' => 'warehouse_receive_items',
                'roles' => getAllRoles()
            ],
            'Inventory Valuation Detail' => [
                'icon' => 'fas fa-list',
                'link' => 'inventory_valuation_detail',
                'roles' => getAllRoles()
            ],
            'Material Issuance' => [
                'icon' => 'fas fa-box-open',
                'link' => 'material_issuance',
                'roles' => getAllRoles()
            ],
        ],
    ],
    'Attendance' => [
        'icon' => 'fas fa-user-clock', // Updated to user-clock for attendance
        'toggle' => 'attendance',
        'roles' => getAllRoles(),
        'submenu' => [
            'Attendance List' => [
                'icon' => 'fas fa-list-alt', // Updated to list-alt for a list view
                'link' => 'attendace_list',
                'roles' => getAllRoles()
            ],
            'Daily Time Record' => [
                'icon' => 'fas fa-calendar-day', // Updated to calendar-day for daily records
                'link' => 'daily_time_record',
                'roles' => getAllRoles()
            ],
        ],
    ],
    'Application' => [
        'icon' => 'fas fa-briefcase', // Updated to briefcase for applications
        'toggle' => 'application',
        'roles' => getAllRoles(),
        'submenu' => [
            'Leave List' => [
                'icon' => 'fas fa-plane-departure', // Updated to plane-departure for leave
                'link' => 'leave_list',
                'roles' => getAllRoles()
            ],
            'Overtime List' => [
                'icon' => 'fas fa-clock', // Updated to clock for overtime
                'link' => 'overtime_list',
                'roles' => getAllRoles()
            ],
            'Loan List' => [
                'icon' => 'fas fa-money-bill-wave', // Updated to money-bill-wave for loans
                'link' => 'loan_list',
                'roles' => getAllRoles()
            ],
        ],
    ],
    'Payroll' => [
        'icon' => 'fas fa-money-check-alt', // Updated to money-check-alt for payroll
        'toggle' => 'payroll',
        'roles' => getAllRoles(),
        'submenu' => [
            'Process Payroll' => [
                'icon' => 'fas fa-cogs', // Updated to cogs for processing
                'link' => 'payroll',
                'roles' => getAllRoles()
            ],
        ],
    ],
    'Reports' => [
        'icon' => 'fas fa-box',
        'toggle' => 'reports',
        'roles' => getAllRoles(),
        'submenu' => [
            'Accounting Reports' => [
                'icon' => 'fas fa-chart-line',
                'link' => 'reports',
                'roles' => getAllRoles()
            ],
            'Generate Payroll' => [
                'icon' => 'fas fa-chart-line',
                'link' => 'generate_payroll',
                'roles' => getAllRoles()
            ],
        ],
    ],
    'Maintenance' => [
        'icon' => 'fas fa-tools',
        'toggle' => 'maintenance',
        'roles' => getAllRoles(),
        'submenu' => [
            'Account Types' => [
                'icon' => 'fas fa-users-cog',
                'link' => 'account_types',
                'roles' => getAllRoles()
            ],
            'Users' => [
                'icon' => 'fas fa-users-cog',
                'link' => 'user_list',
                'roles' => getAllRoles()
            ],
            'Roles' => [
                'icon' => 'fas fa-users-cog',
                'link' => 'role_list',
                'roles' => getAllRoles()
            ],
            'Company Settings' => [
                'icon' => 'fas fa-users-cog',
                'link' => 'company_settings',
                'roles' => getAllRoles()
            ],
            'Database' => [
                'icon' => 'fas fa-users-cog',
                'link' => 'database',
                'roles' => getAllRoles()
            ]
        ],
    ],
];

return $menuConfig;
