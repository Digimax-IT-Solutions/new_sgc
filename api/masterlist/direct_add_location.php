<?php

header('Content-Type: application/json');
ob_clean(); // Clear any output before this point

require_once __DIR__ . '/../../_init.php';

// Add location
if (post('action') === 'direct_add') {
    $location = post('location'); // Changed from 'name' to 'location'

    try {
        // Check if the location already exists
        $existingLocation = Location::findByName($location);

        if ($existingLocation) {
            // Location already exists, return their details
            echo json_encode([
                "success" => true, 
                "location" => [
                    "id" => $existingLocation->id,
                    "location" => $existingLocation->name
                ], 
                "message" => "Location already exists."
            ]);
        } else {
            // Insert new location if they do not already exist
            $newLocationId = Location::add($location);

            // Retrieve the newly added location details
            $newLocation = Location::find($newLocationId);

            echo json_encode([
                "success" => true, 
                "location" => [
                    "id" => $newLocationId,
                    "location" => $location
                ], 
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