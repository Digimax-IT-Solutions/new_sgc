<?php

class ProfitAndLoss
{
    public static function displayProfitAndLoss($fromDate = null, $toDate = null)
    {
        global $connection;

        // Fetch data from database
        $currentData = self::fetchAccountData($fromDate, $toDate);

        // Initialize the $displayedAccounts array
        $displayedAccounts = [];

        // Output the Profit and Loss statement
        echo '<div class="profit-loss-statement" style="width: 700px; margin: 0 auto;">';
        echo '<h2 style="text-align: center;">DIGIMAX IT SOLUTIONS INC.</h2>';
        echo '<h3 style="text-align: center;">PROFIT AND LOSS</h3>';
        if ($fromDate && $toDate) {
            echo '<h4 style="text-align: center;">From ' . date('F d, Y', strtotime($fromDate)) . ' to ' . date('F d, Y', strtotime($toDate)) . '</h4>';
        } else {
            echo '<h4 style="text-align: center;">' . date('Y') . '</h4>';
        }
        echo '<table style="width: 100%; border-collapse: collapse;">';
        echo '<thead><tr><th></th><th style="text-align: right;">' . $toDate . '</th></tr></thead>';

        // Income section
        $totalIncome = self::displaySection($currentData, 'Income', 'Income', [], $displayedAccounts);

        // Cost of Goods Sold section
        $totalCOGS = self::displaySection($currentData, 'Cost of Goods Sold', 'Cost of Goods Sold', [], $displayedAccounts);

        // Gross Profit
        $grossProfit = $totalIncome - $totalCOGS;
        echo self::totalLine('Gross Profit', $grossProfit);

        // Expenses section
        $totalExpenses = self::displaySection($currentData, 'Expenses', 'Expenses', ['Cost of Goods Sold'], $displayedAccounts);

        // Operating Income
        $operatingIncome = $grossProfit - $totalExpenses;
        echo self::totalLine('Operating Income', $operatingIncome);

        // Other Income and Expense
        $otherIncome = self::displaySection($currentData, 'Other Income', 'Other Income', [], $displayedAccounts);
        $otherExpense = self::displaySection($currentData, 'Other Expense', 'Other Expense', [], $displayedAccounts);

        // Net Income Before Tax
        $netIncomeBeforeTax = $operatingIncome + $otherIncome - $otherExpense;
        echo self::totalLine('Net Income Before Tax', $netIncomeBeforeTax);

        // Income Tax (assuming 25% tax rate)
        $incomeTax = $netIncomeBeforeTax * 0.25;
        echo self::lineItem('Less provision for Income Tax (25%)', -$incomeTax);

        // Net Income/Loss
        $netIncomeLoss = $netIncomeBeforeTax - $incomeTax;
        echo self::totalLine('Net Income/Loss', $netIncomeLoss, true);

        echo '</table>';
        echo '</div>';

        // Return the net income
        return $netIncomeLoss;
    }

    private static function fetchAccountData($fromDate = null, $toDate = null)
    {
        global $connection;

        $query = "SELECT 
                    ca.id AS account_id,
                    ca.account_code,
                    ca.gl_name AS account_name,
                    at.name AS account_type,
                    te.balance
                FROM 
                    chart_of_account ca
                JOIN 
                    account_types at ON ca.account_type_id = at.id
                LEFT JOIN 
                    transaction_entries te ON ca.id = te.account_id
                WHERE 
                    at.name IN ('Income', 'Cost of Goods Sold', 'Expenses', 'Other Income', 'Other Expense')
                    AND te.balance IS NOT NULL";

        $params = [];

        if ($fromDate && $toDate) {
            $query .= " AND te.transaction_date BETWEEN :from_date AND :to_date";
            $params['from_date'] = $fromDate;
            $params['to_date'] = $toDate;
        }

        $query .= " GROUP BY ca.id, ca.account_code, ca.gl_name, at.name";

        $stmt = $connection->prepare($query);
        $stmt->execute($params);

        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }

    private static function displaySection($data, $accountType, $sectionTitle, $exclude = [], &$displayedAccounts = [])
    {
        echo self::sectionHeader($sectionTitle);
        $total = 0;
        foreach ($data as $account) {
            if ($account['account_type'] == $accountType && !in_array($account['account_name'], $exclude) && !in_array($account['account_name'], $displayedAccounts)) {
                $amount = $accountType == 'Income' || $accountType == 'Other Income' ? $account['balance'] : -$account['balance'];
                echo self::lineItem($account['account_name'], $amount);
                $total += $amount;
                $displayedAccounts[] = $account['account_name'];
            }
        }
        echo self::totalLine("Total $sectionTitle", $total);
        return $total;
    }

    private static function lineItem($label, $amount, $indented = false)
    {
        $indent = $indented ? 'padding-left: 20px;' : '';
        $formattedAmount = number_format(abs($amount), 2);
        $formattedAmount = $amount < 0 ? '-' . $formattedAmount : $formattedAmount;
        return "<tr>
                <td style='$indent'>$label</td>
                <td style='text-align: right;'>$formattedAmount</td>
            </tr>";
    }

    private static function totalLine($label, $amount, $bold = false)
    {
        $style = $bold ? 'font-weight: bold; border-top: 1px solid black; border-bottom: 3px double black;' : 'border-top: 1px solid black;';
        $formattedAmount = number_format(abs($amount), 2);
        $formattedAmount = $amount < 0 ? '-' . $formattedAmount : $formattedAmount;
        return "<tr>
                    <td style='$style'>$label</td>
                    <td style='text-align: right; $style'>$formattedAmount</td>
                </tr>";
    }

    private static function sectionHeader($title)
    {
        return "<tr><td colspan='2' style='font-size: 25px;'><strong>$title</strong></td></tr>";
    }
}
