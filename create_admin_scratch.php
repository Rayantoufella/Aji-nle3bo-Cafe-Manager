<?php
require 'app/models/DatabaseModel.php';
require 'app/models/AdminModel.php';

$db = (new \App\Models\DatabaseModel())->connect();
$adminModel = new \App\Models\AdminModel($db);

$username = "admin_user";
$email = "admin@tabletop.com";
$password = "admin1234";

try {
    // Check if the user already exists to prevent duplicate key errors
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "Admin user ($email) already exists!\n";
    } else {
        $adminModel->createAdmin($username, $email, $password);
        echo "Successfully created Admin account!\n";
        echo "Username: $username\n";
        echo "Email: $email\n";
        echo "Password: $password\n";
    }
} catch (PDOException $e) {
    echo "Error inserting admin: " . $e->getMessage();
}
