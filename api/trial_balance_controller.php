<?php
require_once __DIR__ . '/../_init.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the date inputs
    $startDate = $_POST['from_date'];
    $endDate = $_POST['to_date'];


    // Prepare to call the stored procedure
    $query = "CALL GetTrialBalance(:startDate, :endDate)";

    try {
        $stmt = $connection->prepare($query);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();

        // Fetch all results
        $trialBalanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // Return the results as JSON
        echo json_encode($trialBalanceData);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>