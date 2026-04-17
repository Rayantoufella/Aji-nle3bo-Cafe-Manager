<?php
require 'app/models/DatabaseModel.php';
$db = (new \App\Models\DatabaseModel())->connect();
try {
    $db->query("ALTER TABLE reservations ADD COLUMN user_id INT NULL AFTER phone");
    echo "Added user_id column!";
} catch (PDOException $e) {
    echo "Column likely already exists or other error: " . $e->getMessage();
}
