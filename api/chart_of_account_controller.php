<?php

// api/chart_of_account_controller


require_once __DIR__ . '/../_init.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

try {
    $draw = $_POST['draw'];
    $start = $_POST['start'];
    $length = $_POST['length'];
    $search = $_POST['search']['value'];
    $order_column = $_POST['order'][0]['column'];
    $order_dir = $_POST['order'][0]['dir'];

    $columns = [
        'coa.id',
        'coa.account_code',
        'at.name',
        'coa.account_description',
        'coa.sub_account_id',
        'fc.name',
        'fnc.name'
    ];

    $order_by = $columns[$order_column];

    $where = '';
    $params = [];
    if (!empty($search)) {
        $where = "WHERE coa.account_code LIKE :search 
                   OR at.name LIKE :search
                   OR coa.account_description LIKE :search
                   OR fc.name LIKE :search
                   OR fnc.name LIKE :search";
        $params[':search'] = "%$search%";
    }

    // Count total records
    $count_query = "SELECT COUNT(*) FROM chart_of_account coa
                    LEFT JOIN account_types at ON coa.account_type_id = at.id
                    LEFT JOIN fs_classification fc ON coa.fs_classification = fc.id
                    LEFT JOIN fs_notes_classification fnc ON coa.fs_notes_classification = fnc.id
                    $where";
    $stmt = $connection->prepare($count_query);
    $stmt->execute($params);
    $total_records = $stmt->fetchColumn();

    // Main query
    $query = "SELECT 
    coa.id,
    coa.account_code,
    at.name AS account_type,
    coa.account_description,
    coa.sub_account_id,
    fc.name AS fs_classification_name,
    fnc.name AS fs_notes_classification_name
  FROM chart_of_account coa
  LEFT JOIN account_types at ON coa.account_type_id = at.id
  LEFT JOIN fs_classification fc ON coa.fs_classification = fc.id
  LEFT JOIN fs_notes_classification fnc ON coa.fs_notes_classification = fnc.id
  $where 
  ORDER BY 
    CASE 
        WHEN at.type_order = 0 THEN 1 
        ELSE 0 
    END,
    at.type_order ASC,
    $order_by $order_dir 
  LIMIT :start, :length";

    $stmt = $connection->prepare($query);

    $stmt->bindValue(':start', (int) $start, PDO::PARAM_INT);
    $stmt->bindValue(':length', (int) $length, PDO::PARAM_INT);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }

    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare the response
    $response = [
        "draw" => intval($draw),
        "recordsTotal" => $total_records,
        "recordsFiltered" => $total_records,
        "data" => array_values($results) // Ensure data is a simple array, not an associative array
    ];

    echo json_encode($response);
} catch (Exception $e) {
    // Log the error
    error_log('Chart of Accounts Error: ' . $e->getMessage());

    // Send an error response
    echo json_encode([
        "draw" => intval($draw),
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "An error occurred while processing your request. Please try again later."
    ]);
}
