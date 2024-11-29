<?php

header('Content-Type: application/json');
ob_clean(); // Clear any output before this point

require_once __DIR__ . '/../../_init.php';

// Add location
if (post('action') === 'direct_add') {
    $name = post('name');

    try {
        // Check if the location already exists
        $existingLocation = Location::findByName($name);

        if ($existingLocation) {
            // Location already exists, return their details
            echo json_encode([
                "success" => true, 
                "location" => [
                    "id" => $existingLocation['id'],
                    "name" => $existingLocation['name']
                ], 
                "message" => "Location already exists."
            ]);
        } else {
            // Insert new location if they do not already exist
            $newLocationId = Location::add($name);

            // Retrieve the newly added location details
            $newLocation = [
                "id" => $newLocationId,
                "name" => $name
            ];

            echo json_encode([
                "success" => true, 
                "location" => $newLocation, 
                "message" => "Location added successfully."
            ]);
        }
    } catch (Exception $ex) {
        echo json_encode([
            "success" => false, 
            "message" => $ex->getMessage()
        ]);
    }
    exit;
}