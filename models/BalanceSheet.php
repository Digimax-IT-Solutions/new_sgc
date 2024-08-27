<?php
class BalanceSheet
{
    public static function displayBalanceSheet()
    {
        global $connection; // Assuming $connection is your PDO database connection

        // Query to fetch all account types and their associated accounts for Balance Sheet
        $query = "SELECT account_types.name AS account_type, chart_of_account.account_description AS account, chart_of_account.balance AS balance
                  FROM account_types
                  LEFT JOIN chart_of_account ON chart_of_account.account_type_id = account_types.id
                  WHERE account_types.name IN (
                      'Bank', 
                      'Accounts Receivable', 
                      'Other Current Assets', 
                      'Fixed Assets', 
                      'Accounts Payable', 
                      'Other Current Liabilities', 
                      'Long-term Liabilities', 
                      'Other Non-current Liabilities', 
                      'Equity'
                  )
                  ORDER BY FIELD(account_types.name, 'Assets', 'Liabilities', 'Equity'), account_types.id, chart_of_account.id";

        $statement = $connection->prepare($query);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Initialize arrays to hold data for each account type dynamically
        $assets = [];
        $liabilities = [];
        $equity = [];

        // Categorize data into respective arrays based on account types for Balance Sheet
        foreach ($results as $row) {
            switch ($row['account_type']) {
                case 'Bank':
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
        echo '<tr><td colspan="2"><strong>Equity</strong></td></tr>';
        displayAccounts($equity);
        echo '<tr>';
        echo '<td></td>';
        echo '<td style="text-align: right;"><strong>Total Equity</strong>: ' . formatAsPhilippinePeso(array_sum(array_column($equity, 'balance'))) . '</td>';
        echo '</tr>';

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
}
?>