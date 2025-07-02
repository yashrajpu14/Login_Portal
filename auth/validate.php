<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/jwt.php';
require_once __DIR__ . '/../helper/getEnvironmentValue.php';

use Config\JWTToken;
use Config\Database;
use Helper\EnvironmentValue;

    function validateUser($email, $inputPassword)
    {
        $emailOrUsername = trim($email);

        // If no '@' is present, treat as username and append domain
        if (strpos($emailOrUsername, '@') === false) {
            $email = $emailOrUsername . '@iitp.ac.in';
        } 
        else 
        {
            $email = $emailOrUsername;
        }
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        $db = new Database();
        $result = $db->select($email);
        
        if ($result) {
            $storedHash = $result['password_hash'];
            $timestamp = $result['created_time'];

            // Fixed salt string
            $env = new EnvironmentValue();
            $salt = $env->getSecretKey('salt');

            //Object of JWT Token 
            $jwt = new JWTToken();

            // Reconstruct the hash using the input password and stored timestamp
            $computedHash = sha1($timestamp . $salt . $inputPassword);
            // Compare the hash
            if ($storedHash !== null && $storedHash !== null && hash_equals($storedHash, $computedHash))
                return $jwt->generateJWTToken($email);
            else
                return ["success" => false,
                "message" => "Incorrect Password",
            ];
        }

        return ["success" => false,
            "message" => "Incorrect User",
        ];
    }

    function resetUser($email, $inputPassword)
    {

        $emailOrUsername = trim($email);

        // If no '@' is present, treat as username and append domain
        if (strpos($emailOrUsername, '@') === false) {
            $email = $emailOrUsername . '@iitp.ac.in';
        } else {
            $email = $emailOrUsername;
        }
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $timestamp = time();
        // Fixed salt string
        $env = new EnvironmentValue();
        $salt = $env->getSecretKey('salt');

        // Construct the hash using the input password and stored timestamp
        $computedHash = sha1($timestamp . $salt . $inputPassword);

        $db = new Database();
        $result = $db->updatePassword($email, $computedHash, $timestamp);
        
        if ($result>0) 
        {
            return ["success" => true,
                "message" => "Password updated successfully",
            ];
        }

        return ["success" => $result,
            "message" => "User is not present",
        ];
    }

?>