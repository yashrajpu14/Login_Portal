<?php
require_once __DIR__ . '/../config/Database.php';
use Config\Database;

function validateUser($email, $inputPassword)
{

    $emailOrUsername = trim($email);

    // If no '@' is present, treat as username and append domain
    if (strpos($emailOrUsername, '@') === false) {
        $email = $emailOrUsername . '@iitp.ac.in';
    } else {
        $email = $emailOrUsername;
    }
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    $db = new Database();
    $conn = $db->connect();
    $query = "SELECT created_time, password_hash FROM users WHERE email = ? AND is_active = 1 LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([$email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $storedHash = $result['password_hash'];
        $timestamp = $result['created_time'];

        // Your fixed salt string
        $salt = '25698011483056497657570148107615';

        // Reconstruct the hash using the input password and stored timestamp
        $computedHash = sha1($timestamp . $salt . $inputPassword);
        // Compare the hash
        if ($storedHash !== null && $storedHash !== null)
            return hash_equals($storedHash, $computedHash);
    }

    return false;
}
?>