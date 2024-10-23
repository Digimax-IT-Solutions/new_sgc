<?php


require_once __DIR__ . '/../_init.php';
class BalanceSheet
{
    public static function displayBalanceSheet()
    {
        global $connection; // Assuming $connection is your PDO database connection

        // Calculate net income
        $netIncome = ProfitAndLoss::calculateNetIncome();

        // Query to fetch all account types and their associated accounts for Balance Sheet
        $query = "SELECT 
    ca.id AS account_id,
    ca.account_code,
    ca.account_description AS account,
    at.name AS account_type,
    SUM(te.balance) AS balance
FROM 
    chart_of_account ca
JOIN 
    account_types at ON ca.account_type_id = at.id
LEFT JOIN 
    transaction_entries te ON ca.id = te.account_id
WHERE 
    at.name NOT IN ('Income', 'Cost of Goods Sold', 'Expenses', 'Other Income', 'Other Expense')
    AND te.balance IS NOT NULL
GROUP BY 
    ca.id, ca.account_code, ca.account_description, at.name";

        $statement = $connection->prepare($query);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Initialize arrays to hold data for each account type dynamically
        $assets = [];
        $liabilities = [];
        // Add net income to equity
        $equity[] = [
            'account' => 'Net Income',
            'balance' => $netIncome
        ];

        // Categorize data into respective arrays based on account types for Balance Sheet
        foreach ($results as $row) {
            switch ($row['account_type']) {
                case 'Cash and Cash Equivalents':
                case 'Accounts Receivable':
                case 'Other Current Assets':
                case 'Fixed Assets':
                    $assets[] = [
                        'account' => $row['account'],
                        'balance' => $row['balance']
                    ];
                    break;
                case 'Accounts Payable':
                case 'Other Current Liabilities':
                case 'Long-term Liabilities':
                case 'Other Non-current Liabilities':
                    $liabilities[] = [
                        'account' => $row['account'],
                        'balance' => $row['balance']
                    ];
                    break;
                case 'Equity':
                    $equity[] = [
                        'account' => $row['account'],
                        'balance' => $row['balance']
                    ];
                    break;
                default:
                    // Handle unexpected account types if needed
                    break;
            }
        }

        // Outputting the balance sheet in table format
        echo '<div class="balance-sheet" style="padding: 0 400px;">';
        echo '<table class="table">';
        echo '<thead>';
        echo '</thead>';
        echo '<tbody>';

        // Function to display accounts and their balances
        function displayAccounts($accounts)
        {
            foreach ($accounts as $account) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($account['account']) . '</td>';
                echo '<td style="text-align: right;">' . formatAsPhilippinePeso($account['balance']) . '</td>';
                echo '</tr>';
            }
        }

        // Display Assets section
        echo '<tr><td colspan="2"><strong>Assets</strong></td></tr>';
        displayAccounts($assets);
        echo '<tr>';
        echo '<td></td>';
        echo '<td style="text-align: right;"><strong>Total Assets</strong>: ' . formatAsPhilippinePeso(array_sum(array_column($assets, 'balance'))) . '</td>';
        echo '</tr>';

        // Display Liabilities section
        echo '<tr><td colspan="2"><strong>Liabilities</strong></td></tr>';
        displayAccounts($liabilities);
        echo '<tr>';
        echo '<td></td>';
        echo '<td style="text-align: right;"><strong>Total Liabilities</strong>: ' . formatAsPhilippinePeso(array_sum(array_column($liabilities, 'balance'))) . '</td>';
        echo '</tr>';

        // Display Equity section
        // When displaying the Equity section
        echo '<tr><td colspan="2"><strong>Equity</strong></td></tr>';
        displayAccounts($equity);
        $totalEquity = array_sum(array_column($equity, 'balance'));
        echo '<tr>';
        echo '<td></td>';
        echo '<td style="text-align: right;"><strong>Total Equity</strong>: ' . formatAsPhilippinePeso($totalEquity) . '</td>';
        echo '</tr>';

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
}
