<?php
require_once __DIR__ . '/../_init.php';

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

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
    'coa.gl_code',
    'coa.gl_name',
    'coa.sl_code',
    'coa.sl_name',
    'coa.account_description'
];

$order_by = $columns[$order_column];

$where = '';
if (!empty($search)) {
    $where = "WHERE coa.account_code LIKE :search 
               OR at.name LIKE :search
               OR coa.gl_code LIKE :search 
               OR coa.gl_name LIKE :search 
               OR coa.sl_code LIKE :search 
               OR coa.sl_name LIKE :search 
               OR coa.account_description LIKE :search";
}

$query = "SELECT SQL_CALC_FOUND_ROWS 
            coa.id,
            coa.account_code,
            at.name AS account_type,
            coa.gl_code,
            coa.gl_name,
            coa.sl_code,
            coa.sl_name,
            coa.account_description
          FROM chart_of_account coa
          LEFT JOIN account_types at ON coa.account_type_id = at.id
          $where 
          ORDER BY $order_by $order_dir 
          LIMIT :start, :length";

$stmt = $connection->prepare($query);

if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

$stmt->bindValue(':start', (int) $start, PDO::PARAM_INT);
$stmt->bindValue(':length', (int) $length, PDO::PARAM_INT);

$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $connection->query("SELECT FOUND_ROWS()");
$total_records = $stmt->fetchColumn();

// Prepare the response
$response = [
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $total_records,
    "recordsFiltered" => $total_records,
    "data" => $results  // This should be an array of objects
];

echo json_encode($response);


