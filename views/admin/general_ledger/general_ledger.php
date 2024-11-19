<?php
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('general_ledger');

$ledger = GeneralLedger::getGeneralLedger();

require 'views/templates/header.php';
require 'views/templates/sidebar.php';
?>

<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>General</strong> Ledger</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">General Ledger</li>
                </ol>
            </nav>
            <div class="card">
                <div class="card-body">
                    <style>
                        .ledger-table {
                            width: 100%;
                            border-collapse: collapse;
                            font-family: Arial, sans-serif;
                        }

                        .ledger-table th,
                        .ledger-table td {
                            border: 1px solid #ddd;
                            padding: 12px;
                            text-align: left;
                        }

                        .ledger-table th {
                            background-color: #f2f2f2;
                            font-weight: bold;
                            color: #333;
                        }

                        .ledger-table tr:nth-child(even) {
                            background-color: #f9f9f9;
                        }

                        .ledger-table tr:hover {
                            background-color: #f5f5f5;
                        }

                        .ledger-table .debit {
                            color: #28a745;
                        }

                        .ledger-table .credit {
                            color: #dc3545;
                        }

                        .ledger-table .balance {
                            font-weight: bold;
                        }

                        .no-data {
                            text-align: center;
                            padding: 20px;
                            color: #666;
                            font-style: italic;
                        }
                    </style>

                    <?php if ($ledger): ?>
                        <table class="ledger-table">
                            <thead>
                                <tr>
                                    <th>Account Code</th>
                                    <th>Account Name</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ledger as $entry): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($entry['account_code']); ?></td>
                                        <td><?php echo htmlspecialchars($entry['account_name']); ?></td>
                                        <td class="debit"><?php echo number_format($entry['debit'], 2); ?></td>
                                        <td class="credit"><?php echo number_format($entry['credit'], 2); ?></td>
                                        <td class="balance"><?php echo number_format($entry['running_balance'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-data">No data found or an error occurred.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require 'views/templates/footer.php' ?>