<?php
header('Content-Type: application/json');
ob_clean(); // Clear any output before this point

require_once __DIR__ . '/../../_init.php';

// Add location
if (post('action') === 'direct_add') {
    $location = post('location');

    try {
        // Check if the location already exists
        $existingLocation = Location::findByName($location);

        if ($existingLocation) {
            // Location already exists
            echo json_encode([
                "success" => true, 
                "location" => [
                    "id" => $existingLocation->id,
                    "location_name" => $existingLocation->name // Changed to match client-side expectation
                ], 
                "message" => "Location already exists."
            ]);
            exit;
        } else {
            // Insert new location
            $newLocationId = Location::add($location);

            // Retrieve the newly added location details
            $newLocation = Location::find($newLocationId);

            echo json_encode([
                "success" => true, 
                "location" => [
                    "id" => $newLocation->id,
                    "location_name" => $newLocation->name
                ], 
                "message" => "Location added successfully."
            ]);
            exit;
        }
    } catch (Exception $ex) {
        echo json_encode([
            "success" => false, 
            "message" => $ex->getMessage()
        ]);
        exit;
    }
}