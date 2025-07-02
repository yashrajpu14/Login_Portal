<?php

namespace Config;

require_once __DIR__ . '/../vendor/autoload.php'; 
require_once __DIR__ . '/../helper/getEnvironmentValue.php';

use Helper\EnvironmentValue;
use \Firebase\JWT\JWT;

Class JWTToken{

    public function generateJWTToken($email) {
        $env = new EnvironmentValue();
    
        // Define the current time and expiration time
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;  // JWT valid for 1 hour
        
        // Get the issuer and secret from the environment or config
        $issuer = $env->getSecretKey('issuer'); 
        $secretKey = $env->getSecretKey('jwtKey'); // Ensure the secret key is correct
    
        // Create the payload
        $payload = [
            "iss" => $issuer,  
            "iat" => $issuedAt, 
            "exp" => $expirationTime, 
            "sub" => $email, 
        ];
    
        // Encode the payload to create the JWT token
        $jwtToken = JWT::encode($payload, $secretKey,'HS256');
    
        // Return the token along with additional information
        return [
            'access_token' => $jwtToken,
            'email' => $email,
            'expires_in' => $expirationTime,
            'success' => true,
        ];
    }
}

?>