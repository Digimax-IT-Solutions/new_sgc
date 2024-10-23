<?php

class ProfitAndLoss
{
    public static function displayProfitAndLoss($fromDate = null, $toDate = null)
    {

        // Fetch data from database
        $currentData = self::fetchAccountData($fromDate, $toDate);

        // Initialize the $displayedAccounts array
        $displayedAccounts = [];

        // Output the Profit and Loss statement
        echo '<div class="profit-loss-statement">';
        echo '<h1>LA PERLA SUGAR EXPORT CORPORATION</h1>';
        echo '<h2>Profit and Loss</h2>';
        if ($fromDate && $toDate) {
            echo '<h3>' . date('F d, Y', strtotime($fromDate)) . ' to ' . date('F d, Y', strtotime($toDate)) . '</h3>';
        } else {
            echo '<h3>Fiscal Year ' . date('Y') . '</h3>';
        }

        // Start report
        echo '<div class="report-body">';

        // Income section
        $totalIncome = self::displayReportSection($currentData, 'Income', 'Income', [], $displayedAccounts);

        // Cost of Goods Sold section
        $totalCOGS = self::displayReportSection($currentData, 'Cost of Goods Sold', 'Cost of Goods Sold', [], $displayedAccounts);

        // Gross Profit
        $grossProfit = $totalIncome - $totalCOGS;
        echo self::reportTotalItem('Gross Profit', $grossProfit);

        // Expenses section
        $totalExpenses = self::displayReportSection($currentData, 'Expenses', 'Expenses', ['Cost of Goods Sold'], $displayedAccounts);

        // Operating Income
        $operatingIncome = $grossProfit - $totalExpenses;
        echo self::reportTotalItem('Operating Income', $operatingIncome);

        // Other Income and Expense
        $otherIncome = self::displayReportSection($currentData, 'Other Income', 'Other Income', [], $displayedAccounts);
        $otherExpense = self::displayReportSection($currentData, 'Other Expense', 'Other Expense', [], $displayedAccounts);

        // Net Income Before Tax
        $netIncomeBeforeTax = $operatingIncome + $otherIncome - $otherExpense;
        echo self::reportTotalItem('Net Income Before Tax', $netIncomeBeforeTax);

        // Income Tax (assuming 25% tax rate)
        if ($netIncomeBeforeTax > 0) {
            $incomeTax = $netIncomeBeforeTax * 0.25;
        } else {
            $incomeTax = 0;
        }
        echo self::reportItem('Less provision for Income Tax (25%)', -$incomeTax);

        // Net Income/Loss
        $netIncomeLoss = $netIncomeBeforeTax - $incomeTax;
        echo self::reportTotalItem('Net Income/Loss', $netIncomeLoss, true);

        // End report
        echo '</div>';

        echo '</div>';

        // Return the net income
        return $netIncomeLoss;
    }

    public static function calculateNetIncome($fromDate = null, $toDate = null)
    {
        // Fetch data from database
        $currentData = self::fetchAccountData($fromDate, $toDate);

        // Calculate net income without displaying the statement
        $totalIncome = self::calculateSectionTotal($currentData, 'Income');
        $totalCOGS = self::calculateSectionTotal($currentData, 'Cost of Goods Sold');
        $grossProfit = $totalIncome - $totalCOGS;
        $totalExpenses = self::calculateSectionTotal($currentData, 'Expenses', ['Cost of Goods Sold']);
        $operatingIncome = $grossProfit - $totalExpenses;
        $otherIncome = self::calculateSectionTotal($currentData, 'Other Income');
        $otherExpense = self::calculateSectionTotal($currentData, 'Other Expense');
        $netIncomeBeforeTax = $operatingIncome + $otherIncome - $otherExpense;
        $incomeTax = $netIncomeBeforeTax > 0 ? $netIncomeBeforeTax * 0.25 : 0;
        $netIncomeLoss = $netIncomeBeforeTax - $incomeTax;

        return $netIncomeLoss;
    }

    private static function calculateSectionTotal($data, $accountType, $exclude = [])
    {
        $total = 0;
        foreach ($data as $account) {
            if ($account['account_type'] == $accountType && !in_array($account['account_name'], $exclude)) {
                $amount = $accountType == 'Income' || $accountType == 'Other Income' ? $account['balance'] * -1 : $account['balance'];
                $total += $amount;
            }
        }
        return $total;
    }

    private static function displayReportSection($data, $accountType, $sectionTitle, $exclude = [], &$displayedAccounts = [])
    {
        echo "<div class='report-section'>";
        echo "<h4 class='section-header'>$sectionTitle</h4>";
        $total = 0;
        foreach ($data as $account) {
            if ($account['account_type'] == $accountType && !in_array($account['account_name'], $exclude) && !in_array($account['account_name'], $displayedAccounts)) {
                $amount = $accountType == 'Income' || $accountType == 'Other Income' ? $account['balance'] * -1 : $account['balance'];
                echo self::reportItem($account['account_name'], $amount);
                $total += $amount;
                $displayedAccounts[] = $account['account_name'];
            }
        }
        echo self::reportTotalItem("Total $sectionTitle", $total);
        echo "</div>";
        return $total;
    }

    private static function reportItem($label, $amount, $indented = false)
    {
        $indentClass = $indented ? 'indented' : '';
        $formattedAmount = number_format(abs($amount), 2);
        $formattedAmount = $amount < 0 ? '-' . $formattedAmount : $formattedAmount;
        return "<div class='report-item $indentClass'>
                    <span class='item-label'>$label</span>
                    <span class='item-amount'>$formattedAmount</span>
                </div>";
    }

    private static function reportTotalItem($label, $amount, $bold = false)
    {
        $boldClass = $bold ? 'bold' : '';
        $formattedAmount = number_format(abs($amount), 2);
        $formattedAmount = $amount < 0 ? '-' . $formattedAmount : $formattedAmount;
        return "<div class='report-item total $boldClass'>
                    <span class='item-label'>$label</span>
                    <span class='item-amount'>$formattedAmount</span>
                </div>";
    }

    private static function fetchAccountData($fromDate = null, $toDate = null)
    {
        global $connection;

        $query = "SELECT 
                    ca.id AS account_id,
                    ca.account_code,
                    ca.account_description AS account_name,
                    at.name AS account_type,
                    SUM(te.balance) AS balance
                FROM 
                    chart_of_account ca
                JOIN 
                    account_types at ON ca.account_type_id = at.id
                LEFT JOIN 
                    transaction_entries te ON ca.id = te.account_id
                WHERE 
                    at.name IN ('Income', 'Cost of Goods Sold', 'Expenses', 'Other Income', 'Other Expense')
                    AND te.balance IS NOT NULL ";

        $params = [];

        if ($fromDate && $toDate) {
            $query .= " AND te.transaction_date BETWEEN :from_date AND :to_date";
            $params['from_date'] = $fromDate;
            $params['to_date'] = $toDate;
        }

        $query .= " GROUP BY ca.id, ca.account_code, at.name";

        $stmt = $connection->prepare($query);
        $stmt->execute($params);

        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }
}
