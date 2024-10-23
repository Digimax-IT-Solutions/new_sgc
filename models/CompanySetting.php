<?php

require_once __DIR__ . '/../_init.php';

class CompanySetting
{
    private $data;
    private $db;

    public function __construct($data)
    {
        // Initialize the data property
        $this->data = $data;
        // Use the PDO connection from init.php
        global $connection;
        $this->db = $connection;
    }

    public function save()
    {
        // Check if a company record already exists
        if ($this->exists()) {
            // If it exists, update the company settings
            $this->update();
        } else {
            // If it doesn't exist, add a new company setting
            $this->add();
        }
    }

    private function exists()
    {
        // Prepare a query to check for existing company settings
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM company_settings LIMIT 1");
        $stmt->execute();
        return $stmt->fetchColumn() > 0; // Returns true if at least one record exists
    }

    private function add()
    {
        // Prepare the SQL statement for insertion
        $stmt = $this->db->prepare("INSERT INTO company_settings (company_name, logo, address, zip_code, contact, tin) VALUES (?, ?, ?, ?, ?, ?)");

        // Bind parameters
        $stmt->bindParam(1, $this->data['company_name']);
        $stmt->bindParam(2, $this->data['logo']);
        $stmt->bindParam(3, $this->data['address']);
        $stmt->bindParam(4, $this->data['zip_code']);
        $stmt->bindParam(5, $this->data['contact']);
        $stmt->bindParam(6, $this->data['tin']);

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Failed to add company settings: " . implode(", ", $stmt->errorInfo()));
        }

        // Handle logo file upload if necessary
        if ($this->data['logo']) {
            $this->uploadLogo();
        }
    }

    private function update()
    {
        // Prepare the SQL statement for updating
        $stmt = $this->db->prepare("UPDATE company_settings SET company_name = ?, logo = ?, address = ?, zip_code = ?, contact = ?, tin = ? WHERE id = (SELECT id FROM company_settings LIMIT 1)");

        // Bind parameters
        $stmt->bindParam(1, $this->data['company_name']);
        $stmt->bindParam(2, $this->data['logo']);
        $stmt->bindParam(3, $this->data['address']);
        $stmt->bindParam(4, $this->data['zip_code']);
        $stmt->bindParam(5, $this->data['contact']);
        $stmt->bindParam(6, $this->data['tin']);

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Failed to update company settings: " . implode(", ", $stmt->errorInfo()));
        }

        // Handle logo file upload if necessary
        if ($this->data['logo']) {
            $this->uploadLogo();
        }
    }

    private function uploadLogo()
    {
        // Directory where logos will be stored
        $targetDir = __DIR__ . '/../uploads/'; // Make sure this directory exists and is writable
        $targetFile = $targetDir . basename($this->data['logo']);

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES['logo']['tmp_name'], $targetFile)) {
            throw new Exception("Failed to upload logo.");
        }
    }
}
