<?php

require_once __DIR__ . '/../../_init.php';


// Delete employee
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = $_GET['id'];

    // Sanitize $id if necessary to prevent SQL injection
    // $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

    $employee = Employee::find($id);



    if ($employee && $employee->delete()) {
        flashMessage('delete_employee', 'Employee deleted successfully', FLASH_SUCCESS);
    } else {
        flashMessage('delete_employee', 'Employee deleted successfully', FLASH_SUCCESS);
    }
    redirect('../../employee_list');
}


// Add employee
if (post('action') === 'add') {
    // Retrieve data from the form
    $employee_code = post('employee_code');
    $employee_status = post('employment_status');
    $first_name = post('first_name');
    $middle_name = post('middle_name');
    $last_name = post('last_name');
    $ext_name = post('ext_name');
    $co_name = post('co_name');
    $tin = post('tin');
    $terms = post('terms');
    $house_lot_number = post('house_lot_number');
    $street = post('street');
    $barangay = post('barangay');
    $town = post('town');
    $city = post('city');
    $zip = post('zip');
    $sss = post('sss');
    $philhealth = post('philhealth');
    $pagibig = post('pagibig');

    // $data = [
    //     'employee_code' => post('employee_code'),
    //     'employee_status' => post('employment_status'),
    //     'first_name' => post('first_name'),
    //     'middle_name' => post('middle_name'),
    //     'last_name' => post('last_name'),
    //     'ext_name' => post('ext_name'),
    //     'co_name' => post('co_name'),
    //     'tin' => post('tin'),
    //     'house_lot_number' => post('house_lot_number'),
    //     'street' => post('street'),
    //     'barangay' => post('barangay'),
    //     'town' => post('town'),
    //     'city' => post('city'),
    //     'zip' => post('zip'),
    //     'sss' => post('sss'),
    //     'philhealth' => post('philhealth'),
    //     'pagibig' => post('pagibig'),
    // ];

    // dd($data);

    try {
        // Add the employee using the Employee class method
        Employee::add($employee_code, $employee_status, $first_name, $middle_name, $last_name, $ext_name, $co_name, $tin, $terms, $house_lot_number, $street, $barangay, $town, $city, $zip, $sss, $philhealth, $pagibig);
        flashMessage('add_employee', 'Employee added successfully.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add_employee', $ex->getMessage(), FLASH_ERROR);
    }

    // Redirect back to the page
    redirect('../../employee_list');
}
