<?php
require_once 'db_connection.php';

// Add new columns to users table for profile functionality
$alter_queries = [
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS dateOfBirth DATE NULL",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS gender VARCHAR(20) NULL",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS bloodType VARCHAR(10) NULL",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS weight VARCHAR(20) NULL",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS emergencyContact VARCHAR(100) NULL",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS emergencyPhone VARCHAR(20) NULL",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS allergies TEXT NULL",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS currentMedications TEXT NULL",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
];

echo "Updating database schema...\n";

foreach ($alter_queries as $query) {
    if (mysqli_query($conn, $query)) {
        echo "✓ Successfully executed: " . substr($query, 0, 50) . "...\n";
    } else {
        echo "✗ Error executing query: " . mysqli_error($conn) . "\n";
        echo "Query: " . $query . "\n";
    }
}

echo "\nDatabase schema update completed!\n";
mysqli_close($conn);
?>